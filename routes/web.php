<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EdukasiController as AdminEdukasiController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LimbahController as AdminLimbahController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\ProdukTahuController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\EdukasiController;
use App\Http\Controllers\User\LimbahController;
use App\Http\Controllers\User\PesananController;
use App\Http\Controllers\User\ProdukController;
use Illuminate\Support\Facades\Route;

// ============================
// PUBLIC
// ============================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ============================
// PUBLIC CATALOG
// ============================
Route::prefix('produk')->name('user.produk.')->group(function () {
    Route::get('/', [ProdukController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProdukController::class, 'show'])->name('show');
});

Route::prefix('limbah')->name('user.limbah.')->group(function () {
    Route::get('/', [LimbahController::class, 'index'])->name('index');
    Route::get('/{slug}', [LimbahController::class, 'show'])->name('show');
});

Route::prefix('edukasi')->name('user.edukasi.')->group(function () {
    Route::get('/', [EdukasiController::class, 'index'])->name('index');
    Route::get('/{slug}', [EdukasiController::class, 'show'])->name('show');
});

// ============================
// USER (Pelanggan) — butuh login
// ============================
Route::middleware('auth')->group(function () {

    // Profil (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart
    Route::prefix('cart')->name('user.cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::put('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // Checkout
    Route::prefix('checkout')->name('user.checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('/success/{kode}', [CheckoutController::class, 'success'])->name('success');
        Route::post('/upload-bukti/{kode}', [CheckoutController::class, 'uploadBukti'])->name('uploadBukti');
    });

    // Riwayat Pesanan
    Route::prefix('pesanan')->name('user.pesanan.')->group(function () {
        Route::get('/', [PesananController::class, 'index'])->name('index');
        Route::get('/{kode}', [PesananController::class, 'show'])->name('show');
        Route::post('/{kode}/cancel', [PesananController::class, 'cancel'])->name('cancel');
        Route::post('/{kode}/upload-bukti', [PesananController::class, 'uploadBukti'])->name('uploadBukti');
    });
});

// ============================
// ADMIN
// ============================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kategori (CRUD)
    Route::resource('kategori', KategoriController::class);

    // Produk Tahu (CRUD)
    Route::resource('produk-tahu', ProdukTahuController::class);

    // Limbah (CRUD)
    Route::resource('limbah', AdminLimbahController::class);

    // Pesanan (custom)
    Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [AdminPesananController::class, 'show'])->name('pesanan.show');
    Route::patch('/pesanan/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
    Route::post('/pesanan/{pesanan}/verifikasi', [AdminPesananController::class, 'verifikasiPembayaran'])->name('pesanan.verifikasi');

    // Refund (custom)
    Route::get('/refund', [RefundController::class, 'index'])->name('refund.index');
    Route::get('/refund/{refund}', [RefundController::class, 'show'])->name('refund.show');
    Route::put('/refund/{refund}', [RefundController::class, 'update'])->name('refund.update');
    Route::delete('/refund/{refund}', [RefundController::class, 'destroy'])->name('refund.destroy');

    // Pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');

    // Edukasi (CRUD)
    Route::resource('edukasi', AdminEdukasiController::class);

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

// ============================
// AUTH (Breeze)
// ============================
require __DIR__.'/auth.php';