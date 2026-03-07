<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\DepartmentStock;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(private SaleService $sales) {}

    public function index(Request $request)
    {
        $user  = $request->user();
        $sales = Sale::with('soldBy', 'department')
            ->when(!$user->isAdmin(), fn($q) => $q->where('department_id', $user->department_id))
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()->paginate(20);

        return view('sales.index', compact('sales'));
    }

    public function create(Request $request)
    {
        $user  = $request->user();
        $stock = DepartmentStock::with('batch.medicine')
            ->where('department_id', $user->department_id)
            ->where('quantity_remaining', '>', 0)
            ->whereHas('batch', fn($q) => $q->where('expiry_date', '>', now()))
            ->get();

        return view('sales.create', compact('stock'));
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            $sale = $this->sales->process(
                $request->only(['patient_name', 'patient_id', 'notes']),
                $request->input('items', [])
            );
            return redirect()->route('sales.receipt', $sale)
                ->with('success', "Sale #{$sale->receipt_number} completed.");
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Sale $sale)
    {
        $this->guard($sale);
        $sale->load('items.batch.medicine', 'department', 'soldBy');
        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $this->guard($sale);
        $sale->load('items.batch.medicine', 'department', 'soldBy');
        return view('sales.receipt', compact('sale'));
    }

    private function guard(Sale $sale): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $sale->department_id !== $user->department_id) {
            abort(403);
        }
    }
}
