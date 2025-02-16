<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->hasPermissionTo($permission)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Vous n\'avez pas la permission d\'accéder à cette ressource.');
        }

        return $next($request);
    }
}
