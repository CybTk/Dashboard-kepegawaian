<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KepegawaianController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [KepegawaianController::class, 'index'])->name('dashboard');