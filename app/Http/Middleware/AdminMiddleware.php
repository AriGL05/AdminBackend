<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has rol_id = 1 (Admin)
        if (!Auth::check() || Auth::user()->rol_id != 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Administrator access required.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta área.');
        }

        return $next($request);
    }
}
