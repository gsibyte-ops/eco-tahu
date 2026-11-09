<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukTahuController;

// ==========================================
// PUBLIC ROUTES (gak butuh token)
// ==========================================
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// PROTECTED ROUTES (wajib token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD Produk Tahu (diproteksi)
    Route::apiResource('produk-tahu', ProdukTahuController::class);
});