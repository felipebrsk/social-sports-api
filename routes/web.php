<?php

use Illuminate\Support\Facades\Route;

Route::get('email/confirmado', function () {
    return view('email-confirmed');
});
