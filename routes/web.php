<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AppSettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Get app settings for logo and branding
    $appSettings = [
        'app_name' => \App\Models\AppSetting::get('app_name', 'Kelola Pusat App'),
        'logo' => \App\Models\AppSetting::get('logo'),
        'favicon' => \App\Models\AppSetting::get('favicon'),
    ];
    
    // Get brands for display on welcome page
    $brands = \App\Models\Brand::all(['id', 'nama_brand', 'pemilik', 'logo_path']);
    
    return Inertia::render('Welcome', [
        'appSettings' => $appSettings,
        'brands' => $brands,
    ]);
})->name('home');


Route::get('dashboard', function () {
    $brands = \App\Models\Brand::all(['id', 'nama_brand', 'pemilik', 'logo_path']);
    $transaksis = \App\Models\Transaksi::orderBy('tanggal', 'desc')->get();
    
    return Inertia::render('Dashboard', [
        'brands' => $brands,
        'transaksis' => $transaksis,
        'userRole' => auth()->user()->role,
        'canEdit' => auth()->user()->canEdit(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Brand routes - Owner can view but not edit, manajer/spv/karyawan can edit
Route::middleware(['auth', 'verified', 'role:owner,manajer,spv,karyawan'])->group(function () {
    Route::get('brand-list', [BrandController::class, 'index'])->name('brand.list');
    Route::get('brand-input', [BrandController::class, 'create'])->name('brand.input');
});

// Brand edit routes - require edit access (not Owner)
Route::middleware(['auth', 'verified', 'role:manajer,spv,karyawan'])->group(function () {
    Route::resource('brands', BrandController::class)->except(['index', 'create', 'show']);
    Route::post('brand-input', [BrandController::class, 'store'])->name('brand.store');
});

// Transaksi routes - Owner can view but not edit, manajer/spv/karyawan can edit
Route::middleware(['auth', 'verified', 'role:owner,manajer,spv,karyawan'])->group(function () {
    Route::get('transaksi-input', [TransaksiController::class, 'create'])->name('transaksi.input');
    Route::get('transaksi-list', [TransaksiController::class, 'index'])->name('transaksi.list');
});

// Transaksi edit routes - require edit access (not Owner)
Route::middleware(['auth', 'verified', 'role:manajer,spv,karyawan'])->group(function () {
    Route::resource('transaksis', TransaksiController::class)->except(['index', 'create', 'show']);
    Route::put('transaksis/{id}', [TransaksiController::class, 'update'])->name('transaksis.update');
    Route::get('transaksi-input', [TransaksiController::class, 'create'])->name('transaksi.input');
    Route::post('transaksi-input', [TransaksiController::class, 'store'])->name('transaksi.store');
    
    // App Settings routes
    Route::get('settings/logo', [AppSettingController::class, 'logoSettings'])->name('settings.logo');
    Route::post('settings/logo', [AppSettingController::class, 'updateLogo'])->name('settings.logo.update');
    Route::delete('settings/logo', [AppSettingController::class, 'deleteLogo'])->name('settings.logo.delete');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
