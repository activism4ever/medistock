<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicineRequest;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $medicines = Medicine::withSum('batches', 'quantity_remaining')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()->paginate(20);

        return view('medicines.index', compact('medicines'));
    }

    public function create() { return view('medicines.create'); }

    public function store(StoreMedicineRequest $request)
    {
        Medicine::create($request->validated());
        return redirect()->route('medicines.index')->with('success', 'Medicine added successfully.');
    }

    public function show(Medicine $medicine)
    {
        $batches = $medicine->batches()->latest()->paginate(10);
        return view('medicines.show', compact('medicine', 'batches'));
    }

    public function edit(Medicine $medicine) { return view('medicines.edit', compact('medicine')); }

    public function update(StoreMedicineRequest $request, Medicine $medicine)
    {
        $medicine->update($request->validated());
        return redirect()->route('medicines.index')->with('success', 'Medicine updated.');
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->batches()->where('quantity_remaining', '>', 0)->exists()) {
            return back()->withErrors(['error' => 'Cannot delete medicine with active stock.']);
        }
        $medicine->delete();
        return redirect()->route('medicines.index')->with('success', 'Medicine deleted.');
    }
}
