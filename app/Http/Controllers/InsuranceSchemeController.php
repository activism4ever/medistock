<?php
namespace App\Http\Controllers;

use App\Models\InsuranceScheme;
use Illuminate\Http\Request;

class InsuranceSchemeController extends Controller
{
    public function index()
    {
        $schemes = InsuranceScheme::latest()->get();
        return view('insurance.index', compact('schemes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:insurance_schemes,name',
        ]);

        InsuranceScheme::create([
            'name'                 => strtoupper(trim($request->name)),
            'copayment_percentage' => 10.00,
            'is_active'            => true,
        ]);

        return back()->with('success', 'Insurance scheme added successfully.');
    }

    public function toggle(InsuranceScheme $scheme)
    {
        $scheme->update(['is_active' => !$scheme->is_active]);
        $status = $scheme->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Scheme {$scheme->name} {$status}.");
    }

    public function destroy(InsuranceScheme $scheme)
    {
        if ($scheme->sales()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete scheme with existing sales.']);
        }
        $scheme->delete();
        return back()->with('success', 'Scheme deleted.');
    }
}