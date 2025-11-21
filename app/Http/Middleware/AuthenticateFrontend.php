<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateFrontend
{
    public function handle($request, Closure $next, $guard = 'frontend')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('frontend.login'); // YOUR USER LOGIN ROUTE
        }

        return $next($request);
    }
}
