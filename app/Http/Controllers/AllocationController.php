<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreAllocationRequest;
use App\Models\Allocation;
use App\Models\Batch;
use App\Models\Department;
use App\Services\AllocationService;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    public function __construct(private AllocationService $allocations) {}

    public function index()
    {
        $allocations = Allocation::with('batch.medicine', 'department', 'allocatedBy')
            ->latest()->paginate(20);
        return view('allocations.index', compact('allocations'));
    }

    public function create(Request $request)
    {
        $batches = Batch::with('medicine')
            ->where('quantity_remaining', '>', 0)
            ->where('expiry_date', '>', now())->get();
        $departments  = Department::where('is_active', true)->orderBy('name')->get();
        $selectedBatch = $request->batch_id ? Batch::with('medicine')->find($request->batch_id) : null;

        return view('allocations.create', compact('batches', 'departments', 'selectedBatch'));
    }

    public function store(StoreAllocationRequest $request)
    {
        try {
            $alloc = $this->allocations->allocate($request->validated());
            return redirect()->route('allocations.index')
                ->with('success', "Allocated {$alloc->quantity_allocated} units successfully.");
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
