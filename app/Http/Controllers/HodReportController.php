<?php
namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Department;
use App\Models\DepartmentStock;
use App\Models\Invoice;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HodReportController extends Controller
{
    private function getPharmacy()
    {
        return Department::where('name', 'Pharmacy')->first();
    }

    public function index(Request $request)
    {
        $pharmacy = $this->getPharmacy();
        $from     = $request->get('from', today()->format('Y-m-d'));
        $to       = $request->get('to', today()->format('Y-m-d'));
        $period   = $request->get('period', 'custom');

        // Apply quick filter
        if ($period === 'today') {
            $from = $to = today()->format('Y-m-d');
        }

        // Sales per drawer
        $drawerSales = [];
        foreach ([1, 2, 3] as $drawer) {
            $q = Invoice::where('drawer_number', $drawer)
                ->whereIn('status', ['paid', 'dispensed'])
                ->whereDate('paid_at', '>=', $from)
                ->whereDate('paid_at', '<=', $to);

            $drawerSales[$drawer] = [
                'total_amount'     => (clone $q)->sum('total_amount'),
                'total_count'      => (clone $q)->count(),
                'normal_amount'    => (clone $q)->where('sale_type', 'normal')->sum('total_amount'),
                'normal_count'     => (clone $q)->where('sale_type', 'normal')->count(),
                'insurance_amount' => (clone $q)->where('sale_type', 'insurance')->sum('copayment_amount'),
                'insurance_count'  => (clone $q)->where('sale_type', 'insurance')->count(),
                'pending'          => Invoice::where('drawer_number', $drawer)->where('status', 'pending')->count(),
            ];
        }

        // Overall summary
        $summary = [
            'total_amount'     => collect($drawerSales)->sum('total_amount'),
            'total_count'      => collect($drawerSales)->sum('total_count'),
            'normal_amount'    => collect($drawerSales)->sum('normal_amount'),
            'insurance_amount' => collect($drawerSales)->sum('insurance_amount'),
        ];

        // Stock per drawer
        $drawerStock = [];
        foreach ([1, 2, 3] as $drawer) {
            $drawerStock[$drawer] = DepartmentStock::with('batch.medicine')
                ->where('department_id', $pharmacy?->id)
                ->where('quantity_remaining', '>', 0)
                ->whereHas('batch', function($q) use ($drawer, $pharmacy) {
                    $q->whereExists(function($sub) use ($drawer, $pharmacy) {
                        $sub->from('allocations')
                            ->whereColumn('allocations.batch_id', 'batches.id')
                            ->where('allocations.drawer_number', $drawer)
                            ->where('allocations.department_id', $pharmacy?->id);
                    });
                })
                ->get();
        }

        // Low stock
        $lowStock = DepartmentStock::with('batch.medicine')
            ->where('department_id', $pharmacy?->id)
            ->where('quantity_remaining', '>', 0)
            ->where('quantity_remaining', '<', 20)
            ->orderBy('quantity_remaining')->get();

        // Expiring soon (30 days)
        $expiring = DepartmentStock::with('batch.medicine')
            ->where('department_id', $pharmacy?->id)
            ->where('quantity_remaining', '>', 0)
            ->whereHas('batch', fn($q) => $q->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>', now()))
            ->get();

        return view('hod.reports', compact(
            'drawerSales', 'summary', 'drawerStock',
            'lowStock', 'expiring', 'from', 'to', 'period'
        ));
    }

    public function salesPdf(Request $request)
    {
        $from   = $request->get('from', today()->format('Y-m-d'));
        $to     = $request->get('to', today()->format('Y-m-d'));
        $user   = auth()->user();

        $drawerSales = [];
        $drawerInvoices = [];
        foreach ([1, 2, 3] as $drawer) {
            $q = Invoice::with(['createdBy', 'insuranceScheme'])
                ->where('drawer_number', $drawer)
                ->whereIn('status', ['paid', 'dispensed'])
                ->whereDate('paid_at', '>=', $from)
                ->whereDate('paid_at', '<=', $to);

            $invoices = (clone $q)->latest('paid_at')->get();
            $drawerInvoices[$drawer] = $invoices;

            $drawerSales[$drawer] = [
                'total_amount'     => $invoices->sum('total_amount'),
                'total_count'      => $invoices->count(),
                'normal_amount'    => $invoices->where('sale_type', 'normal')->sum('total_amount'),
                'normal_count'     => $invoices->where('sale_type', 'normal')->count(),
                'insurance_amount' => $invoices->where('sale_type', 'insurance')->sum('copayment_amount'),
                'insurance_count'  => $invoices->where('sale_type', 'insurance')->count(),
            ];
        }

        $summary = [
            'total_amount'     => collect($drawerSales)->sum('total_amount'),
            'total_count'      => collect($drawerSales)->sum('total_count'),
            'normal_amount'    => collect($drawerSales)->sum('normal_amount'),
            'insurance_amount' => collect($drawerSales)->sum('insurance_amount'),
        ];

        $pdf = Pdf::loadView('hod.pdf.sales', compact(
            'drawerSales', 'drawerInvoices', 'summary', 'from', 'to', 'user'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("hod-sales-report-{$from}-to-{$to}.pdf");
    }

    public function stockPdf(Request $request)
    {
        $pharmacy = $this->getPharmacy();
        $user     = auth()->user();

        $drawerStock = [];
        foreach ([1, 2, 3] as $drawer) {
            $drawerStock[$drawer] = DepartmentStock::with('batch.medicine')
                ->where('department_id', $pharmacy?->id)
                ->where('quantity_remaining', '>', 0)
                ->whereHas('batch', function($q) use ($drawer, $pharmacy) {
                    $q->whereExists(function($sub) use ($drawer, $pharmacy) {
                        $sub->from('allocations')
                            ->whereColumn('allocations.batch_id', 'batches.id')
                            ->where('allocations.drawer_number', $drawer)
                            ->where('allocations.department_id', $pharmacy?->id);
                    });
                })
                ->get();
        }

        $lowStock = DepartmentStock::with('batch.medicine')
            ->where('department_id', $pharmacy?->id)
            ->where('quantity_remaining', '>', 0)
            ->where('quantity_remaining', '<', 20)
            ->orderBy('quantity_remaining')->get();

        $expiring = DepartmentStock::with('batch.medicine')
            ->where('department_id', $pharmacy?->id)
            ->where('quantity_remaining', '>', 0)
            ->whereHas('batch', fn($q) => $q->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>', now()))
            ->get();

        $pdf = Pdf::loadView('hod.pdf.stock', compact(
            'drawerStock', 'lowStock', 'expiring', 'user'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("hod-stock-report-" . now()->format('Ymd') . ".pdf");
    }
}