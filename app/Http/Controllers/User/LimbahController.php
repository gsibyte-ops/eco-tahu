<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Limbah;

class LimbahController extends Controller
{
    public function index()
    {
        $query = Limbah::with('kategori')->where('status', 'aktif');

        if (request('kategori')) {
            $query->where('kategori_id', request('kategori'));
        }

        if (request('q')) {
            $query->where('nama_limbah', 'like', '%' . request('q') . '%');
        }

        if (request('sort') === 'termurah') {
            $query->orderBy('harga');
        } elseif (request('sort') === 'termahal') {
            $query->orderByDesc('harga');
        } else {
            $query->orderByDesc('id');
        }

        $limbah = $query->paginate(12)->withQueryString();
        $kategori = Kategori::where('tipe', 'limbah')->get();

        return view('user.limbah.index', compact('limbah', 'kategori'));
    }

    public function show($slug)
    {
        $limbah = Limbah::with('kategori')
            ->where('slug', $slug)
            ->where('status', 'aktif')
            ->firstOrFail();

        $related = Limbah::where('status', 'aktif')
            ->where('id', '!=', $limbah->id)
            ->limit(4)
            ->get();

        return view('user.limbah.show', compact('limbah', 'related'));
    }
}