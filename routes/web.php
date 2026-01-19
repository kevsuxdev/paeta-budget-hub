<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/login', [AuthController::class, 'authenticate'])->name('auth.login');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');