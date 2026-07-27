<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    MeController,
};

Route::get('me', MeController::class)->name('user.me');
