<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Authenticate user with JWT
            $user = JWTAuth::parseToken()->authenticate();

            // Check if user exists and has a valid role (either 'user' or 'admin')
            if (!$user || !in_array($user->role, ['user', 'admin'])) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // If authenticated, pass the request further
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token is invalid or expired'], 401);
        }
    }
}
