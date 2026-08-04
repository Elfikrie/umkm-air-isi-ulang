<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'pelanggan'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('produk', 'produk')
    ->middleware(['auth', 'pelanggan'])
    ->name('pelanggan');

require __DIR__.'/auth.php';
