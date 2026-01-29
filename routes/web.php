<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KepegawaianController;

Route::get('/', function () {
    return view('welcome');
});

// HARUS lewat controller agar variabel $units terisi
Route::get('/dashboard', [KepegawaianController::class, 'index']);