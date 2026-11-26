<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Resep;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    public function index(Request $request)
    {
        $query = Resep::with('user')
            ->withCount('likes')
            ->visibleToUser()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $query->where('judul', 'like', '%' . $term . '%');
        }

        if ($request->filled('tingkat') && in_array($request->tingkat, ['mudah', 'sedang', 'sulit'], true)) {
            $query->where('tingkat_kesulitan', $request->tingkat);
        }

        $resep = $query->paginate(9)->withQueryString();

        $terbaru = Resep::withCount('likes')
            ->visibleToUser()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('user.resep.index', compact('resep', 'terbaru'));
    }

    public function show($slug)
    {
        $resep = Resep::with('user')
            ->withCount('likes')
            ->visibleToUser()
            ->where('slug', $slug)
            ->firstOrFail();

        $isLiked = auth()->check() ? $resep->isLikedBy(auth()->user()) : false;

        $terkait = Resep::withCount('likes')
            ->visibleToUser()
            ->where('id', '!=', $resep->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view('user.resep.show', compact('resep', 'terkait', 'isLiked'));
    }

    public function toggleLike(Request $request, Resep $resep)
    {
        $user = $request->user();
        $exists = $resep->likes()->where('user_id', $user->id)->exists();

        if ($exists) {
            $resep->likes()->detach($user->id);
            $liked = false;
        } else {
            $resep->likes()->attach($user->id);
            $liked = true;
        }

        $count = $resep->likes()->count();

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
