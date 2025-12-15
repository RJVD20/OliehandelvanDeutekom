<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('themes.default.pages.home');
});