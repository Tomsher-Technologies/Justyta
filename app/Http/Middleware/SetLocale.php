<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        elseif (Auth::guard('frontend')->check() && Auth::guard('frontend')->user()->language) {
            $locale = Auth::guard('frontend')->user()->language;
            App::setLocale($locale);
            Session::put('locale', $locale); 
        }
        else {
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}