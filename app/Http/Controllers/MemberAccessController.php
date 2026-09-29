<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberAccessController extends Controller
{
    public function provision(Request $request, Member $member)
    {
        $existing = User::where('member_id', $member->id)->first();
        if ($existing) {
            return back()->withErrors(['login' => 'This member already has a login ('.$existing->email.').']);
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6', 'max:64'],
        ]);

        $password = $data['password'] ?? Str::random(10);

        $user = User::create([
            'name' => $member->name,
            'email' => $data['email'],
            'phone' => $member->phone,
            'password' => Hash::make($password),
            'role' => 'member',
            'member_id' => $member->id,
        ]);
        $user->roles()->sync(Role::where('slug', 'member')->pluck('id'));

        if ($member->email !== $data['email']) {
            $member->update(['email' => $data['email']]);
        }

        return back()->with('status', 'Member login created.')->with('provisioned_password', $password);
    }

    public function resetPassword(Member $member)
    {
        $user = User::where('member_id', $member->id)->first();
        abort_unless($user, 404, 'No login found for this member.');

        $password = Str::random(10);
        $user->update(['password' => Hash::make($password)]);

        return back()->with('status', 'Password reset for '.$user->email.'.')->with('provisioned_password', $password);
    }

    public function destroy(Member $member)
    {
        $user = User::where('member_id', $member->id)->first();
        abort_unless($user, 404, 'No login found for this member.');

        if ($user->id === auth()->id()) {
            return back()->withErrors(['login' => 'You cannot remove your own login.']);
        }

        $user->delete();

        return back()->with('status', 'Member login removed.');
    }
}
