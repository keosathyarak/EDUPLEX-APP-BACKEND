<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $request->expectsJson() 
                ? response()->json(['message' => 'Unauthenticated.'], 401) 
                : redirect('/login');
        }

        if ($user->role !== 'admin') {
            return $request->expectsJson() 
                ? response()->json(['message' => 'Unauthorized. Admin access only.'], 403) 
                : redirect('/')->with('error', 'Unauthorized. Admin access only.');
        }

        return $next($request);
    }
}
