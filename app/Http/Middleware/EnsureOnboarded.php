<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && function_exists('is_applicant_incomplete') && is_applicant_incomplete($user)) {
            $route = $request->route()?->getName() ?? '';
            $allowed = str_starts_with($route, 'join.')
                || in_array($route, ['logout', 'account.index', 'account.update', 'account.security', 'account.security.update'], true);

            if (! $allowed) {
                return redirect()->route('join.index');
            }
        }

        return $next($request);
    }
}
