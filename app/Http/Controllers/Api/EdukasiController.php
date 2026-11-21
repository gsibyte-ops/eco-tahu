<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdukasiController extends Controller
{
    private function formatArticle(Edukasi $e, ?User $user, ?array $likedIds = null): array
    {
        return [
            'id'                    => $e->id,
            'judul'                 => $e->judul,
            'slug'                  => $e->slug,
            'konten'                => $e->konten,
            'thumbnail'             => $e->thumbnail,
            'thumbnail_url'         => $e->thumbnail_url,
            'media_type'            => $e->media_type ?? 'image',
            'video_url'             => $e->video_url,
            'youtube_url'           => $e->youtube_url,
            'youtube_embed_url'     => $e->youtube_embed_url,
            'youtube_thumbnail_url' => $e->youtube_thumbnail_url,
            'display_thumbnail_url' => $e->display_thumbnail_url,
            'share_url'             => $e->share_url,
            'author_name'           => $e->user->username ?? 'Admin EcoTahu',
            'likes_count'           => (int) ($e->likes_count ?? $e->likes()->count()),
            'is_liked'              => $likedIds !== null
                ? in_array($e->id, $likedIds, true)
                : $e->isLikedBy($user),
            'created_at'            => $e->created_at,
            'published_at'          => $e->published_at,
        ];
    }

    /**
     * GET /api/edukasi
     * Tampilkan artikel edukasi yang sudah publish / visible.
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $artikel = Edukasi::with('user:id,username')
            ->withCount('likes')
            ->visibleToUser()
            ->orderByDesc('id')
            ->get();

        $likedIds = $user
            ? DB::table('edukasi_likes')->where('user_id', $user->id)->pluck('edukasi_id')->all()
            : [];

        $data = $artikel->map(fn ($e) => $this->formatArticle($e, $user, $likedIds))->values();

        return response()->json([
            'success' => true,
            'message' => 'Daftar artikel edukasi',
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /api/edukasi/{id}
     * Tampilkan satu artikel edukasi.
     */
    public function show(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $artikel = Edukasi::with('user:id,username')
            ->withCount('likes')
            ->visibleToUser()
            ->find($id);

        if (!$artikel) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatArticle($artikel, $user),
        ], 200);
    }

    /**
     * POST /api/edukasi/{id}/like
     * Toggle suka artikel edukasi untuk user login.
     */
    public function toggleLike(Request $request, $id)
    {
        $artikel = Edukasi::visibleToUser()->find($id);
        if (!$artikel) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan',
            ], 404);
        }

        $user = $request->user();
        $exists = $artikel->likes()->where('user_id', $user->id)->exists();

        if ($exists) {
            $artikel->likes()->detach($user->id);
            $liked = false;
        } else {
            $artikel->likes()->attach($user->id);
            $liked = true;
        }

        $count = $artikel->likes()->count();

        return response()->json([
            'success'     => true,
            'liked'       => $liked,
            'likes_count' => $count,
            'message'     => $liked ? 'Artikel disukai!' : 'Suka dibatalkan.',
        ], 200);
    }
}
