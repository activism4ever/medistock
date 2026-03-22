<?php
namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DepartmentStock;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InsuranceScheme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $invoices = Invoice::with(['cashier', 'items.batch.medicine'])
            ->where('created_by', $user->id)
            ->latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $user  = $request->user();
        $stock = DepartmentStock::with('batch.medicine')
            ->where('department_id', $user->department_id)
            ->where('quantity_remaining', '>', 0)
            ->whereHas('batch', fn($q) => $q->where('expiry_date', '>', now()))
            ->get();

        $stockData = $stock->map(fn($s) => [
            'id'            => $s->id,
            'batch_id'      => $s->batch_id,
            'batch_number'  => $s->batch->batch_number,
            'medicine_name' => $s->batch->medicine->name,
            'price'         => (float) $s->batch->selling_price,
            'expiry_date'   => $s->batch->expiry_date->format('d M Y'),
            'qty'           => $s->quantity_remaining,
        ])->values();

        $cashiers = User::where('role', 'cashier')->where('is_active', true)->get();
        $schemes  = InsuranceScheme::where('is_active', true)->orderBy('name')->get();

        return view('invoices.create', compact('stockData', 'cashiers', 'schemes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name'        => 'required|string|max:255',
            'patient_id'          => 'nullable|string|max:100',
            'cashier_id'          => 'required|exists:users,id',
            'sale_type'           => 'required|in:normal,insurance',
            'insurance_scheme_id' => 'required_if:sale_type,insurance|nullable|exists:insurance_schemes,id',
            'sector'              => 'required_if:sale_type,insurance|nullable|in:formal,informal',
            'enrolee_name'        => 'required_if:sale_type,insurance|nullable|string|max:255',
            'enrolee_id'          => 'required_if:sale_type,insurance|nullable|string|max:100',
            'items'               => 'required|array|min:1',
            'items.*.batch_id'    => 'required|exists:batches,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'notes'               => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $user        = $request->user();
            $isInsurance = $request->sale_type === 'insurance';
            $sector      = $isInsurance ? $request->sector : null;
            $isInformal  = $sector === 'informal';
            $total       = 0;

            $lineItems = [];
            foreach ($request->items as $item) {
                $stock = DepartmentStock::with('batch')
                    ->where('batch_id', $item['batch_id'])
                    ->where('department_id', $user->department_id)
                    ->firstOrFail();

                $subtotal  = $stock->batch->selling_price * $item['quantity'];
                $total    += $subtotal;
                $lineItems[] = [
                    'batch_id'      => $item['batch_id'],
                    'quantity'      => $item['quantity'],
                    'selling_price' => $stock->batch->selling_price,
                    'subtotal'      => $subtotal,
                ];
            }

            // Calculate co-payment based on sector
            $copaymentAmount = null;
            $insuranceAmount = null;
            if ($isInsurance) {
                if ($isInformal) {
                    $copaymentAmount = 0;
                    $insuranceAmount = $total; // 100% covered
                } else {
                    $copaymentAmount = round($total * 0.10, 2); // 10%
                    $insuranceAmount = round($total * 0.90, 2); // 90%
                }
            }

            $invoice = Invoice::create([
                'invoice_number'      => $this->invoiceNumber(),
                'department_id'       => $user->department_id,
                'created_by'          => $user->id,
                'cashier_id'          => $request->cashier_id,
                'drawer_number'       => $user->drawer_number,
                'patient_name'        => $request->patient_name,
                'patient_id'          => $request->patient_id,
                'sale_type'           => $request->sale_type,
                'sector'              => $sector,
                'insurance_scheme_id' => $isInsurance ? $request->insurance_scheme_id : null,
                'enrolee_name'        => $isInsurance ? $request->enrolee_name : null,
                'enrolee_id'          => $isInsurance ? $request->enrolee_id : null,
                'total_amount'        => $total,
                'copayment_amount'    => $copaymentAmount,
                'insurance_amount'    => $insuranceAmount,
                'notes'               => $request->notes,
                'status'              => 'pending',
            ]);

            foreach ($lineItems as $line) {
                $invoice->items()->create($line);
            }
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created and sent to cashier successfully.');
    }

    public function dispense(Invoice $invoice)
    {
        if (!$invoice->isPaid()) {
            return back()->withErrors(['error' => 'Cannot dispense unpaid invoice.']);
        }

        foreach ($invoice->items as $item) {
            $stock = DepartmentStock::where('batch_id', $item->batch_id)
                ->where('department_id', $invoice->department_id)
                ->first();
            if ($stock) {
                $stock->decrement('quantity_remaining', $item->quantity);
            }
        }

        $invoice->update([
            'status'       => 'dispensed',
            'dispensed_at' => now(),
        ]);

        return back()->with('success', 'Invoice marked as dispensed.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items.batch.medicine', 'cashier', 'createdBy', 'insuranceScheme');
        return view('invoices.show', compact('invoice'));
    }

    public function mySales(Request $request)
    {
        $user = $request->user();
        $from = $request->get('from', today()->format('Y-m-d'));
        $to   = $request->get('to', today()->format('Y-m-d'));

        $q = Invoice::with(['cashier', 'insuranceScheme', 'items'])
            ->where('created_by', $user->id)
            ->whereIn('status', ['paid', 'dispensed'])
            ->whereDate('paid_at', '>=', $from)
            ->whereDate('paid_at', '<=', $to);

        $invoices = (clone $q)->latest('paid_at')->paginate(25)->withQueryString();

        $jchmaId = InsuranceScheme::where('name', 'JCHMA')->value('id');

        $summary = [
            'total_amount'          => (clone $q)->sum('total_amount'),
            'total_count'           => (clone $q)->count(),
            'normal_amount'         => (clone $q)->where('sale_type', 'normal')->sum('total_amount'),
            'normal_count'          => (clone $q)->where('sale_type', 'normal')->count(),
            'jchma_formal_amount'   => (clone $q)->where('insurance_scheme_id', $jchmaId)->where('sector', 'formal')->sum('total_amount'),
            'jchma_formal_count'    => (clone $q)->where('insurance_scheme_id', $jchmaId)->where('sector', 'formal')->count(),
            'jchma_informal_amount' => (clone $q)->where('insurance_scheme_id', $jchmaId)->where('sector', 'informal')->sum('total_amount'),
            'jchma_informal_count'  => (clone $q)->where('insurance_scheme_id', $jchmaId)->where('sector', 'informal')->count(),
            'nhia_amount'           => (clone $q)->where('sale_type', 'insurance')->where('insurance_scheme_id', '!=', $jchmaId)->sum('copayment_amount'),
            'nhia_count'            => (clone $q)->where('sale_type', 'insurance')->where('insurance_scheme_id', '!=', $jchmaId)->count(),
        ];

        return view('invoices.my-sales', compact('invoices', 'summary', 'from', 'to'));
    }

    public function mySalesPdf(Request $request)
    {
        $user = $request->user();
        $from = $request->get('from', today()->format('Y-m-d'));
        $to   = $request->get('to', today()->format('Y-m-d'));

        $invoices = Invoice::with(['cashier', 'insuranceScheme', 'items.batch.medicine'])
            ->where('created_by', $user->id)
            ->whereIn('status', ['paid', 'dispensed'])
            ->whereDate('paid_at', '>=', $from)
            ->whereDate('paid_at', '<=', $to)
            ->latest('paid_at')->get();

        $jchmaId = InsuranceScheme::where('name', 'JCHMA')->value('id');

        $summary = [
            'total_amount'          => $invoices->sum('total_amount'),
            'total_count'           => $invoices->count(),
            'normal_amount'         => $invoices->where('sale_type', 'normal')->sum('total_amount'),
            'normal_count'          => $invoices->where('sale_type', 'normal')->count(),
            'jchma_formal_amount'   => $invoices->where('insurance_scheme_id', $jchmaId)->where('sector', 'formal')->sum('total_amount'),
            'jchma_formal_count'    => $invoices->where('insurance_scheme_id', $jchmaId)->where('sector', 'formal')->count(),
            'jchma_informal_amount' => $invoices->where('insurance_scheme_id', $jchmaId)->where('sector', 'informal')->sum('total_amount'),
            'jchma_informal_count'  => $invoices->where('insurance_scheme_id', $jchmaId)->where('sector', 'informal')->count(),
            'nhia_amount'           => $invoices->where('sale_type', 'insurance')->where('insurance_scheme_id', '!=', $jchmaId)->sum('copayment_amount'),
            'nhia_count'            => $invoices->where('sale_type', 'insurance')->where('insurance_scheme_id', '!=', $jchmaId)->count(),
        ];

        $pdf = Pdf::loadView('invoices.my-sales-pdf', compact('invoices', 'summary', 'from', 'to', 'user'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("drawer-sales-{$from}-to-{$to}.pdf");
    }

    private function invoiceNumber(): string
    {
        $count = Invoice::whereDate('created_at', today())->count() + 1;
        return 'INV-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}