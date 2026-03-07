<?php
namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount(['users', 'sales'])->latest()->get();
        return view('departments.index', compact('departments'));
    }

    public function create() { return view('departments.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:500',
        ]);
        Department::create($data);
        return redirect()->route('departments.index')->with('success', 'Department created.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name'        => "required|string|max:255|unique:departments,name,{$department->id}",
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);
        $department->update($data);
        return redirect()->route('departments.index')->with('success', 'Department updated.');
    }
}
