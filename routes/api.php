<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\AdminController;
use App\Http\Controllers\v1\AuthController;

// Login Route
Route::post('/login', [AuthController::class, 'login']); // API login (get token)

// Protected Routes for Users
Route::middleware('auth:sanctum')->group(function () {

    // User Routes
    Route::middleware('role:USER')->group(function () {
        Route::get('/user/wallet', [UserController::class, 'getWalletBalance'])->name('user.wallet');
        Route::put('/user/deposit', [UserController::class, 'depositFunds'])->name('user.deposit');
        Route::put('/user/withdraw', [UserController::class, 'withdrawFunds'])->name('user.withdraw');
        Route::get('/user/transactions', [UserController::class, 'getTransactionHistory'])->name('user.transactions');
    });

    // Admin Routes
    Route::middleware('role:ADMIN')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'getAllUsers'])->name('admin.users');
        Route::get('/admin/users/{id}', [AdminController::class, 'getUserDetails'])->name('admin.user.details');
        Route::put('/admin/users/deposit/{id}', [AdminController::class, 'depositToUser'])->name('admin.user.deposit');
        Route::put('/admin/users/withdraw/{id}', [AdminController::class, 'withdrawFromUser'])->name('admin.user.withdraw');
    });

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout']);
});