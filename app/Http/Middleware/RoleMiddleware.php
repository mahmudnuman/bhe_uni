<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if role parameter is provided and matches the user's role
        if ($role && Auth::user()->role !== $role) {
            return response()->json(['error' => 'Forbidden - Access Denied'], 403);
        }

        return $next($request);
    }
}
