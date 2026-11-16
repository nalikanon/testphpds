<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/db-viewer', [DashboardController::class, 'dbViewer'])->name('db.viewer');

    // Profile & Password
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Customers CRUD
    Route::resource('customers', CustomerController::class);

    // Items CRUD & AJAX JSON API
    Route::get('/api/items/{item}', [ItemController::class, 'getItemJson'])->name('api.items.show');
    Route::resource('items', ItemController::class);

    // Sales Orders CRUD & Actions
    Route::post('/orders/{order}/confirm', [SalesOrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/{order}/cancel', [SalesOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/print', [SalesOrderController::class, 'print'])->name('orders.print');
    Route::resource('orders', SalesOrderController::class);

    // Admin-only: Users Management
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
