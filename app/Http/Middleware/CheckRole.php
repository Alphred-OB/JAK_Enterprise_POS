<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
        }

        if (!$request->user()->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        if (!in_array($request->user()->role, $roles)) {
            // If they are a cashier trying to see manager stuff, send them to POS
            if ($request->user()->role === 'cashier') {
                return redirect()->route('pos.index')->with('error', 'Unauthorized access to management area.');
            }
            
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
