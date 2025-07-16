<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckFrontendUserType
{
    public function handle($request, Closure $next, ...$types)
    {
        $user = Auth::guard('frontend')->user();

        if (!$user || !in_array($user->user_type, $types)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
