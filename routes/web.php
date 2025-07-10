<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\Auth\AuthController;

require __DIR__.'/admin.php';

Route::get('/', [HomeController::class, 'home'])->name('home');

// Frontend Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('frontend.login');
Route::post('/login', [AuthController::class, 'login'])->name('frontend.login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('frontend.logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('frontend.register');
Route::post('/register', [AuthController::class, 'register'])->name('frontend.register.submit');

// Protected Dashboards
Route::prefix('lawyer')->middleware(['auth:frontend', 'checkFrontendUserType:lawyer'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Lawyer Dashboard';
    })->name('lawyer.dashboard');
});

Route::prefix('vendor')->middleware(['auth:frontend', 'checkFrontendUserType:vendor'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Vendor Dashboard';
    })->name('vendor.dashboard');
});

Route::prefix('translator')->middleware(['auth:frontend', 'checkFrontendUserType:translator'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Translator Dashboard';
    })->name('translator.dashboard');
});

Route::prefix('user')->middleware(['auth:frontend', 'checkFrontendUserType:user'])->group(function () {
    Route::get('/dashboard', function () {
        return 'User Dashboard';
    })->name('user.dashboard');
});



// Route::get('/lang/{locale}', function ($locale) {
//     session(['locale' => $locale]);
//     app()->setLocale($locale);
//     return response()->json([
//         'status' => 'success',
//         'message' => "Locale changed to $locale"
//     ]);
// });

Route::get('/lang/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'ar', 'fr', 'fa', 'ru', 'zh'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
        // return response()->json([
        //     'status' => 'success',
        //     'message' => "Locale changed to $lang"
        // ]);
    }
    return redirect()->back();
})->name('lang.switch');
