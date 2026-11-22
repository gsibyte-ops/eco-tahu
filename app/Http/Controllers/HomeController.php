<?php

namespace App\Http\Controllers;

use App\Models\Edukasi;
use App\Models\Kategori;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Resep;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        // Produk pilihan (tahu) - Urutkan berdasarkan yang paling banyak di-review
        $produkPilihan = ProdukTahu::with('kategori')
            ->where('status', 'aktif')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_count')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        // Limbah pilihan - Sama, urutkan berdasarkan review terbanyak
        $limbahPilihan = Limbah::with('kategori')
            ->where('status', 'aktif')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_count')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        // Kategori — ambil semua (produk_tahu + limbah), max 4, dengan count produk
        $kategori = Kategori::withCount(['produkTahu', 'limbah'])
            ->orderBy('id')
            ->limit(4)
            ->get();

        // Artikel edukasi terbaru
        $edukasi = Edukasi::withCount('likes')
            ->visibleToUser()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // Resep olahan tahu terbaru
        $resepTerbaru = Resep::withCount('likes')
            ->visibleToUser()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        // STATS REAL untuk hero
        $stats = [
            'total_pesanan_selesai' => Pesanan::where('order_status', 'selesai')->count(),
            'total_review'          => Review::count(),
            'rating_rata'           => round((float) (Review::avg('rating') ?? 0), 1),
            'total_produk'          => ProdukTahu::where('status', 'aktif')->count()
                                     + Limbah::where('status', 'aktif')->count(),
        ];

        // TESTIMONI REAL dari review (rating >= 4), prioritaskan user berbeda jika mencukupi
        $recentReviews = Review::with(['user', 'reviewable'])
            ->where('rating', '>=', 4)
            ->whereNotNull('komentar')
            ->where('komentar', '!=', '')
            ->orderByDesc('created_at')
            ->limit(24)
            ->get();

        $distinctUsers = $recentReviews->unique('user_id')->values();
        $testimoniReal = $distinctUsers->count() >= 3
            ? $distinctUsers->take(8)
            : $recentReviews->take(8);

        return view('user.home', compact(
            'produkPilihan',
            'limbahPilihan',
            'kategori',
            'edukasi',
            'resepTerbaru',
            'stats',
            'testimoniReal'
        ));
    }
}