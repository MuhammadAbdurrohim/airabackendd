<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentSettingsController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\LiveStreamingController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth:admin')->group(function () {
    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verifyPayment');
        Route::post('{order}/reject-payment', [OrderController::class, 'rejectPayment'])->name('orders.rejectPayment');
        Route::post('{order}/shipping-proof', [OrderController::class, 'uploadShippingProof'])->name('orders.uploadShippingProof');
        Route::delete('{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    // Products
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::post('products/reorder', [ProductController::class, 'reorder'])->name('products.reorder');

    // Users
    Route::resource('users', UserController::class)->except(['create', 'store']);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // Payment Settings
    Route::prefix('payment-settings')->group(function () {
        Route::get('/', [PaymentSettingsController::class, 'index'])->name('payment-settings.index');
        Route::post('/', [PaymentSettingsController::class, 'store'])->name('payment-settings.store');
        Route::patch('{paymentSetting}/toggle-status', [PaymentSettingsController::class, 'toggleStatus'])
            ->name('payment-settings.toggleStatus');
        Route::delete('{paymentSetting}', [PaymentSettingsController::class, 'destroy'])->name('payment-settings.destroy');
    });

    // Shipping
    Route::prefix('shipping')->group(function () {
        Route::get('/', [ShippingController::class, 'index'])->name('shipping.index');
        Route::post('/', [ShippingController::class, 'store'])->name('shipping.store');
        Route::patch('{shipping}/toggle-status', [ShippingController::class, 'toggleStatus'])
            ->name('shipping.toggleStatus');
        Route::delete('{shipping}', [ShippingController::class, 'destroy'])->name('shipping.destroy');
    });

    // Live Streaming
    Route::prefix('streaming')->group(function () {
        Route::get('/', [LiveStreamingController::class, 'index'])->name('streaming.index');
        Route::get('dashboard', [LiveStreamingController::class, 'dashboard'])->name('streaming.dashboard');
        Route::post('start', [LiveStreamingController::class, 'start'])->name('streaming.start');
        Route::post('end', [LiveStreamingController::class, 'end'])->name('streaming.end');
        Route::post('toggle-product', [LiveStreamingController::class, 'toggleProduct'])->name('streaming.toggleProduct');
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [AuthController::class, 'showProfile'])->name('profile.show');
        Route::patch('/', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::patch('/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    });
});
