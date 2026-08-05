<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    MeController,
    SportController,
    VenueController,
    ProfileController,
    GameSessionController,
};

Route::get('me', MeController::class)->name('user.me');
Route::get('sports', SportController::class)->name('sports.index');
Route::put('profiles', ProfileController::class)->name('profiles.update');

Route::apiResource('game-sessions', GameSessionController::class);
Route::apiResource('venues', VenueController::class)->only(['index', 'show']);
