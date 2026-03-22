<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function dashboard(Request $request)
{
    $user = $request->user();

    $pending = Invoice::with(['createdBy', 'items.batch.medicine', 'insuranceScheme'])
        ->where('cashier_id', $user->id)
        ->where('status', 'pending')
        ->latest()->get();

    $history = Invoice::with(['createdBy', 'insuranceScheme'])
        ->where('cashier_id', $user->id)
        ->where('status', 'paid')
        ->latest('paid_at')
        ->paginate(20);

    $todayPaid = Invoice::where('cashier_id', $user->id)
        ->where('status', 'paid')
        ->whereDate('paid_at', today())
        ->sum('total_amount');

    $todayCount = Invoice::where('cashier_id', $user->id)
        ->where('status', 'paid')
        ->whereDate('paid_at', today())
        ->count();

    $monthPaid = Invoice::where('cashier_id', $user->id)
        ->where('status', 'paid')
        ->whereMonth('paid_at', now()->month)
        ->whereYear('paid_at', now()->year)
        ->sum('total_amount');

    $monthCount = Invoice::where('cashier_id', $user->id)
        ->where('status', 'paid')
        ->whereMonth('paid_at', now()->month)
        ->whereYear('paid_at', now()->year)
        ->count();

    return view('cashier.dashboard', compact(
        'pending', 'history', 'todayPaid', 'todayCount', 'monthPaid', 'monthCount'
    ));
}

    public function collections(Request $request)
    {
        $user    = $request->user();
        $from    = $request->get('from', today()->format('Y-m-d'));
        $to      = $request->get('to', today()->format('Y-m-d'));

        $q = Invoice::with(['createdBy', 'insuranceScheme', 'items'])
            ->where('cashier_id', $user->id)
            ->where('status', 'paid')
            ->whereDate('paid_at', '>=', $from)
            ->whereDate('paid_at', '<=', $to);

        $invoices = $q->latest('paid_at')->paginate(25)->withQueryString();

        $summary = [
            'total_amount'        => (clone $q)->sum('total_amount'),
            'total_count'         => (clone $q)->count(),
            'normal_amount'       => (clone $q)->where('sale_type', 'normal')->sum('total_amount'),
            'normal_count'        => (clone $q)->where('sale_type', 'normal')->count(),
            'insurance_amount'    => (clone $q)->where('sale_type', 'insurance')->sum('copayment_amount'),
            'insurance_count'     => (clone $q)->where('sale_type', 'insurance')->count(),
        ];

        return view('cashier.collections', compact('invoices', 'summary', 'from', 'to'));
    }

    public function collectionsPdf(Request $request)
    {
        $user = $request->user();
        $from = $request->get('from', today()->format('Y-m-d'));
        $to   = $request->get('to', today()->format('Y-m-d'));

        $invoices = Invoice::with(['createdBy', 'insuranceScheme', 'items'])
            ->where('cashier_id', $user->id)
            ->where('status', 'paid')
            ->whereDate('paid_at', '>=', $from)
            ->whereDate('paid_at', '<=', $to)
            ->latest('paid_at')->get();

        $summary = [
            'total_amount'     => $invoices->sum('total_amount'),
            'total_count'      => $invoices->count(),
            'normal_amount'    => $invoices->where('sale_type', 'normal')->sum('total_amount'),
            'normal_count'     => $invoices->where('sale_type', 'normal')->count(),
            'insurance_amount' => $invoices->where('sale_type', 'insurance')->sum('copayment_amount'),
            'insurance_count'  => $invoices->where('sale_type', 'insurance')->count(),
        ];

        $pdf = Pdf::loadView('cashier.collections-pdf', compact('invoices', 'summary', 'from', 'to', 'user'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("collections-{$from}-to-{$to}.pdf");
    }

    public function pay(Request $request, Invoice $invoice)
    {
        if (!$invoice->isPending()) {
            return back()->withErrors(['error' => 'Invoice is not pending payment.']);
        }

        DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);

            $sale = Sale::create([
                'receipt_number'      => 'RCP-' . now()->format('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'department_id'       => $invoice->department_id,
                'sold_by'             => $invoice->created_by,
                'patient_name'        => $invoice->patient_name,
                'patient_id'          => $invoice->patient_id,
                'total_amount'        => $invoice->total_amount,
                'total_profit'        => $invoice->items->sum(fn($i) => ($i->selling_price - $i->batch->purchase_price) * $i->quantity),
                'status'              => 'completed',
                'drawer_number'       => $invoice->drawer_number,
                'sale_type'           => $invoice->sale_type,
                'insurance_scheme_id' => $invoice->insurance_scheme_id,
                'enrolee_name'        => $invoice->enrolee_name,
                'enrolee_id'          => $invoice->enrolee_id,
                'copayment_amount'    => $invoice->copayment_amount,
                'insurance_amount'    => $invoice->insurance_amount,
                'notes'               => $invoice->notes,
            ]);

            foreach ($invoice->items as $item) {
                $sale->items()->create([
                    'batch_id'       => $item->batch_id,
                    'quantity'       => $item->quantity,
                    'selling_price'  => $item->selling_price,
                    'purchase_price' => $item->batch->purchase_price,
                    'profit'         => ($item->selling_price - $item->batch->purchase_price) * $item->quantity,
                ]);
            }

            $invoice->update(['notes' => $invoice->notes . ' | Receipt: ' . $sale->receipt_number]);
        });

        return redirect()->route('cashier.receipt', $invoice)
            ->with('success', 'Payment confirmed successfully.');
    }

    public function receipt(Invoice $invoice)
    {
        $invoice->load('items.batch.medicine', 'createdBy', 'cashier', 'insuranceScheme');
        return view('cashier.receipt', compact('invoice'));
    }
}