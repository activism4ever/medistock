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
        $totalSales      = Sale::where('status', 'completed')->sum('total_amount');
        $totalProfit     = Sale::where('status', 'completed')->sum('total_profit');
        $todaySales      = Sale::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount');
        $lowStockCount   = Batch::where('quantity_remaining', '>', 0)->where('quantity_remaining', '<', 20)->count();
        $expiringCount   = Batch::where('expiry_date', '<=', now()->addDays(30))
                                ->where('expiry_date', '>', now())
                                ->where('quantity_remaining', '>', 0)->count();
        return compact('totalStockValue', 'totalSales', 'totalProfit', 'todaySales', 'lowStockCount', 'expiringCount');
    }

    public function departmentDashboard(int $deptId): array
    {
        $assignedStock  = DepartmentStock::where('department_id', $deptId)->where('quantity_remaining', '>', 0)->count();
        $todaySales     = Sale::where('department_id', $deptId)->where('status', 'completed')->whereDate('created_at', today())->sum('total_amount');
        $totalUnits     = DepartmentStock::where('department_id', $deptId)->sum('quantity_remaining');
        $lowStockItems  = DepartmentStock::where('department_id', $deptId)->where('quantity_remaining', '>', 0)->where('quantity_remaining', '<', 20)->count();
        return compact('assignedStock', 'todaySales', 'totalUnits', 'lowStockItems');
    }

    public function salesReport(string $period = 'monthly', ?string $drawer = null, ?string $saleType = null, ?int $schemeId = null): array
    {
        $q = Sale::where('status', 'completed');

        // Period filter
        if ($period === 'daily')       $q->whereDate('created_at', today());
        elseif ($period === 'monthly') $q->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year);

        // Drawer filter
        if ($drawer) $q->where('drawer_number', $drawer);

        // Sale type filter (normal / insurance)
        if ($saleType) $q->where('sale_type', $saleType);

        // Insurance scheme filter
        if ($schemeId) $q->where('insurance_scheme_id', $schemeId);

        $sales   = (clone $q)->with(['department', 'soldBy', 'insuranceScheme'])->latest()->paginate(25);

        $summary = [
            'total_amount'        => (clone $q)->sum('total_amount'),
            'total_profit'        => (clone $q)->sum('total_profit'),
            'total_count'         => (clone $q)->count(),
            'insurance_count'     => (clone $q)->where('sale_type', 'insurance')->count(),
            'insurance_amount'    => (clone $q)->where('sale_type', 'insurance')->sum('insurance_amount'),
            'copayment_collected' => (clone $q)->where('sale_type', 'insurance')->sum('copayment_amount'),
        ];

        return compact('sales', 'summary');
    }
}