<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class TokenAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Accept token from cookie, Authorization header (Bearer) or query param
        $token = $request->cookie('api_token') ?? $request->bearerToken() ?? $request->get('api_token');

        if ($token) {
            $token = trim($token, '"');
            if (str_contains($token, '%')) {
                $token = urldecode($token);
            }

            $accessToken = PersonalAccessToken::findToken($token);

            if (! $accessToken || ! $accessToken->tokenable) {
                Auth::guard('web')->logout();
                return $request->expectsJson() 
                    ? response()->json(['message' => 'Invalid token'], 401) 
                    : redirect('/login')->withCookie(cookie()->forget('api_token'));
            }

            // Check token expiration
            $expiration = config('sanctum.expiration');
            if ($expiration && $accessToken->created_at->addMinutes($expiration)->isPast()) {
                $accessToken->delete();
                Auth::guard('web')->logout();
                return $request->expectsJson() 
                    ? response()->json(['message' => 'Token expired'], 401) 
                    : redirect('/login')->withCookie(cookie()->forget('api_token'));
            }

            // Log in the user to the web guard for this request
            Auth::guard('web')->login($accessToken->tokenable, false);

            return $next($request);
        }

        // No token provided — allow if web session is already authenticated
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        // Not authenticated by token or session
        return $request->expectsJson() 
            ? response()->json(['message' => 'Unauthenticated'], 401) 
            : redirect('/login');
    }
}
