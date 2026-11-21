<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Limbah;
use Illuminate\Http\Request;

class LimbahController extends Controller
{
    /**
     * GET /api/limbah
     * Tampilkan semua produk limbah + relasi kategori.
     */
    public function index()
    {
        $limbah = Limbah::with('kategori')
            ->where(function ($q) {
                $q->where('status', 'aktif')
                  ->orWhereNull('status');
            })
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_count')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk limbah',
            'data'    => $limbah,
        ], 200);
    }

    /**
     * GET /api/limbah/{id}
     * Tampilkan satu produk limbah.
     */
    public function show($id)
    {
        $limbah = Limbah::with('kategori')->find($id);

        if (!$limbah) {
            return response()->json([
                'success' => false,
                'message' => 'Produk limbah tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $limbah,
        ], 200);
    }
}
