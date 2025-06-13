<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

require __DIR__.'/admin.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lang/{locale}', function ($locale) {
    session(['locale' => $locale]);
    app()->setLocale($locale);
    return response()->json([
        'status' => 'success',
        'message' => "Locale changed to $locale"
    ]);
});

Route::get('/login', [AuthController::class, 'login']);
