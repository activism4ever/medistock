<?php
namespace App\Http\Controllers;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Batch;
use App\Models\Sale;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

public function index(Request $request)
{
    $period   = $request->get('period', 'monthly');
    $drawer   = $request->get('drawer');
    $saleType = $request->get('sale_type');
    $schemeId = $request->get('scheme_id');

    $data    = $this->reports->salesReport($period, $drawer, $saleType, $schemeId);
    $sales   = $data['sales'];
    $summary = $data['summary'];
    $schemes = \App\Models\InsuranceScheme::where('is_active', true)->orderBy('name')->get();

    return view('reports.index', compact('sales', 'summary', 'period', 'schemes'));
}

public function downloadPdf(Request $request)
{
    $period   = $request->get('period', 'monthly');
    $drawer   = $request->get('drawer');
    $saleType = $request->get('sale_type');
    $schemeId = $request->get('scheme_id');

    $q = Sale::with(['department', 'soldBy', 'insuranceScheme'])->where('status', 'completed');
    if ($period === 'daily')       $q->whereDate('created_at', today());
    elseif ($period === 'monthly') $q->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year);
    if ($drawer)   $q->where('drawer_number', $drawer);
    if ($saleType) $q->where('sale_type', $saleType);
    if ($schemeId) $q->where('insurance_scheme_id', $schemeId);

    $sales   = $q->latest()->get();
    $summary = [
        'total_amount'        => $sales->sum('total_amount'),
        'total_profit'        => $sales->sum('total_profit'),
        'total_count'         => $sales->count(),
        'insurance_count'     => $sales->where('sale_type', 'insurance')->count(),
        'insurance_amount'    => $sales->where('sale_type', 'insurance')->sum('insurance_amount'),
        'copayment_collected' => $sales->where('sale_type', 'insurance')->sum('copayment_amount'),
    ];

    $periodLabel = match($period) {
        'daily'   => 'Today — ' . now()->format('d M Y'),
        'monthly' => now()->format('F Y'),
        default   => 'All Time',
    };

    $pdf = Pdf::loadView('reports.pdf.sales', compact('sales', 'summary', 'period', 'periodLabel'))
        ->setPaper('a4', 'landscape');

    return $pdf->download('sales-report-' . $period . '-' . now()->format('Ymd') . '.pdf');
}

public function downloadExcel(Request $request)
{
    $period   = $request->get('period', 'monthly');
    $drawer   = $request->get('drawer');
    $saleType = $request->get('sale_type');
    $schemeId = $request->get('scheme_id');
    $filename = 'sales-report-' . $period . '-' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new \App\Exports\SalesExport($period, $drawer, $saleType, $schemeId), $filename);
}
    public function stockValue()
    {
        $batches    = Batch::with('medicine')->where('quantity_remaining', '>', 0)->get();
        $totalValue = $batches->sum(fn($b) => $b->quantity_remaining * $b->purchase_price);
        return view('reports.stock-value', compact('batches', 'totalValue'));
    }

    public function expiry()
    {
        $batches = Batch::with('medicine')
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date')->get();
        return view('reports.expiry', compact('batches'));
    }

    public function lowStock()
    {
        $batches = Batch::with('medicine')
            ->where('quantity_remaining', '>', 0)
            ->where('quantity_remaining', '<', 20)
            ->orderBy('quantity_remaining')->get();
        return view('reports.low-stock', compact('batches'));
    }
}