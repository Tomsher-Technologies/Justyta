<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Frontend\HomeController;

require __DIR__.'/admin.php';

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/lang/{locale}', function ($locale) {
    session(['locale' => $locale]);
    app()->setLocale($locale);
    return response()->json([
        'status' => 'success',
        'message' => "Locale changed to $locale"
    ]);
});

Route::get('/login', [AuthController::class, 'login']);
