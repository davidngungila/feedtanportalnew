<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/', 'unique:roles,slug'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Role::create($data);

        return redirect()->route('roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $role->update($data);

        return redirect()->route('roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'administrator') {
            return back()->withErrors(['role' => 'The administrator role cannot be deleted.']);
        }
        if ($role->users()->where('users.id', auth()->id())->exists() && $role->users()->count() === 1) {
            return back()->withErrors(['role' => 'You cannot delete your own only role.']);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('status', 'Role removed.');
    }
}
