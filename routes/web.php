<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LiveStreamingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentSettingsController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('login', [AuthController::class, 'login'])->name('admin.login.submit');
    });

    Route::middleware('auth')->group(function () {
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('dashboard/stats', [DashboardController::class, 'getRealtimeStats'])->name('admin.dashboard.stats');

        // Logout
        Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

        // Live Streaming
        Route::prefix('streaming')->group(function () {
            Route::get('/', [LiveStreamingController::class, 'index'])->name('admin.streaming.index');
            Route::get('/dashboard', [LiveStreamingController::class, 'dashboard'])->name('admin.streaming.dashboard');
            Route::post('/start', [LiveStreamingController::class, 'startStream'])->name('admin.streaming.start');
            Route::post('/end', [LiveStreamingController::class, 'endStream'])->name('admin.streaming.end');
            Route::post('/export-comments', [LiveStreamingController::class, 'exportComments'])->name('admin.streaming.export-comments');
            Route::post('/add-product', [LiveStreamingController::class, 'addProduct'])->name('admin.streaming.add-product');
            Route::post('/remove-product', [LiveStreamingController::class, 'removeProduct'])->name('admin.streaming.remove-product');
        });

        // Product Management
        Route::resource('products', ProductController::class, ['as' => 'admin']);

        // Order Management
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('admin.orders.index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
            Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
            Route::post('/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('admin.orders.verify-payment');
        });

        // Payment Settings
        Route::prefix('settings')->group(function () {
            Route::get('/payment', [PaymentSettingsController::class, 'index'])->name('admin.settings.payment');
            Route::post('/payment', [PaymentSettingsController::class, 'store'])->name('admin.settings.payment.store');
            Route::put('/payment/{setting}', [PaymentSettingsController::class, 'update'])->name('admin.settings.payment.update');
            Route::delete('/payment/{setting}', [PaymentSettingsController::class, 'destroy'])->name('admin.settings.payment.destroy');
        });

        // Shipping Management
        Route::prefix('shipping')->group(function () {
            Route::get('/', [ShippingController::class, 'index'])->name('admin.shipping.index');
            Route::post('/calculate', [ShippingController::class, 'calculate'])->name('admin.shipping.calculate');
            Route::put('/{order}/update-shipping', [ShippingController::class, 'updateShipping'])->name('admin.shipping.update');
        });

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/{user}', [UserController::class, 'show'])->name('admin.users.show');
            Route::put('/{user}/block', [UserController::class, 'toggleBlock'])->name('admin.users.toggle-block');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
        });
    });
});
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;

// Removed duplicate routes below as they are already defined in the admin prefix group above
