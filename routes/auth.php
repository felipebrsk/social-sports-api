<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    MeController,
    ProfileController,
};

Route::get('me', MeController::class)->name('user.me');
Route::put('profiles', ProfileController::class)->name('profiles.update');
