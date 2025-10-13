<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\Api\SelfieController;
// use App\Http\Controllers\Api\UtilsController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Push notification routes
    Route::post('/push/register', [PushNotificationController::class, 'register']);

    // Selfie routes
    Route::prefix('selfies')->group(function () {
        Route::post('/upload', [SelfieController::class, 'upload']);
        Route::get('/', [SelfieController::class, 'index']);
        Route::delete('/{id}', [SelfieController::class, 'destroy']);
    });

    // Utils routes
    // Route::prefix('utils')->group(function () {
    //     Route::get('/enums', [UtilsController::class, 'enums']);
    // });
});
