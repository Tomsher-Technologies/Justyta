<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\MembershipPlanController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\DropdownOptionController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\JobPostController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LawyerController;
use App\Http\Controllers\Admin\TranslatorController;
use App\Http\Controllers\Admin\FreezoneController;
use App\Http\Controllers\Admin\ContractTypeController;
use App\Http\Controllers\Admin\CourtRequestController;
use App\Http\Controllers\Admin\PublicProsecutionController;
use App\Http\Controllers\Admin\LicenseTypeController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\EmirateController;
use App\Http\Controllers\Admin\ServiceRequestController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\CaseTypeController;
use App\Http\Controllers\Admin\RequestTypeController;
use App\Http\Controllers\Admin\RequestTitleController;

Route::prefix('admin')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
});

Route::prefix('admin')->middleware(['web', 'auth', 'user_type:admin,staff'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('notifications.bulkDelete');

    // Manage staffs
    Route::resource('staffs', StaffController::class);
    Route::get('/staffs/destroy/{id}', [StaffController::class, 'destroy'])->name('staffs.destroy');
    Route::post('/staff/status', [StaffController::class, 'updateStatus'])->name('staff.status');
    
    // Manage roles & permissions
    Route::resource('roles', RoleController::class);
    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');

    // Manage membership plans
    Route::resource('membership-plans', MembershipPlanController::class);
    Route::get('/plan-pricing/{id}', [MembershipPlanController::class, 'indexPricing'])->name('plan-pricing');
    Route::get('/plan-pricing-create/{id}', [MembershipPlanController::class, 'createPricing'])->name('plan-pricing.create');
    Route::post('plan-pricing/store', [MembershipPlanController::class, 'storePricing'])->name('plan-pricing.store');
    Route::get('/plan-pricing-edit/{id}/{planId}', [MembershipPlanController::class, 'editPricing'])->name('plan-pricing.edit');
    Route::put('plan-pricing/{id}', [MembershipPlanController::class, 'updatePricing'])->name('plan-pricing.update');
    Route::post('/plan-pricing/status', [MembershipPlanController::class, 'updatePricingStatus'])->name('plan-pricing.status');
    Route::delete('plan-pricing/{id}', [MembershipPlanController::class, 'destroyPricing'])->name('plan-pricing.destroy');


    // Manage law firms
    Route::resource('vendors', VendorController::class);
    Route::post('/vendor/{id}/status', [VendorController::class, 'updateStatus'])->name('vendor.updateStatus');

    // List all dropdowns
    Route::get('/dropdowns', [DropdownOptionController::class, 'dropdowns'])->name('dropdowns.index');

    // Show options for a specific dropdown
    Route::get('/dropdowns/options/{dropdown}', [DropdownOptionController::class, 'index'])->name('dropdown-options.index');
    Route::post('dropdowns/options/{dropdown}', [DropdownOptionController::class, 'store'])->name('dropdown-options.store');
    Route::put('dropdown-options/{option}', [DropdownOptionController::class, 'update'])->name('dropdown-options.update');
    Route::post('/dropdown-options/status', [DropdownOptionController::class, 'updateStatus'])->name('dropdown-options.status');

    // Manage document types
    Route::resource('document-types', DocumentTypeController::class);
    Route::post('/document-types/status', [DocumentTypeController::class, 'updateStatus'])->name('document-types.status');
    Route::get('/document-types/edit/{id}', [DocumentTypeController::class, 'edit']);

    // Manage free zones
    Route::resource('free-zones', FreezoneController::class);
    Route::post('/free-zones/status', [FreezoneController::class, 'updateStatus'])->name('free-zones.status');
    Route::get('/free-zones/edit/{id}', [FreezoneController::class, 'edit']);

    // Manage contract types
    Route::resource('contract-types', ContractTypeController::class);
    Route::post('/contract-types/status', [ContractTypeController::class, 'updateStatus'])->name('contract-types.status');
    Route::get('/contract-types/edit/{id}', [ContractTypeController::class, 'edit']);

    // Manage case types
    Route::resource('case-types', CaseTypeController::class);
    Route::post('/case-types/status', [CaseTypeController::class, 'updateStatus'])->name('case-types.status');
    Route::get('/case-types/edit/{id}', [CaseTypeController::class, 'edit']);

    // Manage Request Types and Titles
    Route::resource('request-types', RequestTypeController::class);
    Route::post('/request-types/status', [RequestTypeController::class, 'updateStatus'])->name('request-types.status');
    Route::get('/request-types/edit/{id}', [RequestTypeController::class, 'edit']);

    // Manage request Titles
    // Route::resource('request-titles', RequestTitleController::class);
    Route::get('/request-titles/{id}', [RequestTitleController::class, 'index'])->name('request-titles.index');
    Route::post('/request-titles/store', [RequestTitleController::class, 'store'])->name('request-titles.store');
    Route::get('/request-titles/edit/{id}', [RequestTitleController::class, 'edit'])->name('request-titles.edit');
    Route::put('/request-titles/{id}', [RequestTitleController::class, 'update'])->name('request-titles.update');
    Route::post('/request-titles/status', [RequestTitleController::class, 'updateStatus'])->name('request-titles.status');

    // Manage court requests
    Route::resource('court-requests', CourtRequestController::class);
    Route::post('/court-requests/status', [CourtRequestController::class, 'updateStatus'])->name('court-requests.status');
    Route::get('/court-requests/edit/{id}', [CourtRequestController::class, 'edit']);

    // Manage public prosecution types
    Route::resource('public-prosecutions', PublicProsecutionController::class);
    Route::post('/public-prosecutions/status', [PublicProsecutionController::class, 'updateStatus'])->name('public-prosecutions.status');
    Route::get('/public-prosecutions/edit/{id}', [PublicProsecutionController::class, 'edit']);

    // Manage License Types & Activities
    Route::resource('license-types', LicenseTypeController::class);
    Route::post('/license-types/status', [LicenseTypeController::class, 'updateStatus'])->name('license-types.status');
    Route::get('/license-types/edit/{id}', [LicenseTypeController::class, 'edit']);

    // Manage Emirates
    Route::resource('emirates', EmirateController::class);
    Route::post('/emirates/status', [EmirateController::class, 'updateStatus'])->name('emirates.status');
    Route::get('/emirates/edit/{id}', [EmirateController::class, 'edit']);
    Route::post('/emirates/federal-status', [EmirateController::class, 'updateFederalStatus'])->name('emirates.federal-status');
    Route::post('/emirates/local-status', [EmirateController::class, 'updateLocalStatus'])->name('emirates.local-status');

    // Manage countries
    Route::resource('countries', CountryController::class);
    Route::post('/countries/status', [CountryController::class, 'updateStatus'])->name('countries.status');
    Route::get('/countries/edit/{id}', [CountryController::class, 'edit']);

    // Manage service 
    Route::resource('services', ServiceController::class);
    Route::post('/services/status', [ServiceController::class, 'updateStatus'])->name('services.status');

    Route::get('/expert-pricing', [ServiceController::class, 'indexExpertPricing'])->name('expert-pricing.index');
    Route::get('/expert-pricing-create', [ServiceController::class, 'createExpertPricing'])->name('expert-pricing.create');
    Route::post('expert-pricing/store', [ServiceController::class, 'storeExpertPricing'])->name('expert-pricing.store');
    Route::get('/expert-pricing-edit/{id}', [ServiceController::class, 'editExpertPricing'])->name('expert-pricing.edit');
    Route::put('expert-pricing/{id}', [ServiceController::class, 'updateExpertPricing'])->name('expert-pricing.update');
    Route::post('/expert-pricing/status', [ServiceController::class, 'updateExpertPricingStatus'])->name('expert-pricing.status');
    Route::delete('expert-pricing/{id}', [ServiceController::class, 'destroyExpertPricing'])->name('expert-pricing.destroy');

    Route::get('/request-pricing', [ServiceController::class, 'indexRequestPricing'])->name('request-pricing.index');
    Route::get('/request-pricing-create', [ServiceController::class, 'createRequestPricing'])->name('request-pricing.create');
    Route::post('request-pricing/store', [ServiceController::class, 'storeRequestPricing'])->name('request-pricing.store');
    Route::get('/request-pricing-edit/{id}', [ServiceController::class, 'editRequestPricing'])->name('request-pricing.edit');
    Route::put('request-pricing/{id}', [ServiceController::class, 'updateRequestPricing'])->name('request-pricing.update');
    Route::post('/request-pricing/status', [ServiceController::class, 'updateRequestPricingStatus'])->name('request-pricing.status');
    Route::delete('request-pricing/{id}', [ServiceController::class, 'destroyRequestPricing'])->name('request-pricing.destroy');

    Route::get('/get-case-types', [ServiceController::class, 'getCaseTypes'])->name('filter-case-types');
    Route::get('/get-request-types', [ServiceController::class, 'getRequestTypes'])->name('filter-request-types');
    Route::get('/get-request-titles', [ServiceController::class, 'getRequestTitles'])->name('filter-request-titles');

    // Manage pages
    Route::resource('pages', PageController::class);

    //Manage news
    Route::resource('news', NewsController::class);
    Route::post('/news/status', [NewsController::class, 'updateStatus'])->name('news.status');

    //Manage job posts
    Route::resource('job-posts', JobPostController::class);
    Route::post('/job-posts/status', [JobPostController::class, 'updateStatus'])->name('job-posts.status');  
    Route::get('/job/applications/{id}', [JobPostController::class, 'applications'])->name('job-applications');

    //Manage faqs
    Route::resource('faqs', FaqController::class)->except(['show']);
    Route::post('/faq/status', [FaqController::class, 'updateStatus'])->name('faqs.status');  

    // Manage lawyers
    Route::resource('lawyers', LawyerController::class);

    // Manage Translators
    Route::resource('translators', TranslatorController::class);
    Route::get('default', [TranslatorController::class, 'showDefaultForm'])->name('translators.default');
    Route::post('/assign', [TranslatorController::class, 'assign'])->name('translators.set-default');
    Route::get('/default-translators/history/{from_language_id}/{to_language_id}', [TranslatorController::class, 'historyForPair'])
    ->name('default-translators.history');
    Route::get('/translator-pricing/{id}', [TranslatorController::class, 'indexPricing'])->name('translator-pricing');
    Route::get('/translator-pricing-create/{id}', [TranslatorController::class, 'createPricing'])->name('translator-pricing.create');
    Route::post('translator-pricing/store', [TranslatorController::class, 'storePricing'])->name('translator-pricing.store');
    Route::get('/translator-pricing-edit/{id}/{transId}', [TranslatorController::class, 'editPricing'])->name('translator-pricing.edit');
    Route::put('translator-pricing/{id}', [TranslatorController::class, 'updatePricing'])->name('translator-pricing.update');
    Route::post('/translator-pricing/status', [TranslatorController::class, 'updatePricingStatus'])->name('translator-pricing.status');
    Route::delete('translator-pricing/{id}', [TranslatorController::class, 'destroyPricing'])->name('translator-pricing.destroy');
    Route::get('/get-sub-doc-types/{docTypeId}', [TranslatorController::class, 'getSubDocTypes'])->name('get-sub-doc-types');

    // Service Requests Management
    Route::get('/service-requests', [ServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::get('/service-request-details/{id}', [ServiceRequestController::class, 'show'])->name('service-request-details');
    Route::post('/service-requests/request-status', [ServiceRequestController::class, 'updateRequestStatus'])->name('update-service-request-status');
    Route::post('/service-requests/payment-status', [ServiceRequestController::class, 'updatePaymentStatus'])->name('update-service-payment-status');
    Route::get('/service-requests/export', [ServiceRequestController::class, 'export'])->name('service-requests.export');
    Route::post('/service-requests/installments/update-status', [ServiceRequestController::class, 'updateInstallmentStatus'])->name('update.installment.status');
    Route::post('/service-requests/assign-lawfirm', [ServiceRequestController::class, 'assignServiceLawfirm'])->name('assign-service-lawfirm');

    //Legal Translation Requests
    Route::get('/legal-translation-requests', [ServiceRequestController::class, 'indexTranslation'])->name('legal-translation-requests.index');
    Route::get('/translation-requests/export', [ServiceRequestController::class, 'exportLegalTranslationRequests'])->name('legal-translation-requests.export');
    Route::get('/translation-request-details/{id}', [ServiceRequestController::class, 'showTranslationRequest'])->name('translation-request-details');

    //Training requests
    Route::get('/training-requests', [FeedbackController::class, 'trainingRequests'])->name('training-requests.index');
   
    // User Feedbacks
    Route::get('/reported-problems', [FeedbackController::class, 'reportedProblems'])->name('user-reported-problems.feedback');
    Route::get('/user-reported-problems/export', [FeedbackController::class, 'exportUserReportProblems'])->name('user-reported-problems.export');
    Route::get('/user-ratings', [FeedbackController::class, 'userRatings'])->name('user-ratings.feedback');
    Route::get('/user-ratings/export', [FeedbackController::class, 'exportUserRatings'])->name('user-ratings.export');
    Route::get('/user-contacts', [FeedbackController::class, 'userContacts'])->name('user-contacts.feedback');
    Route::get('/user-contacts/export', [FeedbackController::class, 'exportUserContacts'])->name('user-contacts.export');

    // Manage Ads
    Route::get('ads/', [AdController::class, 'index'])->name('ads.index');
    Route::get('ads/create', [AdController::class, 'create'])->name('ads.create');
    Route::post('ads/store', [AdController::class, 'store'])->name('ads.store');
    Route::get('ads/{id}', [AdController::class, 'show'])->name('ads.show');
    Route::get('ads/{id}/click', [AdController::class, 'click'])->name('ads.click');
    Route::get('ads/{id}/edit', [AdController::class, 'edit'])->name('ads.edit');
    Route::put('ads/{id}', [AdController::class, 'update'])->name('ads.update');
    Route::delete('ads/{id}', [AdController::class, 'destroy'])->name('ads.destroy');
    Route::post('/ads/status', [AdController::class, 'updateStatus'])->name('ads.status');
});


Route::prefix('admin')->middleware(['auth', 'user_type:translator'])->group(function () {
    // Translator-specific routes
});


