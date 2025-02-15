<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles  One or more roles allowed to access the route.
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Get the currently authenticated user (assuming JWT authentication is already set up).
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // Admin can do everything.
        if ($user->role === 'admin') {
            return $next($request);
        }
        
        // If no roles were provided, or the user's role is not in the allowed list, return a forbidden response.
        if (empty($roles) || !in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        return $next($request);
    }
}
