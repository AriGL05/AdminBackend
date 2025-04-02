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
        // Debug logging
        \Log::info('AdminMiddleware: checking authorization');

        // Check if user is authenticated
        if (!Auth::check()) {
            \Log::warning('AdminMiddleware: User not authenticated');

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Authentication required.'], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Por favor inicia sesión para acceder a esta área.');
        }

        // Check if user has admin role
        $user = Auth::user();
        \Log::info('AdminMiddleware: User ' . $user->id . ' has role_id: ' .
            ($user->rol_id ?? 'null') . ', role_id: ' . ($user->role_id ?? 'null'));

        // Check both rol_id and role_id columns
        $isAdmin = ($user->rol_id == 1 || $user->role_id == 1);

        if (!$isAdmin) {
            \Log::warning('AdminMiddleware: User ' . $user->id . ' is not an admin');

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Administrator access required.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta área.');
        }

        return $next($request);
    }
}
