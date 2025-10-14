<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Here are the authentication routes for email verification and
| password reset functionality.
|
*/

// Email Verification Routes
Route::middleware('auth')->group(function () {
    // Email verification notice page
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Email verification handler (when user clicks link in email)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/dashboard')->with('success', 'Your email has been verified!');
    })->middleware(['signed'])->name('verification.verify');

    // Resend verification email
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Password Reset Routes
Route::middleware('guest')->group(function () {
    // Forgot password page
    Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)
        ->name('password.request');

    // Password reset sent confirmation page
    Route::get('/password-reset-sent', \App\Livewire\Auth\PasswordResetSent::class)
        ->name('password.sent');

    // Reset password page
    Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)
        ->name('password.reset');
});
