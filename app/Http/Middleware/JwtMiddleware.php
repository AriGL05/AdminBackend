<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware extends BaseMiddleware
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
        try {
            // Check if token exists and is valid
            $user = JWTAuth::parseToken()->authenticate();

            // Log successful token authentication
            \Log::info('JWT auth successful for user: ' . ($user ? $user->id : 'unknown'));

        } catch (Exception $e) {
            // Log the exception for debugging
            \Log::warning('JWT auth failed: ' . $e->getMessage());

            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'error', 'message' => 'Token is invalid'], 401);
            } else if ($e instanceof TokenExpiredException) {
                // If token is expired, try to refresh it
                try {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    $user = JWTAuth::setToken($refreshed)->toUser();

                    // Pass the refreshed token to the frontend
                    return response()->json([
                        'status' => 'token_refreshed',
                        'token' => $refreshed
                    ], 200);
                } catch (Exception $e) {
                    return response()->json(['status' => 'error', 'message' => 'Token has expired and cannot be refreshed'], 401);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Authorization token not found'], 401);
            }
        }

        return $next($request);
    }
}
