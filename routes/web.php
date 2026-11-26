<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// 1. Rute Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt')->middleware('throttle:5,1');
});

// 2. Rute Logout
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// 3. Rute Setelah Login (Auth)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/dashboard', DashboardController::class);

    // Master Data
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class)->except('show');
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('users', UserController::class)->except('show');

    // Pengaturan Toko
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // MODUL KASIR & RIWAYAT TRANSAKSI
    Route::get('/kasir', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/kasir', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    // FITUR BONUS: VOID (PEMBATALAN) TRANSAKSI
    Route::post('/sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');

    // MODUL LAPORAN
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // FITUR BONUS: EXPORT EXCEL
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // FITUR BONUS: LOG AKTIVITAS (B3)
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('/logs/{activityLog}', [ActivityLogController::class, 'show'])->name('logs.show');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
