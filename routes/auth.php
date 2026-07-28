<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    MeController,
    ProfileController,
    SportController,
};

Route::get('me', MeController::class)->name('user.me');
Route::get('sports', SportController::class)->name('sports.index');
Route::put('profiles', ProfileController::class)->name('profiles.update');
