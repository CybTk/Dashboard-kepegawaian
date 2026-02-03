<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KepegawaianController;

Route::get('/', function () {
    return view('welcome');
});

// Tambahkan ->name('dashboard') di akhir agar sinkron dengan JavaScript
Route::get('/dashboard', [KepegawaianController::class, 'index'])->name('dashboard');