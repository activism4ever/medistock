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
        $period  = $request->get('period', 'monthly');
        $data    = $this->reports->salesReport($period);
        $sales   = $data['sales'];
        $summary = $data['summary'];
        return view('reports.index', compact('sales', 'summary', 'period'));
    }

    public function downloadPdf(Request $request)
    {
        $period = $request->get('period', 'monthly');

        $q = Sale::with(['department', 'soldBy'])->where('status', 'completed');
        if ($period === 'daily')       $q->whereDate('created_at', today());
        elseif ($period === 'monthly') $q->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year);

        $sales   = $q->latest()->get();
        $summary = [
            'total_amount' => $sales->sum('total_amount'),
            'total_profit' => $sales->sum('total_profit'),
            'total_count'  => $sales->count(),
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
    $filename = 'sales-report-' . $period . '-' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new SalesExport($period), $filename);
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