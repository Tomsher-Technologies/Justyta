<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;

Route::group(['prefix' => 'v1'], function () {
    Route::middleware('set_api_locale')->group(function () {
        
        Route::get('/test', function () {
            return 'test';
        });

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/user-register', [AuthController::class, 'register']);
        Route::post('/forget-password', [AuthController::class, 'forgetRequest']);
        Route::post('/resend-email-otp', [AuthController::class, 'forgetRequest']);
        Route::post('/verify-email-otp', [AuthController::class, 'verifyOTP']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);


        Route::middleware(['ensureFrontendRequestsAreStateful', 'auth:sanctum'])->group(function () {
            // Route::get('/services', [ServiceController::class, 'index']);
            Route::post('/logout', [AuthController::class, 'logout']);

            Route::get('/home', [HomeController::class, 'home']);
            Route::get('/lawfirm-services', [HomeController::class, 'lawfirmServices']);
            

            Route::get('/user', fn(\Illuminate\Http\Request $request) => $request->user());
        });

        
    });

    Route::fallback(function () {
        return response()->json([
            'status' => false,
            'message' => 'API route not found.',
        ], 404);
    });
});

Route::fallback(function () {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});

