<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\Auth\AuthController;
use App\Http\Controllers\Frontend\ServiceRequestController;
use App\Http\Controllers\Frontend\TranslatorController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\VendorHomeController;
use App\Http\Controllers\Frontend\LawyerController;
use App\Http\Controllers\Frontend\VendorJobPostController;

require __DIR__ . '/admin.php';

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/refund-policy', [HomeController::class, 'refundPolicy'])->name('refund-policy');

// Frontend Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('frontend.login');
Route::post('/login', [AuthController::class, 'login'])->name('frontend.login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('frontend.logout');

// User registration
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('frontend.register');
Route::post('/register', [AuthController::class, 'register'])->name('frontend.register.submit');
Route::get('vendor-success-payment', [AuthController::class, 'purchaseSuccess'])->name('purchase-success');
Route::get('vendor-cancel-payment', [AuthController::class, 'purchaseCancel'])->name('purchase-cancel');

// Vendor/Law firm registration
Route::get('/law-firm-register', [AuthController::class, 'showLawfirmRegisterForm'])->name('law-firm.register');
Route::post('/law-firm-register', [AuthController::class, 'registerLawfirm'])->name('law-firm.register.submit');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('frontend.forgot-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('frontend.reset-password');
Route::get('/enter-otp', [AuthController::class, 'showOtpForm'])->name('otp.enter');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
Route::get('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
Route::get('/new-password', [AuthController::class, 'newPasswordForm'])->name('new-password');
Route::post('/set-new-password', [AuthController::class, 'submitNewPassword'])->name('password.set.submit');


Route::get('success-payment', [ServiceRequestController::class, 'paymentSuccess'])->name('successPayment');
Route::get('cancel-payment', [ServiceRequestController::class, 'paymentCancel'])->name('cancelPayment');
Route::get('success-consultation-payment', [ServiceRequestController::class, 'consultationPaymentSuccess'])->name('consultationSuccessPayment');
Route::get('cancel-consultation-payment', [ServiceRequestController::class, 'consultationPaymentCancel'])->name('consultationCancelPayment');
Route::post('network-webhook', [ServiceRequestController::class, 'networkWebhook'])->name('network-webhook');

// Protected Dashboards
Route::prefix('lawyer')->middleware(['auth:frontend', 'checkFrontendUserType:lawyer'])->group(function () {
    Route::get('/dashboard', [LawyerController::class, 'lawyerDashboard'])->name('lawyer.dashboard');

    Route::get('/lawyer/video', [LawyerController::class, 'lawyerDashboard'])->name('web.lawyer.video');
    Route::get('/web/lawyer/poll', [LawyerController::class, 'poll'])->name('web.lawyer.poll');
    Route::post('/web/lawyer/response', [LawyerController::class, 'lawyerResponse'])->name('web.lawyer.response');
    Route::post('/consultation/update-status', [LawyerController::class, 'updateConsultationStatus'])
    ->name('consultation.status.update');
});

Route::prefix('vendor')->middleware(['auth:frontend', 'checkFrontendUserType:vendor'])->group(function () {
    Route::get('/dashboard', [VendorHomeController::class, 'dashboard'])->name('vendor.dashboard');
    // Manage Lawyers
    Route::get('/lawyers', [VendorHomeController::class, 'lawyers'])->name('vendor.lawyers');
    Route::get('/create-lawyer', [VendorHomeController::class, 'createLawyer'])->name('vendor.create.lawyers');
    Route::post('/store-lawyer', [VendorHomeController::class, 'storeLawyer'])->name('vendor.store.lawyers');

    Route::post('/check-lawyer-email', function (Illuminate\Http\Request $request) {
        $exists = \App\Models\User::where('email', $request->email)
            ->where('user_type', 'lawyer')
            ->exists();

        return response()->json(!$exists); // true means valid, false means already taken
    })->name('check.lawyer.email');

    Route::get('/edit-lawyer/{id}', [VendorHomeController::class, 'editLawyer'])->name('vendor.edit.lawyers');
    Route::put('/update-lawyer/{id}', [VendorHomeController::class, 'updateLawyer'])->name('vendor.update.lawyers');
    Route::get('/lawyer-details/{id}', [VendorHomeController::class, 'viewLawyer'])->name('vendor.view.lawyers');

    //Manage job posts
    Route::resource('jobs', VendorJobPostController::class);
    Route::post('/jobs/status', [VendorJobPostController::class, 'updateStatus'])->name('jobs.status');
    Route::get('/jobs/details/{id}', [VendorJobPostController::class, 'jobPostDetails'])->name('jobs.details');
    Route::get('/jobs/edit/{id}', [VendorJobPostController::class, 'edit'])->name('jobs.edit');
    Route::post('/jobs/delete', [VendorJobPostController::class, 'destroy'])->name('jobs.delete');
    Route::get('/jobs/applications/{id}', [VendorJobPostController::class, 'applications'])->name('jobs.applications');
});

Route::prefix('translator')->middleware(['auth:frontend', 'checkFrontendUserType:translator'])->group(function () {
    Route::get('/dashboard', [TranslatorController::class, 'dashboard'])->name('translator.dashboard');
    Route::get('/my-account', [TranslatorController::class, 'account'])->name('translator.my-account');

    //Service Requests
    Route::get('/service-request/{id}', [TranslatorController::class, 'showServiceRequest'])->name('translator.service.details');
    Route::post('/service-request/{id}/update-status', [TranslatorController::class, 'updateServiceRequestStatus'])->name('translator.service.update-status');
    Route::get('/service-request/{id}/download', [TranslatorController::class, 'downloadServiceCompletedFiles'])->name('translator.service-request.download');
    Route::get('/service-requests', [TranslatorController::class, 'serviceRequestsIndex'])->name('translator.service.index');

    //Notifications
    Route::get('/notifications', [TranslatorController::class, 'notifications'])->name('translator.notifications.index');
    Route::post('/notifications/clear', [TranslatorController::class, 'clearAllNotifications'])->name('translator.notifications.clear');
    Route::post('/notifications/delete-selected', [TranslatorController::class, 'deleteSelectedNotifications'])->name('translator.notifications.delete.selected');
});

Route::prefix('user')->middleware(['auth:frontend', 'checkFrontendUserType:user'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'userDashboard'])->name('user.dashboard');

    // Online Video Call
    Route::get('/user/video', [HomeController::class, 'userDashboard'])->name('web.user.video');
    Route::get('/web/user/check-consultation', [HomeController::class, 'checkUserConsultationStatus'])->name('web.user.check');

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

    Route::get('/online-live-consultancy', [ServiceRequestController::class, 'showConsultationForm'])->name('service.online.consultation');
    Route::post('/request-consultation', [ServiceRequestController::class,'requestConsultation'])->name('service.request.consultation');

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
    Route::get('/emirates', [ServiceRequestController::class, 'getEmirates'])->name('user.emirates');
    Route::get('/case-types', [ServiceRequestController::class, 'getCaseTypes'])->name('user.case-types');
    Route::get('/request-submission-price', [ServiceRequestController::class, 'getRequestSubmissionPrice'])->name('user.request-submission-price');
    Route::get('/expert-report-price', [ServiceRequestController::class, 'getExpertReportPrice'])->name('user.expert-report-price');
    Route::get('/online-consultation-price', [ServiceRequestController::class, 'getOnlineConsultationPrice'])->name('user.consultation-fee');

    // Payment call back Online Consultation
    Route::get('/consultation-payment-success/{id}', [ServiceRequestController::class, 'consultationWaitingLawyer'])->name('user.consultation-payment.success');
    Route::get('/consultation-payment-cancel', [ServiceRequestController::class, 'consultationPaymentCancel'])->name('user.consultation-payment.cancel');
    Route::get('/consultation-payment-failed', [ServiceRequestController::class, 'consultationRequestFailed'])->name('user.payment-consultation-failed');
    


    // Payment call back
    Route::get('/payment-callback/{order_id}', [ServiceRequestController::class, 'paymentSuccess'])->name('user.web-payment.callback');
    Route::get('/payment-cancel', [ServiceRequestController::class, 'paymentCancel'])->name('user.web-payment.cancel');

    // User Feedbacks
    Route::get('/report-a-problem', [UserController::class, 'reportProblem'])->name('user-report-problem');
    Route::post('/report-problem', [UserController::class, 'submitReportProblem'])->name('user.report.problem.submit');
    Route::get('/rate-us', [UserController::class, 'rateUs'])->name('user-rate-us');
    Route::post('/rating', [UserController::class, 'rateUsSave'])->name('user.rating.submit');

    // Manage Training Request
    Route::get('/training-request', [UserController::class, 'getTrainingFormData'])->name('user-training-request');
    Route::post('/training-request-save', [UserController::class, 'requestTraining'])->name('user-training-training-submit');

    // Manage Jobs
    Route::get('/law-firm-jobs', [UserController::class, 'jobPosts'])->name('user-lawfirm-jobs');
    Route::get('/law-firm-jobs/details/{id}', [UserController::class, 'jobPostDetails'])->name('user.job.details');
    Route::get('/law-firm-jobs/apply/{id}', [UserController::class, 'jobPostApply'])->name('user.job.details.apply');
    Route::post('/law-firm-apply-job', [UserController::class, 'applyJob'])->name('user.job.apply');

    // Service History 
    Route::get('/service-history', [UserController::class, 'serviceHistory'])->name('user.service.history');
    Route::get('/pending-services', [UserController::class, 'servicePending'])->name('user.service.pending');
    Route::get('/payment-services', [UserController::class, 'servicePayment'])->name('user.service.payment');

    Route::get('/service-history-details/{id}', [UserController::class, 'getServiceHistoryDetails'])->name('user.service.history.details');
    Route::get('/service-pending-details/{id}', [UserController::class, 'getServiceHistoryDetails'])->name('user.service.pending.details');
    Route::get('/service-payment-details/{id}', [UserController::class, 'getServiceHistoryDetails'])->name('user.service.payment.details');

    Route::get('/service-request/{id}/download', [UserController::class, 'downloadServiceCompletedFiles'])->name('user.service-request.download');
    Route::post('/service-request/{id}/re-upload', [ServiceRequestController::class, 'reUploadAfterRejection'])->name('user.service-request.re-upload');

    Route::get('/my-account', [UserController::class, 'account'])->name('user.my-account');
    Route::post('/user-profile', [UserController::class, 'updateProfile'])->name('user.update.profile');
    Route::delete('/account/delete', [UserController::class, 'deleteAccount'])->name('user.delete.account');
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('user.change-password');
    Route::post('/update-password', [UserController::class, 'updateNewPassword'])->name('user.update-new-password');
    Route::get('/notifications', [UserController::class, 'notifications'])->name('user.notifications.index');
    Route::post('/notifications/clear', [UserController::class, 'clearAllNotifications'])->name('user.notifications.clear');
    Route::post('/notifications/delete-selected', [UserController::class, 'deleteSelectedNotifications'])->name('user.notifications.delete.selected');

    Route::get('/search-services', [UserController::class, 'searchService'])->name('user.search.services');
});




Route::get('/lang/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'ar', 'fr', 'fa', 'ru', 'zh'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return redirect()->back();
})->name('lang.switch');
