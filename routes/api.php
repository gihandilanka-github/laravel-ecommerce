<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Auth\RoleController;
use App\Http\Controllers\Api\v1\User\UserController;
use App\Http\Controllers\Api\v1\Auth\PermissionController;
use App\Http\Controllers\Api\v1\User\UserRoleController;
use App\Http\Controllers\Api\v1\Auth\ModuleController;
use App\Http\Controllers\Api\v1\Order\OrderController;
use App\Http\Controllers\Api\v1\Product\ProductController;

Route::middleware(['api', 'throttle:10,1'])->prefix('v1')->group(function () {


    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/resend', [AuthController::class, 'resend'])
        ->name('verification.resend');

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/oauth/token', [AuthController::class, 'authToken']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:superadmin')->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::post('users/{user}/assign-role', [UserRoleController::class, 'assignRole']);
        Route::post('users/{user}/revoke-role', [UserRoleController::class, 'revokeRole']);
        Route::apiResource('products', ProductController::class);
    });

    Route::apiResource('modules', ModuleController::class);
    Route::apiResource('orders', OrderController::class);
    Route::put('update-order-status/{orderId}', [OrderController::class, 'updateOrderStatus']);
});
