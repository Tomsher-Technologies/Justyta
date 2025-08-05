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

    // Manage law firms
    Route::resource('vendors', VendorController::class);

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

    // Manage countries
    Route::resource('countries', CountryController::class);
    Route::post('/countries/status', [CountryController::class, 'updateStatus'])->name('countries.status');
    Route::get('/countries/edit/{id}', [CountryController::class, 'edit']);

    // Manage service 
    Route::resource('services', ServiceController::class);
    Route::post('/services/status', [ServiceController::class, 'updateStatus'])->name('services.status');

    // Manage pages
    Route::resource('pages', PageController::class);

    //Manage news
    Route::resource('news', NewsController::class);
    Route::post('/news/status', [NewsController::class, 'updateStatus'])->name('news.status');

    //Manage job posts
    Route::resource('job-posts', JobPostController::class);
    Route::post('/job-posts/status', [JobPostController::class, 'updateStatus'])->name('job-posts.status');  

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


