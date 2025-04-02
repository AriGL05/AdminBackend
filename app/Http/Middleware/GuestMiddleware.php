<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GuestMiddleware
{
    /**
     * Handle an incoming request.
     * This middleware allows guests to access only specific read-only routes
     * and redirects to login for anything else.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log for debugging
        Log::info('GuestMiddleware handling request: ' . $request->path() . ' [' . $request->method() . ']');

        if (!Auth::check()) {
            // Only allow GET/HEAD requests for public paths
            if (($request->isMethod('GET') || $request->isMethod('HEAD')) &&
                $this->isAllowedPublicRoute($request)) {
                return $next($request);
            }

            // For all other requests by guests, redirect to login
            Log::warning('Unauthenticated request redirected to login: ' . $request->path());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Please login to continue',
                    'redirect' => route('login')
                ], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Please login to continue with this action.');
        }

        return $next($request);
    }

    /**
     * Check if the request is for a publicly allowed route
     * This explicitly defines which routes guests can access
     */
    private function isAllowedPublicRoute(Request $request)
    {
        $path = $request->path();

        // First, check if it's a staff-related route (always protected)
        if ($this->isStaffRelatedRoute($request)) {
            return false;
        }

        // Check for malformed routes that should be protected
        // Such as incomplete edit routes, routes missing required IDs, etc.
        if (preg_match('#^edit(/.*)?$#', $path) ||  // Any 'edit' route with or without parameters
            preg_match('#^(films|actors|categories|customers|address)/\d+/edit$#', $path) || // Any direct entity edit route
            preg_match('#^new(film|actor|category|customer|address|staff)$#', $path)) { // Any 'new' entity route
            return false;
        }

        // List of allowed prefixes/routes for public access
        $allowedPrefixes = [
            'dashboard',
            'contact',
            'about',
            'login',
            'register',
            'password/forgot',
            'password/reset',
            '2fa'
        ];

        // Check for exact path matches or prefixes
        foreach ($allowedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        // Allow API endpoints that only retrieve data
        $allowedApiPatterns = [
            '#^films$#',                  // Basic film listing
            '#^actors$#',                 // Basic actor listing
            '#^categories$#',             // Basic category listing
            '#^languages$#',              // Basic language listing
            '#^customers$#',              // Basic customer listing
            '#^address$#',                // Basic address listing
            '#^actors/all$#',             // All actors API
            '#^categories/all$#',         // All categories API
            '#^languages/all$#',          // All languages API
            '#^api/cities$#',             // Cities API
        ];

        // Check if the path matches any of the allowed patterns
        foreach ($allowedApiPatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        // Allow basic item viewing, but not editing
        if (preg_match('#^(aboutfilm|aboutactor)/\d+$#', $path)) {
            return true;
        }

        // Basic table views
        if (preg_match('#^tablas/(peliculas|actores|categorias|customers|address)$#', $path)) {
            return true;
        }

        // Default to not allowed - any unrecognized or malformed route will require login
        return false;
    }

    /**
     * Check if the request is for a staff-related route
     */
    private function isStaffRelatedRoute(Request $request)
    {
        $path = $request->path();

        // Check if path contains staff-related keywords
        if (preg_match('/\bstaff\b/i', $path)) {
            return true;
        }

        // Check for staff-related parameters (e.g., tablas/staff)
        if ($request->route('tipo') === 'staff') {
            return true;
        }

        // For edit routes with itemType=staff
        if ($request->route('itemType') === 'staff') {
            return true;
        }

        return false;
    }
}
