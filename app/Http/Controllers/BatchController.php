<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\Batch;
use App\Models\Medicine;
use App\Services\BatchService;

class BatchController extends Controller
{
    public function __construct(private BatchService $batches) {}

    public function index()
    {
        $batches = Batch::with('medicine', 'creator')->latest()->paginate(20);
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();
        return view('batches.create', compact('medicines'));
    }

    public function store(StoreBatchRequest $request)
    {
        $batch = $this->batches->create($request->validated());
        return redirect()->route('batches.show', $batch)->with('success', "Batch #{$batch->batch_number} created.");
    }

    public function show(Batch $batch)
    {
        $batch->load('medicine', 'creator', 'allocations.department', 'allocations.allocatedBy');
        return view('batches.show', compact('batch'));
    }

    public function edit(Batch $batch) { return view('batches.edit', compact('batch')); }

    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        $this->batches->update($batch, $request->validated());
        return redirect()->route('batches.show', $batch)->with('success', 'Batch updated.');
    }
}
