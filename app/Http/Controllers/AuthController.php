<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->landingRoute(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            \App\Models\AccessLog::create([
                'user_id' => Auth::id(),
                'email' => $credentials['email'],
                'event' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
            ]);
            log_activity('login');

            return redirect()->intended(route($this->landingRoute(Auth::user())));
        }

        \App\Models\AccessLog::create([
            'email' => $credentials['email'],
            'event' => 'failed',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->landingRoute(Auth::user()));
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role' => 'applicant',
        ]);
        $user->roles()->sync(\App\Models\Role::where('slug', 'applicant')->pluck('id'));

        Auth::login($user);
        $request->session()->regenerate();

        \App\Models\AccessLog::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'event' => 'register',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
        log_activity('register');

        return redirect()->route('join.index')->with('status', 'Account created — let’s finish your membership in a few steps.');
    }

    public function logout(Request $request)
    {
        log_activity('logout');
        \App\Models\AccessLog::create([
            'user_id' => Auth::id(),
            'email' => Auth::user()?->email,
            'event' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function landingRoute($user): string
    {
        return home_route_for($user);
    }
}
