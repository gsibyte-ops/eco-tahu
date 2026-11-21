<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;

class EdukasiController extends Controller
{
    public function index()
    {
        $query = Edukasi::with('user')
            ->withCount('likes')
            ->visibleToUser()
            ->orderByDesc('published_at');

        if (request('q')) {
            $query->where('judul', 'like', '%' . request('q') . '%');
        }

        $edukasi = $query->paginate(9)->withQueryString();

        $terbaru = Edukasi::withCount('likes')
            ->visibleToUser()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('user.edukasi.index', compact('edukasi', 'terbaru'));
    }

    public function show($slug)
    {
        $artikel = Edukasi::with('user')
            ->withCount('likes')
            ->visibleToUser()
            ->where('slug', $slug)
            ->firstOrFail();

        $isLiked = auth()->check() ? $artikel->isLikedBy(auth()->user()) : false;

        $terkait = Edukasi::withCount('likes')
            ->visibleToUser()
            ->where('id', '!=', $artikel->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('user.edukasi.show', compact('artikel', 'terkait', 'isLiked'));
    }

    public function toggleLike(Request $request, Edukasi $edukasi)
    {
        $user = $request->user();
        $exists = $edukasi->likes()->where('user_id', $user->id)->exists();

        if ($exists) {
            $edukasi->likes()->detach($user->id);
            $liked = false;
        } else {
            $edukasi->likes()->attach($user->id);
            $liked = true;
        }

        $count = $edukasi->likes()->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'     => true,
                'liked'       => $liked,
                'likes_count' => $count,
            ]);
        }

        return back();
    }
}
