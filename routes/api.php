<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;

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
        Route::get('/search', [HomeController::class, 'search']);

        //News
        Route::get('/news', [HomeController::class, 'news']);
        Route::get('/news-details', [HomeController::class, 'newsDetails']);

        // User Account
        Route::get('/user-account', [UserController::class, 'account']);
        Route::post('/edit-profile', [UserController::class, 'editProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::delete('/delete-account', [UserController::class, 'deleteAccount']);

        //Contact US
        Route::post('/contact-us', [HomeController::class, 'contactUs']);

        // Job Posts
        Route::get('/job-posts', [JobPostController::class, 'index']);

        // Get Service Form Contents
        Route::get('/court-case-submission', [ServiceController::class, 'getCourtCaseFormData']);
        Route::get('/criminal-complaint-submission', [ServiceController::class, 'getCriminalComplaintFormData']);
        Route::get('/power-of-attorney', [ServiceController::class, 'getPowerOfAttorneyFormData']);
        Route::get('/last-will', [ServiceController::class, 'getLastWillFormData']);
        Route::get('/memo-writing', [ServiceController::class, 'getMemoWritingFormData']);
        Route::get('/expert-reports', [ServiceController::class, 'getExpertReportsFormData']);
        Route::get('/contracts-drafting', [ServiceController::class, 'getContractsDraftingFormData']);
        Route::get('/sub-contract-types', [ServiceController::class, 'getSubContractTypes']);
        Route::get('/escrow-accounts', [ServiceController::class, 'getEscrowAccountsFormData']);
        Route::get('/debts-collection', [ServiceController::class, 'getDebtsCollectionFormData']);
        Route::get('/company-setup', [ServiceController::class, 'getCompanySetupFormData']);
        Route::get('/zones', [ServiceController::class, 'getZones']);
        Route::get('/license-activities', [ServiceController::class, 'getLicenseActivities']);

        // Service Request Submission

    });

    
});

Route::fallback(function () {
    return response()->json([
        'status' => false,
        'message' => 'Route not found.',
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

