<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\Auth\AuthController;
use App\Http\Controllers\Frontend\ServiceRequestController;
use App\Http\Controllers\Frontend\UserController;

require __DIR__.'/admin.php';

Route::get('/', [HomeController::class, 'home'])->name('home');

// Frontend Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('frontend.login');
Route::post('/login', [AuthController::class, 'login'])->name('frontend.login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('frontend.logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('frontend.register');
Route::post('/register', [AuthController::class, 'register'])->name('frontend.register.submit');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('frontend.forgot-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('frontend.reset-password');
Route::get('/enter-otp', [AuthController::class, 'showOtpForm'])->name('otp.enter');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
Route::get('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
Route::get('/new-password', [AuthController::class, 'newPasswordForm'])->name('new-password');
Route::post('/set-new-password', [AuthController::class, 'submitNewPassword'])->name('password.set.submit');


Route::get('success-payment', [ServiceRequestController::class, 'paymentSuccess'])->name('successPayment');
Route::get('cancel-payment', [ServiceRequestController::class, 'paymentCancel'])->name('cancelPayment');
Route::post('network-webhook', [ServiceRequestController::class, 'networkWebhook'])->name('network-webhook');

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
    Route::get('/dashboard', [HomeController::class, 'userDashboard'])->name('user.dashboard');

    Route::get('/services', [HomeController::class, 'services'])->name('user.services');
    
    // Service Requests
    Route::get('/service-request/{slug}', [ServiceRequestController::class, 'showForm'])->name('service.request.form');
    Route::post('/court-case-request', [ServiceRequestController::class, 'requestCourtCase'])->name('service.court-case-request');
    Route::post('/criminal-complaint-request', [ServiceRequestController::class, 'requestCriminalComplaint'])->name('service.criminal-complaint-request');
    Route::post('/last-will-request', [ServiceRequestController::class, 'requestLastWill'])->name('service.last-will-request');
    Route::post('/escrow-account-request', [ServiceRequestController::class, 'requestEscrowAccount'])->name('service.escrow-account-request');
    Route::post('/debts-collection-request', [ServiceRequestController::class, 'requestDebtsCollection'])->name('service.debts-collection-request');
    Route::post('/memo-writing-request', [ServiceRequestController::class, 'requestMemoWriting'])->name('service.memo-writing-request');
    Route::post('/power-of-attorney-request', [ServiceRequestController::class, 'requestPowerOfAttorney'])->name('service.power-of-attorney-request');   
    Route::post('/contract-drafting-request', [ServiceRequestController::class, 'requestContractDrafting'])->name('service.contract-drafting-request');    
    Route::post('/company-setup-request', [ServiceRequestController::class, 'requestCompanySetup'])->name('service.company-setup-request');    
    Route::post('/expert-report-request', [ServiceRequestController::class, 'requestExpertReport'])->name('service.expert-report-request');  
    Route::post('/immigration-request', [ServiceRequestController::class, 'requestImmigration'])->name('service.immigration-request'); 
    Route::post('/request-submission-request', [ServiceRequestController::class, 'requestRequestSubmission'])->name('service.request-submission-request');   
    Route::post('/legal-translation-request', [ServiceRequestController::class, 'requestLegalTranslation'])->name('service.legal-translation-request'); 
    Route::post('/annual-agreement-request', [ServiceRequestController::class, 'requestAnnualAgreement'])->name('service.annual-agreement-request');  

    // Get sub dropdowns and general links related to service requests
    Route::post('/get-request-types', [ServiceRequestController::class, 'getRequestTypes'])->name('get.request.types');
    Route::post('/get-request-titles', [ServiceRequestController::class, 'getRequestTitles'])->name('get.request.titles');
    Route::post('/get-sub-document-types', [ServiceRequestController::class, 'getSubDocumentTypes'])->name('get.sub.document.types');
    Route::post('/calculate-translation-price', [ServiceRequestController::class, 'calculateTranslationPrice'])->name('user.calculate-translation-price');
    Route::get('/ajax/annual-agreement-price', [ServiceRequestController::class, 'getAnnualAgreementPrice'])->name('ajax.getAnnualAgreementPrice');
    Route::get('/request-success/{reqid}', [ServiceRequestController::class, 'requestSuccess'])->name('user.request-success');
    Route::get('/payment-request-success/{reqid}', [ServiceRequestController::class, 'requestPaymentSuccess'])->name('user.payment-request-success');
    Route::get('/get-sub-contract-types/{id}', [ServiceRequestController::class, 'getSubContractTypes'])->name('user.sub.contract.types');
    Route::get('/get-license-activities/{id}', [ServiceRequestController::class, 'getLicenseActivities'])->name('user.license.activities');
    Route::get('/get-zones/{id}', [ServiceRequestController::class, 'getZones'])->name('user.zones');

    // Payment call back
    Route::get('/payment-callback/{order_id}', [ServiceRequestController::class, 'paymentSuccess'])->name('user.web-payment.callback');
    Route::get('/payment-cancel', [ServiceRequestController::class, 'paymentCancel'])->name('user.web-payment.cancel');

    Route::get('/report-a-problem', [UserController::class, 'reportProblem'])->name('user-report-problem');
    Route::post('/report-problem', [UserController::class, 'submitReportProblem'])->name('user.report.problem.submit');
    Route::get('/rate-us', [UserController::class, 'rateUs'])->name('user-rate-us');
    Route::post('/rating', [UserController::class, 'rateUsSave'])->name('user.rating.submit');

    Route::get('/training-request', [UserController::class, 'getTrainingFormData'])->name('user-training-request');
    Route::post('/training-request-save', [UserController::class, 'requestTraining'])->name('user-training-training-submit');

    // Manage Jobs
    Route::get('/law-firm-jobs', [UserController::class, 'jobPosts'])->name('user-lawfirm-jobs');
    Route::get('/law-firm-jobs/details/{id}', [UserController::class, 'jobPostDetails'])->name('user.job.details');
    Route::get('/law-firm-jobs/apply/{id}', [UserController::class, 'jobPostApply'])->name('user.job.details.apply');
    Route::post('/law-firm-apply-job', [UserController::class, 'applyJob'])->name('user.job.apply');
});




Route::get('/lang/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'ar', 'fr', 'fa', 'ru', 'zh'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return redirect()->back();
})->name('lang.switch');
