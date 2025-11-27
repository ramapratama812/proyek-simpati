<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Check user role(s). Usage: ->middleware('role:admin') or 'role:admin|dosen'
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }
        $allowed = array_map('trim', explode('|', $roles));
        if (!in_array(strtolower($user->role ?? ''), array_map('strtolower', $allowed), true)) {
            abort(403, 'You do not have permission to access this resource.');
        }
        return $next($request);
    }
}
