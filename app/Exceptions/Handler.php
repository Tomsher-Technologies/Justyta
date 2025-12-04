<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void {}

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('admin/*')) {
            $loginRoute = route('admin.login'); // Adjust this route name as per your setup
        } else {
            $loginRoute = route('frontend.login');
        }

        // Return JSON for all API calls
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. Please login.'
            ], 401);
        }
    
        return redirect()->guest($loginRoute);
    }
}
