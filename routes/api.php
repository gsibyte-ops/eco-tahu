<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProdukTahuController;
use App\Http\Controllers\Api\LimbahController;
use App\Http\Controllers\Api\EdukasiController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ResepController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\TokoController;

// ==========================================
// PUBLIC ROUTES (tidak butuh token)
// ==========================================
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/auth/google', [AuthController::class, 'googleAuth']);
});

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Info Toko & Kalkulasi Ongkir
Route::get('/toko', [TokoController::class, 'info']);
Route::post('/toko/hitung-ongkir', [TokoController::class, 'hitungOngkir']);

// Katalog Produk Tahu (Public)
Route::get('/produk-tahu', [ProdukTahuController::class, 'index']);
Route::get('/produk-tahu/{id}', [ProdukTahuController::class, 'show']);

// Katalog Produk Limbah (Public)
Route::get('/limbah', [LimbahController::class, 'index']);
Route::get('/limbah/{id}', [LimbahController::class, 'show']);

// Artikel Edukasi (Public)
Route::get('/edukasi', [EdukasiController::class, 'index']);
Route::get('/edukasi/{id}', [EdukasiController::class, 'show']);

// Resep Olahan Tahu (Public)
Route::get('/resep', [ResepController::class, 'index']);
Route::get('/resep/{id}', [ResepController::class, 'show']);

// Ulasan / Reviews (Public View)
Route::get('/reviews', [ReviewController::class, 'index']);

// ==========================================
// PROTECTED ROUTES (wajib token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    // User info, Profile & Logout
    Route::get('/user', [AuthController::class, 'me']);
    Route::match(['put', 'post'], '/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);
    Route::delete('/user', [AuthController::class, 'deleteAccount'])
        ->middleware('no-admin-customer-action');
    Route::post('/logout', [AuthController::class, 'logout']);

    // Keranjang Belanja (Persistent DB)
    Route::get('/promo/first-order', [TokoController::class, 'cekPromo']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store'])
        ->middleware('no-admin-customer-action');
    Route::put('/cart', [CartController::class, 'update'])
        ->middleware('no-admin-customer-action');
    Route::delete('/cart/item', [CartController::class, 'removeItem'])
        ->middleware('no-admin-customer-action');
    Route::delete('/cart', [CartController::class, 'clear'])
        ->middleware('no-admin-customer-action');

    // Pesanan / Order Management
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);
    Route::post('/pesanan', [PesananController::class, 'store'])
        ->middleware('no-admin-customer-action');
    Route::post('/pesanan/{id}/cancel', [PesananController::class, 'cancel'])
        ->middleware('no-admin-customer-action');
    Route::post('/pesanan/{id}/upload-bukti', [PesananController::class, 'uploadBukti'])
        ->middleware('no-admin-customer-action');
    Route::get('/pesanan/{id}/tracking', [PesananController::class, 'tracking']);

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Beri Ulasan / Review
    Route::post('/reviews', [ReviewController::class, 'store'])
        ->middleware('no-admin-customer-action');

    // Like Artikel Edukasi
    Route::post('/edukasi/{id}/like', [EdukasiController::class, 'toggleLike'])
        ->middleware('no-admin-customer-action');

    // Like Resep Olahan Tahu
    Route::post('/resep/{id}/like', [ResepController::class, 'toggleLike'])
        ->middleware('no-admin-customer-action');

    // Admin-only API endpoints (wajib role admin)
    Route::middleware('admin')->group(function () {
        // Admin Overview (Read-Only untuk Mobile)
        Route::get('/admin/overview', [AdminDashboardController::class, 'overview']);

        // Admin CRUD Produk Tahu
        Route::post('/produk-tahu', [ProdukTahuController::class, 'store']);
        Route::put('/produk-tahu/{id}', [ProdukTahuController::class, 'update']);
        Route::patch('/produk-tahu/{id}', [ProdukTahuController::class, 'update']);
        Route::delete('/produk-tahu/{id}', [ProdukTahuController::class, 'destroy']);
    });
});
