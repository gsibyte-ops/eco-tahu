<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;

class EdukasiController extends Controller
{
    public function index()
    {
        $query = Edukasi::with('user')
            ->where('status', 'publish')
            ->orderByDesc('tanggal_mengunggah');

        if (request('q')) {
            $query->where('judul', 'like', '%' . request('q') . '%');
        }

        $edukasi = $query->paginate(9)->withQueryString();

        // Artikel terbaru untuk sidebar
        $terbaru = Edukasi::where('status', 'publish')
            ->orderByDesc('tanggal_mengunggah')
            ->limit(5)
            ->get();

        return view('user.edukasi.index', compact('edukasi', 'terbaru'));
    }

    public function show($slug)
    {
        $artikel = Edukasi::with('user')
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->firstOrFail();

        // Artikel terkait (exclude current)
        $terkait = Edukasi::where('status', 'publish')
            ->where('id', '!=', $artikel->id)
            ->orderByDesc('tanggal_mengunggah')
            ->limit(3)
            ->get();

        return view('user.edukasi.show', compact('artikel', 'terkait'));
    }
}