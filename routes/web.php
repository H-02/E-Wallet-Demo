<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\web\AuthController;
use App\Http\Controllers\web\UserController;
use App\Http\Controllers\web\AdminController;

// Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/home', [HomeController::class, 'index'])->name('home');


// Protected Routes
Route::middleware('auth')->group(function () {
    // User Dashboard Routes
    Route::middleware('role:USER')->group(function () {
        Route::get('user/dashboard', [UserController::class, 'showDashboard'])->name('user.dashboard');
        Route::get('/wallet', [UserController::class, 'getWalletBalance'])->name('user.wallet');
        Route::post('/wallet/deposit', [UserController::class, 'depositFunds'])->name('user.wallet.deposit');
        Route::post('/wallet/withdraw', [UserController::class, 'withdrawFunds'])->name('user.wallet.withdraw');
        Route::get('/wallet/transactions', [UserController::class, 'getTransactionHistory'])->name('user.wallet.transactions');
    });

    // Admin Panel Routes
    Route::middleware('role:ADMIN')->group(function () {
        Route::get('admin/dashboard', [AdminController::class, 'showDashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'getAllUsers'])->name('admin.users');
        Route::post('/admin/users/deposit/{id}', [AdminController::class, 'depositToUser'])->name('admin.users.deposit');
        Route::post('/admin/users/withdraw/{id}', [AdminController::class, 'withdrawFromUser'])->name('admin.users.withdraw');
        Route::get('/admin/users/transactions/{id}', [AdminController::class, 'getUserDetails'])->name('admin.users.transactions');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

