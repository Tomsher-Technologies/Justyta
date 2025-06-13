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

Route::prefix('admin')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
});

Route::prefix('admin')->middleware(['web', 'auth', 'user_type:admin,staff'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

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
    Route::get('admin/dropdowns', [DropdownOptionController::class, 'dropdowns'])->name('dropdowns.index');

    // Show options for a specific dropdown
    Route::get('admin/dropdowns/{dropdown}/options', [DropdownOptionController::class, 'index'])->name('dropdown-options.index');
    Route::post('dropdowns/{dropdown}/options', [DropdownOptionController::class, 'store'])->name('dropdown-options.store');
    Route::put('dropdown-options/{option}', [DropdownOptionController::class, 'update'])->name('dropdown-options.update');
    Route::post('/dropdown-options/status', [DropdownOptionController::class, 'updateStatus'])->name('dropdown-options.status');

    // Manage document types
    Route::resource('document-types', DocumentTypeController::class);
    Route::post('/document-types/status', [DocumentTypeController::class, 'updateStatus'])->name('document-types.status');

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
    Route::post('set-default', [TranslatorController::class, 'setDefault'])->name('translators.set-default');



    // User Management
    // Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    // Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    // Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
});


Route::prefix('admin')->middleware(['auth', 'user_type:translator'])->group(function () {
    // Translator-specific routes
});


