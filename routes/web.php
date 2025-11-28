<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserController;

// Home
Route::get('/', function () {
    return view('welcome');
});

// Guest Routes (Only for non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'registerWeb']);

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'loginWeb']);

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPasswordWeb'])->name('password.email');

    Route::get('/reset-password/{token}', function ($token) {
        $email = request('email');
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    })->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPasswordWeb'])->name('password.update');
});

// Email Verification (Everyone can access)
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verifyWeb'])
    ->name('verification.verify');

// Resend Verification Email (Guests only)
Route::post('/resend-verification-email', [EmailVerificationController::class, 'resendWeb'])
    ->middleware('guest')
    ->name('verification.send');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'updateWeb'])->name('profile.update');
    Route::post('/profile/delete', [ProfileController::class, 'deleteWeb'])->name('profile.delete');

    // Users & Profiles
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // Account Settings
    Route::get('/account/edit', [AuthController::class, 'editAccount'])->name('account.edit');
    Route::post('/account/update', [AuthController::class, 'updateAccountWeb'])->name('account.update');
    Route::post('/account/delete', [AuthController::class, 'deleteAccountWeb'])->name('account.delete');
});
