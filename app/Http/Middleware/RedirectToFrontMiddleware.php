<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;

class RedirectToFrontMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(auth()->check() && $request->url() !== Filament::getLogoutUrl()) {
            return redirect()->intended('/');
        }
        return $next($request);
    }
}
