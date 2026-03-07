<?php
namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\ReportService;
use Illuminate\Http\Request;

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
