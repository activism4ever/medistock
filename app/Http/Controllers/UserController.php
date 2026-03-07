<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('users.create', compact('departments'));
    }

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());
        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => "required|email|unique:users,email,{$user->id}",
            'role'          => 'required|in:admin,pharmacist,lab,theatre,ward',
            'department_id' => 'nullable|exists:departments,id',
            'is_active'     => 'boolean',
            'password'      => ['nullable', Password::min(8)->letters()->numbers()],
        ]);

        if (empty($validated['password'])) unset($validated['password']);

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }
        $user->update(['is_active' => false]);
        return redirect()->route('users.index')->with('success', 'User deactivated.');
    }
}
