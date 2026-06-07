<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withTrashed()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        return view('manager.users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['cashier' => 'Cashier', 'manager' => 'Manager', 'admin' => 'Admin', 'inventory_officer' => 'Inventory Officer'];
        return view('manager.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'role' => 'required|in:cashier,manager,admin,inventory_officer',
            'pin_code' => 'nullable|string|size:4',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'pin_code' => $request->filled('pin_code') ? $request->pin_code : null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Staff member added successfully.');
    }

    public function edit(User $user)
    {
        $roles = ['cashier' => 'Cashier', 'manager' => 'Manager', 'admin' => 'Admin', 'inventory_officer' => 'Inventory Officer'];
        return view('manager.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:cashier,manager,admin,inventory_officer',
            'pin_code' => 'nullable|string|size:4',
        ]);

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'pin_code' => $request->filled('pin_code') ? $request->pin_code : null,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()]]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Staff profile updated.');
    }

    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Prevent admin from deleting themselves
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot fire yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Staff member successfully fired and archived.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        
        return redirect()->route('admin.users.index')->with('success', 'Staff member successfully restored.');
    }
}
