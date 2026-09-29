<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = User::query()->with('roles')->latest();

        if ($q !== '') {
            $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users', 'q'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $members = Member::orderBy('name')->get();

        return view('users.create', compact('roles', 'members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,slug'],
            'member_id' => ['nullable', 'exists:members,id'],
        ]);

        $roleSlugs = $data['roles'];
        unset($data['roles']);

        $data['password'] = Hash::make($data['password']);
        // Keep legacy single-role column in sync (first role).
        $data['role'] = $roleSlugs[0] === 'administrator' ? 'admin' : $roleSlugs[0];

        $user = User::create($data);
        $roleIds = Role::whereIn('slug', $roleSlugs)->pluck('id');
        $user->roles()->sync($roleIds);

        return redirect()->route('users.index')->with('status', 'User created with '.count($roleSlugs).' role(s).');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::orderBy('name')->get();
        $members = Member::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles', 'members'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,slug'],
            'member_id' => ['nullable', 'exists:members,id'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $roleSlugs = $data['roles'];
        unset($data['roles']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['role'] = $roleSlugs[0] === 'administrator' ? 'admin' : $roleSlugs[0];

        $user->update($data);
        $user->roles()->sync(Role::whereIn('slug', $roleSlugs)->pluck('id'));

        return redirect()->route('users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'User removed.');
    }
}
