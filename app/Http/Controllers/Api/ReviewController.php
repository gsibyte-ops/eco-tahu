<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Review;
use App\Notifications\UpdatePesanan;
use App\Support\MediaGuard;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for a specific item (Produk Tahu or Limbah).
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:produk,tahu,limbah',
            'item_id'   => 'required|integer',
        ]);

        $modelClass = in_array(strtolower($validated['item_type']), ['produk', 'tahu'])
            ? ProdukTahu::class
            : Limbah::class;

        $reviews = Review::with('user:id,username,avatar')
            ->where('reviewable_type', $modelClass)
            ->where('reviewable_id', $validated['item_id'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($rev) {
                return [
                    'id'          => $rev->id,
                    'user_name'   => $rev->user->username ?? 'Pelanggan',
                    'user_avatar' => $rev->user?->avatar_url,
                    'rating'      => $rev->rating,
                    'komentar'    => $rev->komentar,
                    'media_url'   => $rev->media_url,
                    'media_type'  => $rev->media_type,
                    'created_at'  => $rev->created_at->format('d M Y'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $reviews,
        ]);
    }

    /**
     * Submit a review for an item in a completed order.
     * Supports both ProdukTahu and Limbah.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id'      => 'required|integer',
            'reviewable_type' => 'required|in:produk,tahu,limbah',
            'reviewable_id'   => 'required|integer',
            'rating'          => 'required|integer|min:1|max:5',
            'komentar'        => 'nullable|string|max:500',
            'media'           => MediaGuard::photoOrVideoRules(false, 5120, 20480),
        ], [
            'pesanan_id.required'      => 'Pesanan ID wajib diisi.',
            'reviewable_type.required' => 'Tipe produk wajib diisi (produk/limbah).',
            'reviewable_id.required'   => 'Item wajib diisi.',
            'rating.required'          => 'Rating bintang wajib diisi (1-5).',
            'rating.min'               => 'Rating minimal 1 bintang.',
            'rating.max'               => 'Rating maksimal 5 bintang.',
        ]);

        $user = $request->user();

        $pesanan = Pesanan::where('id', $validated['pesanan_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$pesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if (strtolower($pesanan->order_status) !== 'selesai') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda hanya bisa memberi ulasan untuk pesanan yang sudah selesai.',
            ], 422);
        }

        $modelClass = in_array(strtolower($validated['reviewable_type']), ['produk', 'tahu'])
            ? ProdukTahu::class
            : Limbah::class;

        $item = $modelClass::find($validated['reviewable_id']);
        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        $adaDiPesanan = $pesanan->detail()
            ->where(function ($q) use ($modelClass, $validated) {
                $q->where('item_type', $modelClass)
                  ->orWhere('item_type', in_array(strtolower($validated['reviewable_type']), ['produk', 'tahu']) ? 'tahu' : 'limbah');
            })
            ->where('item_id', $item->id)
            ->exists();

        if (!$adaDiPesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda hanya bisa memberi ulasan untuk produk yang dibeli pada pesanan ini.',
            ], 422);
        }

        $sudahReview = Review::where('user_id', $user->id)
            ->where('pesanan_id', $pesanan->id)
            ->where('reviewable_type', $modelClass)
            ->where('reviewable_id', $item->id)
            ->exists();

        if ($sudahReview) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda sudah memberi ulasan untuk item ini pada pesanan ini.',
            ], 422);
        }

        $mediaPath = null;
        $mediaType = null;
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mediaType = MediaGuard::detectMediaType($file);
            $mediaPath = $file->store('reviews', 'public');
        }

        $review = Review::create([
            'user_id'         => $user->id,
            'pesanan_id'      => $pesanan->id,
            'reviewable_type' => $modelClass,
            'reviewable_id'   => $item->id,
            'rating'          => $validated['rating'],
            'komentar'        => $validated['komentar'] ?? null,
            'media_path'      => $mediaPath,
            'media_type'      => $mediaType,
        ]);

        $namaItem = $item->nama_produk ?? $item->nama_limbah ?? 'produk';
        $user->notify(new UpdatePesanan(
            "Ulasan Anda untuk {$namaItem} berhasil dikirim!",
            route('user.pesanan.show', $pesanan->kode_pesanan)
        ));

        return response()->json([
            'status'  => 'success',
            'message' => 'Ulasan berhasil dikirim. Terima kasih!',
            'data'    => $review,
        ], 201);
    }
}
