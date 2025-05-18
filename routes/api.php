<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::post('cart', [CartController::class, 'add']);
    Route::get('cart', [CartController::class, 'index']);
    
    // Order Routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
        Route::post('/{order}/upload-proof', [OrderController::class, 'uploadProof']);
        Route::post('/{order}/complete', [OrderController::class, 'complete']);
        Route::post('/{order}/complaints', [OrderController::class, 'submitComplaint']);
        Route::get('/{order}/track', [OrderController::class, 'trackShipment']);
    });
});

// Admin Order Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
});
