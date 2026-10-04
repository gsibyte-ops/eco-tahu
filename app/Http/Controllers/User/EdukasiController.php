<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;

class EdukasiController extends Controller
{
    public function index()
    {
        $query = Edukasi::with('user')
            ->visibleToUser()
            ->orderByDesc('published_at');

        if (request('q')) {
            $query->where('judul', 'like', '%' . request('q') . '%');
        }

        $edukasi = $query->paginate(9)->withQueryString();

        // Artikel terbaru untuk sidebar
        $terbaru = Edukasi::visibleToUser()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('user.edukasi.index', compact('edukasi', 'terbaru'));
    }

    public function show($slug)
    {
        $artikel = Edukasi::with('user')
            ->visibleToUser()
            ->where('slug', $slug)
            ->firstOrFail();

        // Artikel terkait (exclude current)
        $terkait = Edukasi::visibleToUser()
            ->where('id', '!=', $artikel->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('user.edukasi.show', compact('artikel', 'terkait'));
    }
}