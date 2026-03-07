<?php
namespace App\Services;

use App\Models\Batch;
use App\Models\DepartmentStock;
use App\Models\Sale;

class ReportService
{
    public function adminDashboard(): array
    {
        $totalStockValue = Batch::where('quantity_remaining', '>', 0)
            ->selectRaw('SUM(quantity_remaining * purchase_price) as v')
            ->value('v') ?? 0;

        $totalSales   = Sale::where('status', 'completed')->sum('total_amount');
        $totalProfit  = Sale::where('status', 'completed')->sum('total_profit');
        $todaySales   = Sale::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount');

        $lowStockCount   = Batch::where('quantity_remaining', '>', 0)->where('quantity_remaining', '<', 20)->count();
        $expiringCount   = Batch::where('expiry_date', '<=', now()->addDays(30))
                                ->where('expiry_date', '>', now())
                                ->where('quantity_remaining', '>', 0)->count();

        return compact('totalStockValue', 'totalSales', 'totalProfit', 'todaySales', 'lowStockCount', 'expiringCount');
    }

    public function departmentDashboard(int $deptId): array
    {
        $assignedStock      = DepartmentStock::where('department_id', $deptId)->where('quantity_remaining', '>', 0)->count();
        $todaySales         = Sale::where('department_id', $deptId)->where('status', 'completed')->whereDate('created_at', today())->sum('total_amount');
        $totalUnits         = DepartmentStock::where('department_id', $deptId)->sum('quantity_remaining');
        $lowStockItems      = DepartmentStock::where('department_id', $deptId)->where('quantity_remaining', '>', 0)->where('quantity_remaining', '<', 20)->count();

        return compact('assignedStock', 'todaySales', 'totalUnits', 'lowStockItems');
    }

    public function salesReport(string $period = 'monthly'): array
    {
        $q = Sale::where('status', 'completed');
        if ($period === 'daily')        $q->whereDate('created_at', today());
        elseif ($period === 'monthly')  $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);

        $sales   = (clone $q)->with(['department', 'soldBy'])->latest()->paginate(25);
        $summary = [
            'total_amount' => (clone $q)->sum('total_amount'),
            'total_profit' => (clone $q)->sum('total_profit'),
            'total_count'  => (clone $q)->count(),
        ];

        return compact('sales', 'summary');
    }
}
