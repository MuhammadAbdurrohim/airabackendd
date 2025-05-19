<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\LiveStreamingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// General Webhook Routes
Route::post('webhook', [WebhookController::class, 'handle']);

// WhatsApp Webhook Routes
Route::prefix('webhook')->group(function () {
    Route::post('whatsapp', [WhatsAppWebhookController::class, 'handle']);
    Route::post('whatsapp/status', [WhatsAppWebhookController::class, 'status']);
});

// Auth Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('profile', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    
    // Cart
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'add']);
    Route::post('cart/update', [CartController::class, 'update']);
    Route::delete('cart/{id}', [CartController::class, 'remove']);
    
    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/{order}/upload-payment', [OrderController::class, 'uploadPayment']);
    
    // Live Streaming
    Route::get('live-streams', [LiveStreamingController::class, 'index']);
    Route::get('live-streams/{id}', [LiveStreamingController::class, 'show']);
    Route::post('live-streams/{id}/comments', [LiveStreamingController::class, 'comment']);
});
