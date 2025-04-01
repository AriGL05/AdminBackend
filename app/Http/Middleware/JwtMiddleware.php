<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip JWT auth for web routes
        if ($request->route()->named([
            'login', 'register', '2fa.show', '2fa.verify', '2fa.resend', 'logout'
        ]) || $request->is('login', 'register', '2fa*')) {
            return $next($request);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 'Token is Invalid'], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 'Token is Expired'], 401);
        } catch (JWTException $e) {
            // If request is to a web route, continue without token validation
            if ($request->expectsHtml()) {
                return $next($request);
            }
            return response()->json(['status' => 'Authorization Token not found'], 401);
        }

        return $next($request);
    }
}
