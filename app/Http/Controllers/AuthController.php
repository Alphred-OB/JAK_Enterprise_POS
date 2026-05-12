<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            RateLimiter::clear($this->throttleKey($request));

            \App\Models\Activity::log('login', 'User logged in');

            return $this->redirectBasedOnRole(Auth::user());
        }

        // Log failed attempt
        RateLimiter::hit($this->throttleKey($request));

        \App\Models\Activity::log('login_failed', 'Failed login attempt', [
            'attempted_email' => $request->email,
            'reason' => 'Invalid credentials'
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    protected function ensureIsNotRateLimited(Request $request)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        \App\Models\Activity::log('login_throttled', 'Login throttled due to too many attempts', [
            'attempted_email' => $request->email,
            'wait_seconds' => $seconds
        ]);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'manager' || $user->role === 'admin') {
            return redirect()->route('manager.dashboard');
        }
        if ($user->role === 'inventory_officer') {
            return redirect()->route('manager.stock.audit');
        }
        return redirect()->route('pos.index');
    }

    public function logout(Request $request)
    {
        \App\Models\Activity::log('logout', 'User logged out');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
