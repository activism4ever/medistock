<?php
namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\DepartmentStock;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $stats = $this->reports->adminDashboard();

            $expiringBatches = Batch::with('medicine')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>', now())
                ->where('quantity_remaining', '>', 0)
                ->orderBy('expiry_date')->take(6)->get();

            $lowStockBatches = Batch::with('medicine')
                ->where('quantity_remaining', '>', 0)
                ->where('quantity_remaining', '<', 20)
                ->orderBy('quantity_remaining')->take(6)->get();

            return view('dashboard.admin', compact('stats', 'expiringBatches', 'lowStockBatches'));
        }

        $stats = $this->reports->departmentDashboard($user->department_id);

        $recentSales = $user->department->sales()
            ->with('items.batch.medicine')->latest()->take(6)->get();

        $lowStockItems = DepartmentStock::with('batch.medicine')
            ->where('department_id', $user->department_id)
            ->where('quantity_remaining', '>', 0)
            ->where('quantity_remaining', '<', 20)
            ->take(6)->get();

        return view('dashboard.department', compact('stats', 'recentSales', 'lowStockItems'));
    }
}
