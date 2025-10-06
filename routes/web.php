<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin redirect - redirects to login if not authenticated, or to admin dashboard if authenticated
Route::get('/admin', function () {
    return redirect('/admin/login');
})->name('admin');

// Include route files
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
