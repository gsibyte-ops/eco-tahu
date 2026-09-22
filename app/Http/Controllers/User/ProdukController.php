<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\ProdukTahu;

class ProdukController extends Controller
{
    public function index()
    {
        $query = ProdukTahu::with('kategori')->where('status', 'aktif');

        if (request('kategori')) {
            $query->where('kategori_id', request('kategori'));
        }

        if (request('q')) {
            $query->where('nama_produk', 'like', '%' . request('q') . '%');
        }

        if (request('sort') === 'termurah') {
            $query->orderBy('harga');
        } elseif (request('sort') === 'termahal') {
            $query->orderByDesc('harga');
        } else {
            $query->orderByDesc('id');
        }

        $produk = $query->paginate(12)->withQueryString();
        $kategori = Kategori::where('tipe', 'produk_tahu')->get();

        return view('user.produk.index', compact('produk', 'kategori'));
    }

    public function show($slug)
    {
        $produk = ProdukTahu::with('kategori')
            ->where('slug', $slug)
            ->where('status', 'aktif')
            ->firstOrFail();

        $related = ProdukTahu::where('status', 'aktif')
            ->where('id', '!=', $produk->id)
            ->where('kategori_id', $produk->kategori_id)
            ->limit(4)
            ->get();

        return view('user.produk.show', compact('produk', 'related'));
    }
}