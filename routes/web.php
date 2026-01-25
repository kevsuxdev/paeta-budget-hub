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

// Password reset for all roles
Route::post('/user/reset-password', [AuthController::class, 'resetPassword'])->name('user.resetPassword')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/budget/{budget}/download-pdf', [AdminController::class, 'downloadBudgetPdf'])->name('admin.budget.downloadPdf');
    Route::get('/admin/budget/create', [AdminController::class, 'createBudget'])->name('admin.budget.create');
    Route::post('/admin/budget/store', [AdminController::class, 'storeBudget'])->name('admin.budget.store');
    Route::get('/admin/document-tracking', [AdminController::class, 'documentTracking'])->name('admin.document.tracking');
    Route::get('/admin/budget/{budget}/logs', [AdminController::class, 'getBudgetLogs'])->name('admin.budget.logs');
    Route::get('/admin/finance-review', [AdminController::class, 'financeReview'])->name('admin.finance.review');
    Route::get('/admin/approval', [AdminController::class, 'approval'])->name('admin.approval');
    Route::post('/admin/budget/{budget}/approve', [AdminController::class, 'approveBudget'])->name('admin.budget.approve');
    Route::post('/admin/budget/{budget}/reject', [AdminController::class, 'rejectBudget'])->name('admin.budget.reject');
    Route::post('/admin/budget/{budget}/update-status', [AdminController::class, 'updateBudgetStatus'])->name('admin.budget.updateStatus');
    Route::post('/admin/budget/{budget}/final-approve', [AdminController::class, 'finalApproveBudget'])->name('admin.budget.finalApprove');
    Route::post('/admin/budget/{budget}/final-reject', [AdminController::class, 'finalRejectBudget'])->name('admin.budget.finalReject');
    Route::get('/admin/user-management', [AdminController::class, 'userManagement'])->name('admin.user.management');
    Route::post('/admin/departments', [AdminController::class, 'storeDepartment'])->name('admin.departments.store');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/change-password', [AdminController::class, 'changeUserPassword'])->name('admin.users.changePassword');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    Route::get('/admin/audit-trail', [AdminController::class, 'auditTrail'])->name('admin.audit.trail');
    Route::get('/admin/archive', [AdminController::class, 'archive'])->name('admin.archive');
});

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff/budget/create', [StaffController::class, 'createBudget'])->name('staff.budget.create');
    Route::post('/staff/budget/store', [StaffController::class, 'storeBudget'])->name('staff.budget.store');
    Route::get('/staff/document-tracking', [StaffController::class, 'documentTracking'])->name('staff.document.tracking');
    Route::get('/staff/budget/{budget}/logs', [StaffController::class, 'getBudgetLogs'])->name('staff.budget.logs');
});
Route::middleware(['auth', 'role:dept_head'])->group(function () {
    Route::get('/dept_head/dashboard', [DeptHeadController::class, 'dashboard'])->name('dept_head.dashboard');
    Route::get('/dept_head/document-tracking', [DeptHeadController::class, 'documentTracking'])->name('dept_head.document.tracking');
    Route::get('/dept_head/budget/create', [DeptHeadController::class, 'createBudget'])->name('dept_head.budget.create');
    Route::post('/dept_head/budget/store', [DeptHeadController::class, 'storeBudget'])->name('dept_head.budget.store');
    Route::post('/dept_head/budget/{budget}/update-status', [DeptHeadController::class, 'updateBudgetStatus'])->name('dept_head.budget.updateStatus');
    Route::get('/dept_head/budget/{budget}/logs', [DeptHeadController::class, 'getBudgetLogs'])->name('dept_head.budget.logs');
});

Route::middleware(['auth', 'role:finance'])->group(function () {
    Route::get('/finance/dashboard', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance/review', [FinanceController::class, 'review'])->name('finance.review');
    Route::get('/finance/approval', [FinanceController::class, 'approval'])->name('finance.approval');
    Route::get('/finance/budget/{budget}/logs', [FinanceController::class, 'getBudgetLogs'])->name('finance.budget.logs');
    Route::post('/finance/budget/{budget}/update-status', [FinanceController::class, 'updateBudgetStatus'])->name('finance.budget.updateStatus');
    Route::post('/finance/budget/{budget}/final-approve', [FinanceController::class, 'finalApproveBudget'])->name('finance.budget.finalApprove');
    Route::post('/finance/budget/{budget}/final-reject', [FinanceController::class, 'finalRejectBudget'])->name('finance.budget.finalReject');
    Route::post('/finance/budget/{budget}/approve', [FinanceController::class, 'approveBudget'])->name('finance.budget.approve');
    Route::post('/finance/budget/{budget}/reject', [FinanceController::class, 'rejectBudget'])->name('finance.budget.reject');
    Route::get('/finance/audit-trail', [FinanceController::class, 'auditTrail'])->name('finance.audit.trail');
    Route::get('/finance/archive', [FinanceController::class, 'archive'])->name('finance.archive');
});
