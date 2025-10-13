<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\JobPostController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\LawyerController;

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
    Route::get('/page-contents', [HomeController::class, 'pageContents']);
    Route::get('/banners', [HomeController::class, 'getBanners']);
    Route::post('/zoom/webhook', [HomeController::class, 'handleZoomWebhook']);


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
        Route::post('/change-language', [UserController::class, 'changeLanguage']);
        Route::post('/report-problem', [UserController::class, 'reportProblem']);
        Route::get('/report-problem-content', [UserController::class, 'getReportProblemFormData']);
        Route::post('/rate-us', [UserController::class, 'rateUs']);
        Route::post('/user/online-status', [UserController::class, 'updateOnlineStatus']);

        //Lawyer Account APIs
        Route::get('/lawyer/dashboard', [LawyerController::class, 'dashboard']);
        Route::get('/lawyer/consultations', [LawyerController::class, 'assignedConsultations']);
        Route::get('/lawyer/account', [LawyerController::class, 'accountDetails']);

        //Contact US
        Route::post('/contact-us', [HomeController::class, 'contactUs']);

        // Job Posts
        Route::get('/job-posts', [JobPostController::class, 'index']); 
        Route::get('/job-posts/{id}', [JobPostController::class, 'jobDetails']);
        Route::get('/apply-job/{id}', [JobPostController::class, 'applyJobFormData']);
        Route::post('/job-posts/apply', [JobPostController::class, 'applyJob']); 

        // Common Datas
        Route::get('/emirates', [CommonController::class, 'getEmirates']);
        Route::get('/case-types', [CommonController::class, 'getCaseTypes']);

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
        Route::get('/online-consultation', [ServiceController::class, 'getOnlineConsultationFormData']);
        Route::get('/request-submission', [ServiceController::class, 'getRequestSubmissionFormData']);
        Route::get('/request-types', [ServiceController::class, 'getRequestTypes']);
        Route::get('/request-titles', [ServiceController::class, 'getRequestTitles']);
        Route::get('/legal-translation', [ServiceController::class, 'getLegalTranslationFormData']);
        Route::get('/subdocument-types', [ServiceController::class, 'getSubDocumentTypes']);
        Route::get('/immigration-request', [ServiceController::class, 'getImmigrationRequestFormData']);
        Route::get('/annual-agreement', [ServiceController::class, 'getAnnualAgreementFormData']);
        Route::get('/annual-agreement-price', [ServiceController::class, 'getAnnualAgreementPrice']);
        Route::get('/translation/payment-calculate', [ServiceController::class, 'calculateTranslationPrice']);

        Route::get('/expert-report-price', [ServiceController::class, 'getExpertReportPrice']);
        Route::get('/request-submission-price', [ServiceController::class, 'getRequestSubmissionPrice']);
        Route::get('/online-consultation-price', [ServiceController::class, 'getOnlineConsultationPrice']);

        // Service Request Submission
        Route::post('/court-case-request', [ServiceController::class, 'requestCourtCase']);
        Route::post('/criminal-complaint-request', [ServiceController::class, 'requestCriminalComplaints']);
        Route::post('/last-will-request', [ServiceController::class, 'requestLastWill']);
        Route::post('/power-of-attorney-request', [ServiceController::class, 'requestPowerOfAttorney']);
        Route::post('/memo-writing-request', [ServiceController::class, 'requestMemoWriting']);
        Route::post('/escrow-account-request', [ServiceController::class, 'requestEscrowAccount']);
        Route::post('/request-debts-collection', [ServiceController::class, 'requestDebtsCollection']);
        Route::post('/company-setup-request', [ServiceController::class, 'requestCompanySetup']);
        Route::post('/contract-drafting-request', [ServiceController::class, 'requestContractDrafting']);
        Route::post('/expert-report-request', [ServiceController::class, 'requestExpertReport']);
        Route::post('/immigration-request', [ServiceController::class, 'requestImmigration']);
        Route::post('/request-submission', [ServiceController::class, 'requestRequestSubmission']);
        Route::post('/annual-agreement-request', [ServiceController::class, 'requestAnnualAgreement']);
        Route::post('/legal-translation-request', [ServiceController::class, 'requestLegalTranslation']);

        // Payment Gateway
        Route::post('/payment-test', [ServiceController::class, 'paymentTest']);
        Route::post('/confirm-payment', [ServiceController::class, 'confirmPayment']);
        Route::get('/payment-callback', [ServiceController::class, 'paymentSuccess'])->name('payment.callback');
        Route::get('/payment-cancel', [ServiceController::class, 'paymentCancel'])->name('payment.cancel');

        // Service History
        Route::get('/service-history', [UserController::class, 'getServiceHistory']);
        Route::get('/service-history-details', [UserController::class, 'getServiceHistoryDetails']);
        Route::get('/pending-services', [UserController::class, 'getServicePending']);
        Route::get('/payment-history', [UserController::class, 'getServicePaymentHistory']);

        //Notifications
        Route::get('/notifications', [UserController::class, 'getGroupedUserNotifications']);
        Route::delete('/notifications/clear', [UserController::class, 'clearAllNotifications']);
        Route::get('/notifications/unread-count', [UserController::class, 'getUnreadNotificationCount']);

        //Training 
        Route::get('/training-data', [UserController::class, 'getTrainingFormData']);
        Route::post('/training-request', [UserController::class, 'requestTraining']);

        
        // Online Consulation User endpoints
        Route::post('/consultations', [ConsultationController::class,'store']);
        Route::get('/consultations/{id}', [ConsultationController::class,'show']);
        Route::get('/consultation/payment-success', [ConsultationController::class,'paymentSuccess']);
        Route::get('/consultation/payment-cancel', [ConsultationController::class, 'paymentCancel']);
        Route::get('/consultation/user-poll', [ConsultationController::class, 'checkUserConsultationStatus']);

        Route::post('/consultation/update-status', [ConsultationController::class, 'updateConsultationStatus']);
        Route::post('/consultations/{id}/extend', [ConsultationController::class,'extendZoom']);

        // Online Consulation Lawyer endpoints
        Route::get('/consultation/lawyer-poll', [ConsultationController::class, 'poll'])->name('lawyer.poll');
        Route::post('/respond-request', [ConsultationController::class, 'lawyerResponse']);
        Route::post('/lawyer-con/respond', [ConsultationController::class,'lawyerResponse']);
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

