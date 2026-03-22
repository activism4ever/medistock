<?php
namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\DepartmentStock;
use App\Models\Invoice;
use App\Models\Sale;
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

        if ($user->isHodPharmacy()) {
            return redirect()->route('hod.dashboard');
        }

        if ($user->isCashier()) {
            return redirect()->route('cashier.dashboard');
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

    public function hodPharmacy()
    {
        $pharmacy = \App\Models\Department::where('name', 'Pharmacy')->first();

        // Drawer performance stats
        $drawers = [];
        foreach ([1, 2, 3] as $drawer) {
            $drawers[$drawer] = [
                'today_sales'      => Sale::where('drawer_number', $drawer)->where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
                'month_sales'      => Sale::where('drawer_number', $drawer)->where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
                'total_count'      => Sale::where('drawer_number', $drawer)->where('status', 'completed')->whereDate('created_at', today())->count(),
                'pending_invoices' => Invoice::where('drawer_number', $drawer)->where('status', 'pending')->count(),
            ];
        }

        // Stock remaining per drawer
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
                ->get()
                ->map(function($s) {
                    return [
                        'medicine' => $s->batch->medicine->name,
                        'batch'    => $s->batch->batch_number,
                        'expiry'   => $s->batch->expiry_date->format('d M Y'),
                        'qty'      => $s->quantity_remaining,
                    ];
                });
        }

        // Low stock in pharmacy
        $lowStock = DepartmentStock::with('batch.medicine')
            ->where('department_id', $pharmacy?->id)
            ->where('quantity_remaining', '>', 0)
            ->where('quantity_remaining', '<', 20)
            ->orderBy('quantity_remaining')->get();

        // Recent allocations
        $allocations = \App\Models\Allocation::with('batch.medicine', 'allocatedBy')
            ->where('department_id', $pharmacy?->id)
            ->latest()->take(10)->get();

        $todaySales = Sale::where('status', 'completed')->whereDate('created_at', today())->whereIn('drawer_number', [1,2,3])->sum('total_amount');
        $todayCount = Sale::where('status', 'completed')->whereDate('created_at', today())->whereIn('drawer_number', [1,2,3])->count();

        return view('dashboard.hod', compact('drawers', 'drawerStock', 'lowStock', 'allocations', 'todaySales', 'todayCount'));
    }
}