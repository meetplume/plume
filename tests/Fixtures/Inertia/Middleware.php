<?php

declare(strict_types=1);

namespace Inertia;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stands in for inertiajs/inertia-laravel's middleware, which Plume does not
 * depend on: its asset version never matches Plume's, so it answers every
 * Inertia visit with a 409.
 */
class Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $request->header('X-Inertia') ? response('', 409) : $response;
    }
}
