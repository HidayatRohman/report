<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::get('dashboard', function () {
    $brands = \App\Models\Brand::all(['id', 'nama_brand', 'pemilik', 'logo_path']);
    $transaksis = \App\Models\Transaksi::orderBy('tanggal', 'desc')->get();
    
    return Inertia::render('Dashboard', [
        'brands' => $brands,
        'transaksis' => $transaksis
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Brand routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('brands', BrandController::class);
    Route::get('brand-list', [BrandController::class, 'index'])->name('brand.list');
    Route::post('brand-input', [BrandController::class, 'store'])->name('brand.store');
    
    // Transaksi routes
    Route::resource('transaksis', TransaksiController::class);
    Route::put('transaksis/{id}', [TransaksiController::class, 'update'])->name('transaksis.update');
});

Route::get('brand-input', [BrandController::class, 'create'])->middleware(['auth', 'verified'])->name('brand.input');

Route::get('transaksi-input', [TransaksiController::class, 'create'])->middleware(['auth', 'verified'])->name('transaksi.input');
Route::post('transaksi-input', [TransaksiController::class, 'store'])->middleware(['auth', 'verified'])->name('transaksi.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
