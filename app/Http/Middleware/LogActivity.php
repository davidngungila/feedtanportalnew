<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && $request->user()
            && ! str_starts_with($request->path(), 'up')) {
            $route = $request->route()?->getName() ?? $request->path();
            log_activity(strtolower($request->method()).' '.$route);
        }

        return $response;
    }
}
