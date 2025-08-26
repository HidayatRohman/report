<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('brand-input', function () {
    return Inertia::render('brand/BrandForm');
})->middleware(['auth', 'verified'])->name('brand.input');

Route::get('transaksi-input', function () {
    return Inertia::render('transaksi/TransaksiInput');
})->middleware(['auth', 'verified'])->name('transaksi.input');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
