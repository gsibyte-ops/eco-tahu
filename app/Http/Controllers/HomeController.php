<?php

namespace App\Http\Controllers;

use App\Models\Edukasi;
use App\Models\Kategori;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        // Produk pilihan (tahu) - 4 terbaru
        $produkPilihan = ProdukTahu::where('status', 'aktif')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        // Limbah pilihan - 4 terbaru
        $limbahPilihan = Limbah::where('status', 'aktif')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        // Kategori — ambil semua (produk_tahu + limbah), max 4, dengan count produk
        $kategori = Kategori::withCount(['produkTahu', 'limbah'])
            ->orderBy('id')
            ->limit(4)
            ->get();

        // Artikel edukasi terbaru (pakai scope biar scheduled yang udah waktunya masuk)
        $edukasi = Edukasi::visibleToUser()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // ============================================================
        // STATS REAL untuk hero
        // ============================================================
        $stats = [
            'total_pesanan_selesai' => Pesanan::where('order_status', 'selesai')->count(),
            'total_review'          => Review::count(),
            'rating_rata'           => round((float) (Review::avg('rating') ?? 0), 1),
            'total_produk'          => ProdukTahu::where('status', 'aktif')->count()
                                     + Limbah::where('status', 'aktif')->count(),
        ];

        // ============================================================
        // TESTIMONI REAL dari review (rating >= 4)
        // ============================================================
        $testimoniReal = Review::with('user')
            ->where('rating', '>=', 4)
            ->whereNotNull('komentar')
            ->where('komentar', '!=', '')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('user.home', compact(
            'produkPilihan',
            'limbahPilihan',
            'kategori',
            'edukasi',
            'stats',
            'testimoniReal'
        ));
    }
}