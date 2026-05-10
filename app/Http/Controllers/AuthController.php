<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            \App\Models\Activity::log('login', 'User logged in');

            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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
