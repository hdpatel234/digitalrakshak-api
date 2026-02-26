<?php

namespace App\Http\Middleware;

use App\Support\RoutePermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoutePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $route = $request->route();

        if (!$user || !$route) {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        $permission = RoutePermission::fromUriAndMethod($route->uri(), $request->method());

        if (!$user->can($permission)) {
            abort(Response::HTTP_FORBIDDEN, 'Insufficient permissions');
        }

        return $next($request);
    }
}
