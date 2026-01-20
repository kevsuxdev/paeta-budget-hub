<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DeptHeadController;
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/login', [AuthController::class, 'authenticate'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/budget/create', [AdminController::class, 'createBudget'])->name('admin.budget.create');
    Route::post('/admin/budget/store', [AdminController::class, 'storeBudget'])->name('admin.budget.store');
    Route::get('/admin/document-tracking', [AdminController::class, 'documentTracking'])->name('admin.document.tracking');
    Route::get('/admin/finance-review', [AdminController::class, 'financeReview'])->name('admin.finance.review');
    Route::post('/admin/budget/{budget}/approve', [AdminController::class, 'approveBudget'])->name('admin.budget.approve');
    Route::post('/admin/budget/{budget}/reject', [AdminController::class, 'rejectBudget'])->name('admin.budget.reject');
    Route::get('/admin/user-management', [AdminController::class, 'userManagement'])->name('admin.user.management');
    Route::post('/admin/departments', [AdminController::class, 'storeDepartment'])->name('admin.departments.store');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
});

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff/budget/create', [StaffController::class, 'createBudget'])->name('staff.budget.create');
    Route::post('/staff/budget/store', [StaffController::class, 'storeBudget'])->name('staff.budget.store');
    Route::get('/staff/document-tracking', [StaffController::class, 'documentTracking'])->name('staff.document.tracking');
});
Route::middleware(['auth', 'role:dept_head'])->get('/dept_head/dashboard', [DeptHeadController::class, 'dashboard'])->name('dept_head.dashboard');
Route::middleware(['auth', 'role:finance'])->group(function () {
    Route::get('/finance/dashboard', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance/review', [FinanceController::class, 'review'])->name('finance.review');
    Route::post('/finance/budget/{budget}/approve', [FinanceController::class, 'approveBudget'])->name('finance.budget.approve');
    Route::post('/finance/budget/{budget}/reject', [FinanceController::class, 'rejectBudget'])->name('finance.budget.reject');
});
