<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('roles', 'member');
        $activity = \App\Models\ActivityLog::where('user_id', $user->id)->latest()->limit(8)->get();
        $lastLogin = \App\Models\AccessLog::where('user_id', $user->id)->where('event', 'login')->latest()->first();
        $memberBalance = $user->member ? member_balance($user->member) : null;

        return view('account.index', compact('user', 'activity', 'lastLogin', 'memberBalance'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($data);

        return redirect()->route('account.index')->with('status', 'Account updated.');
    }

    public function security()
    {
        $user = auth()->user();
        $access = \App\Models\AccessLog::where('user_id', $user->id)->latest()->limit(10)->get();

        return view('account.security', compact('user', 'access'));
    }

    public function updateSecurity(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('account.security')->with('status', 'Password changed.');
    }
}
