<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Authentication\LoginController,
    Authentication\RegisterController,
    Authentication\VerificationController,
    Authentication\ResetPasswordController,
    Authentication\ForgotPasswordController,
};

Route::prefix('authentication')->name('authentication.')->group(function () {
    Route::prefix('email/verification')->controller(VerificationController::class)->middleware(['throttle:6,1'])->group(function () {
        Route::post('send', 'resend')->name('email.notify')->middleware(['api', 'auth']);
        Route::get('verify', 'verify')->name('email.verify')->middleware(['api', 'signed']);
    });

    Route::prefix('password')->group(function () {
        Route::post('reset', ResetPasswordController::class)->name('reset-password');
        Route::post('forgot', ForgotPasswordController::class)->name('forgot-password');
    });

    Route::post('login', LoginController::class)->name('login');
    Route::post('register', RegisterController::class)->name('register');
});
