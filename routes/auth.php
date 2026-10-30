<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordOtpController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

Route::post('forgot-password', [PasswordOtpController::class, 'sendCode'])
    ->middleware('throttle:5,1')->name('password.email');

Route::get('forgot-password/verify', [PasswordOtpController::class, 'showVerify'])
    ->name('password.otp');
Route::post('forgot-password/verify', [PasswordOtpController::class, 'verify'])
    ->middleware('throttle:10,1')->name('password.otp.verify');
Route::post('forgot-password/resend', [PasswordOtpController::class, 'resend'])
    ->middleware('throttle:3,1')->name('password.otp.resend');

Route::get('forgot-password/new', [PasswordOtpController::class, 'showNewPassword'])
    ->name('password.new');
Route::post('forgot-password/new', [PasswordOtpController::class, 'updatePassword'])
    ->name('password.new.update');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [OtpVerificationController::class, 'show'])
        ->name('verification.notice');

    Route::post('verify-otp', [OtpVerificationController::class, 'verify'])
        ->name('verification.otp');

    Route::post('verify-otp/resend', [OtpVerificationController::class, 'resend'])
        ->middleware('throttle:3,1')
        ->name('verification.resend');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
