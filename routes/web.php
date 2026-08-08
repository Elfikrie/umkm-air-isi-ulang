<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'pelanggan'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Volt::route('produk', 'produk')
    ->middleware(['auth', 'pelanggan'])
    ->name('produk.index');

require __DIR__.'/auth.php';
