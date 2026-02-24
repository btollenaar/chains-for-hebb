<?php

use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\OrderApiController;
use App\Http\Controllers\Api\V1\ProductApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Session-based AJAX endpoints (cart, coupon) remain in
| routes/web.php under the Route::prefix('api') group.
|
| The v1 REST API routes below use Sanctum token authentication and are
| stateless (no session/CSRF). They are served under /api/v1/*.
|
*/

// API v1 - Public endpoints
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    // Products
    Route::get('products', [ProductApiController::class, 'index']);
    Route::get('products/{product}', [ProductApiController::class, 'show']);

    // Auth
    Route::post('auth/login', [AuthApiController::class, 'login']);
});

// API v1 - Authenticated endpoints
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    // Auth
    Route::post('auth/logout', [AuthApiController::class, 'logout']);
    Route::get('auth/user', [AuthApiController::class, 'user']);

    // Orders (own orders only)
    Route::get('orders', [OrderApiController::class, 'index']);
    Route::get('orders/{order}', [OrderApiController::class, 'show']);
});
