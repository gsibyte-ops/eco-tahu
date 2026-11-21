<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EdukasiController as AdminEdukasiController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LimbahController as AdminLimbahController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\ProdukTahuController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\ResepController as AdminResepController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\EdukasiController;
use App\Http\Controllers\User\LimbahController;
use App\Http\Controllers\User\NotificationController; 
use App\Http\Controllers\User\PesananController;
use App\Http\Controllers\User\ProdukController;
use App\Http\Controllers\User\ResepController;
use App\Http\Controllers\User\ReviewController;
use Illuminate\Support\Facades\Route;

// ============================
// PUBLIC
// ============================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ============================
// PUBLIC KATALOG (Bisa diakses tanpa login)
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

Route::prefix('resep')->name('user.resep.')->group(function () {
    Route::get('/', [ResepController::class, 'index'])->name('index');
    Route::get('/{slug}', [ResepController::class, 'show'])->name('show');
});

// ============================
// GOOGLE OAUTH (PUBLIC)
// ============================
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// ============================
// USER ACTION (Wajib Login)
// ============================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Notifikasi User (LENGKAP)
    Route::prefix('notifications')->name('user.notif.')->group(function () {
        Route::get('/fetch', [NotificationController::class, 'index'])->name('fetch');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllRead');
        Route::get('/{id}/read', [NotificationController::class, 'readOne'])->name('readOne');
    });

    Route::prefix('cart')->name('user.cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::put('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });

    Route::prefix('checkout')->name('user.checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/cek-promo', [CheckoutController::class, 'cekPromo'])->name('cekPromo');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('/success/{kode}', [CheckoutController::class, 'success'])->name('success');
        Route::post('/upload-bukti/{kode}', [CheckoutController::class, 'uploadBukti'])->name('uploadBukti');
    });

    Route::prefix('pesanan')->name('user.pesanan.')->group(function () {
        Route::get('/', [PesananController::class, 'index'])->name('index');
        Route::get('/{kode}', [PesananController::class, 'show'])->name('show');
        Route::post('/{kode}/cancel', [PesananController::class, 'cancel'])->name('cancel');
        Route::post('/{kode}/upload-bukti', [PesananController::class, 'uploadBukti'])->name('uploadBukti');
        Route::get('/{kode}/tracking', [PesananController::class, 'tracking'])->name('tracking');
    });

    Route::prefix('review')->name('user.review.')->group(function () {
        Route::post('/pesanan/{pesanan}', [ReviewController::class, 'store'])->name('store');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    Route::post('/edukasi/{edukasi}/like', [EdukasiController::class, 'toggleLike'])->name('user.edukasi.like');
    Route::post('/resep/{resep}/like', [ResepController::class, 'toggleLike'])->name('user.resep.like');
});

// ============================
// ADMIN
// ============================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    // Route Notifikasi Admin (LENGKAP)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/fetch', [AdminNotificationController::class, 'index'])->name('fetch');
        Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('markAll');
        Route::get('/{id}/read', [AdminNotificationController::class, 'readOne'])->name('read');
    });

    Route::resource('kategori', KategoriController::class);
    Route::resource('produk-tahu', ProdukTahuController::class);
    Route::resource('limbah', AdminLimbahController::class);

    Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [AdminPesananController::class, 'show'])->name('pesanan.show');
    Route::patch('/pesanan/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
    Route::post('/pesanan/{pesanan}/verifikasi', [AdminPesananController::class, 'verifikasiPembayaran'])->name('pesanan.verifikasi');
    Route::put('/pesanan/{pesanan}/batalkan', [AdminPesananController::class, 'batalkan'])->name('pesanan.batalkan');

    Route::post('/pesanan/{pesanan}/start-tracking', [AdminPesananController::class, 'startTracking'])->name('pesanan.startTracking');
    Route::post('/pesanan/{pesanan}/update-lokasi', [AdminPesananController::class, 'updateLokasi'])->name('pesanan.updateLokasi');
    Route::post('/pesanan/{pesanan}/stop-tracking', [AdminPesananController::class, 'stopTracking'])->name('pesanan.stopTracking');

    Route::get('/refund', [RefundController::class, 'index'])->name('refund.index');
    Route::get('/refund/{refund}', [RefundController::class, 'show'])->name('refund.show');
    Route::put('/refund/{refund}', [RefundController::class, 'update'])->name('refund.update');
    Route::delete('/refund/{refund}', [RefundController::class, 'destroy'])->name('refund.destroy');

    Route::get('/pelanggan/export', [PelangganController::class, 'export'])->name('pelanggan.export');
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');

    Route::resource('edukasi', AdminEdukasiController::class);
    Route::resource('resep', AdminResepController::class);

    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

// ============================
// HALAMAN STATIS (PUBLIC)
// ============================
Route::view('/bantuan', 'pages.bantuan')->name('halaman.bantuan');
Route::view('/cara-pesan', 'pages.cara-pesan')->name('halaman.cara-pesan');
Route::view('/pengiriman', 'pages.pengiriman')->name('halaman.pengiriman');
Route::view('/kebijakan-refund', 'pages.refund')->name('halaman.refund');
Route::view('/faq', 'pages.faq')->name('halaman.faq');
Route::view('/syarat-ketentuan', 'pages.snk')->name('halaman.snk');
Route::view('/kebijakan-privasi', 'pages.privasi')->name('halaman.privasi');
Route::view('/sitemap', 'pages.sitemap')->name('halaman.sitemap');

// Proxy route untuk Flutter Mobile yang memanggil /files/{path} dengan CORS & realpath guard
Route::match(['GET', 'OPTIONS'], '/files/{path}', [\App\Http\Controllers\FileProxyController::class, 'show'])
    ->where('path', '.*')
    ->withoutMiddleware('web');

require __DIR__.'/auth.php';