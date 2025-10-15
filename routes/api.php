<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\Api\SelfieController;
use App\Http\Controllers\Api\MoodApiController;
use App\Http\Controllers\Api\UserProfileController;
// use App\Http\Controllers\Api\UtilsController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::post('/auth/establish-session', [AuthController::class, 'establishSession']);

    // Push notification routes
    Route::post('/push/register', [PushNotificationController::class, 'register']);

    // Selfie routes
    Route::prefix('selfies')->group(function () {
        Route::post('/upload', [SelfieController::class, 'upload']);
        Route::get('/', [SelfieController::class, 'index']);
        Route::delete('/{id}', [SelfieController::class, 'destroy']);
    });

    // Mood routes
    Route::prefix('moods')->group(function () {
        Route::get('/', [MoodApiController::class, 'index']);
        Route::post('/', [MoodApiController::class, 'store']);
        Route::get('/{id}', [MoodApiController::class, 'show']);
        Route::put('/{id}', [MoodApiController::class, 'update']);
        Route::delete('/{id}', [MoodApiController::class, 'destroy']);
    });

    // User Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserProfileController::class, 'show']);
        Route::put('/', [UserProfileController::class, 'update']);
        Route::get('/calendar-status', [UserProfileController::class, 'calendarStatus']);
        Route::post('/calendar/toggle', [UserProfileController::class, 'toggleCalendarSync']);
        Route::post('/calendar/sync', [UserProfileController::class, 'syncCalendar']);
        Route::post('/calendar/disconnect', [UserProfileController::class, 'disconnectCalendar']);
        Route::put('/calendar/quiet-hours', [UserProfileController::class, 'updateQuietHours']);
    });

    // Utils routes
    // Route::prefix('utils')->group(function () {
    //     Route::get('/enums', [UtilsController::class, 'enums']);
    // });
});
