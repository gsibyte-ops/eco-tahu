<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    private function formatResep(Resep $r, ?User $user, ?array $likedIds = null): array
    {
        return [
            'id'                    => $r->id,
            'judul'                 => $r->judul,
            'slug'                  => $r->slug,
            'deskripsi'             => $r->deskripsi,
            'bahan'                 => $r->bahan,
            'bahan_list'            => $r->bahanList(),
            'langkah'               => $r->langkah,
            'langkah_list'          => $r->langkahList(),
            'durasi_menit'          => (int) $r->durasi_menit,
            'porsi'                 => (int) $r->porsi,
            'tingkat_kesulitan'     => $r->tingkat_kesulitan,
            'thumbnail'             => $r->thumbnail,
            'thumbnail_url'         => $r->thumbnail_url,
            'media_type'            => $r->media_type ?? 'image',
            'video_url'             => $r->video_url,
            'youtube_url'           => $r->youtube_url,
            'youtube_embed_url'     => $r->youtube_embed_url,
            'youtube_thumbnail_url' => $r->youtube_thumbnail_url,
            'display_thumbnail_url' => $r->display_thumbnail_url,
            'share_url'             => $r->share_url,
            'author_name'           => $r->user->username ?? 'Admin EcoTahu',
            'likes_count'           => (int) ($r->likes_count ?? $r->likes()->count()),
            'is_liked'              => $likedIds !== null
                ? in_array($r->id, $likedIds, true)
                : $r->isLikedBy($user),
            'created_at'            => $r->created_at,
            'published_at'          => $r->published_at,
        ];
    }

    /**
     * GET /api/resep
     * Tampilkan daftar resep olahan tahu yang sudah publish / visible.
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $query = Resep::with('user:id,username')
            ->withCount('likes')
            ->visibleToUser()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($sub) use ($term) {
                $sub->where('judul', 'like', '%' . $term . '%')
                    ->orWhere('bahan', 'like', '%' . $term . '%');
            });
        }

        if ($request->filled('tingkat') && in_array($request->tingkat, ['mudah', 'sedang', 'sulit'], true)) {
            $query->where('tingkat_kesulitan', $request->tingkat);
        }

        $resepList = $query->get();

        $likedIds = $user
            ? DB::table('resep_likes')->where('user_id', $user->id)->pluck('resep_id')->all()
            : [];

        $data = $resepList->map(fn ($r) => $this->formatResep($r, $user, $likedIds))->values();

        return response()->json([
            'success' => true,
            'message' => 'Daftar resep olahan tahu',
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /api/resep/{id}
     * Tampilkan detail satu resep olahan tahu.
     */
    public function show(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $resep = Resep::with('user:id,username')
            ->withCount('likes')
            ->visibleToUser()
            ->find($id);

        if (!$resep) {
            return response()->json([
                'success' => false,
                'message' => 'Resep tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatResep($resep, $user),
        ], 200);
    }

    /**
     * POST /api/resep/{id}/like
     * Toggle suka resep untuk user login.
     */
    public function toggleLike(Request $request, $id)
    {
        $resep = Resep::visibleToUser()->find($id);
        if (!$resep) {
            return response()->json([
                'success' => false,
                'message' => 'Resep tidak ditemukan',
            ], 404);
        }

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

        return response()->json([
            'success'     => true,
            'liked'       => $liked,
            'likes_count' => $count,
            'message'     => $liked ? 'Resep disukai!' : 'Suka dibatalkan.',
        ], 200);
    }
}
