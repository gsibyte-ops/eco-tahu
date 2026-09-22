<?php

namespace App\Http\Controllers;

use App\Models\Edukasi;
use App\Models\Kategori;
use App\Models\Limbah;
use App\Models\ProdukTahu;

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

        // Kategori produk tahu
        $kategori = Kategori::where('tipe', 'produk_tahu')->limit(4)->get();

        // Artikel edukasi terbaru
        $edukasi = Edukasi::where('status', 'publish')
            ->orderByDesc('tanggal_mengunggah')
            ->limit(3)
            ->get();

        return view('user.home', compact('produkPilihan', 'limbahPilihan', 'kategori', 'edukasi'));
    }
}