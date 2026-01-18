<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  The roles required (can be multiple)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        $user = auth()->user();

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            abort(403, 'You do not have the required role to access this resource');
        }

        return $next($request);
    }
}
