<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
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
