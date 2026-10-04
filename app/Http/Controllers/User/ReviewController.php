<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($pesanan->order_status !== 'selesai') {
            return back()->with('error', 'Anda hanya bisa memberi ulasan untuk pesanan yang sudah selesai.');
        }

        $validated = $request->validate([
            'reviewable_type' => 'required|in:produk,limbah',
            'reviewable_id'   => 'required|integer',
            'rating'          => 'required|integer|min:1|max:5',
            'komentar'        => 'nullable|string|max:500',
        ], [
            'reviewable_type.required' => 'Tipe item tidak valid.',
            'reviewable_type.in'       => 'Tipe item harus produk atau limbah.',
            'reviewable_id.required'   => 'Item tidak valid.',
            'rating.required'          => 'Rating wajib diisi.',
            'rating.integer'           => 'Rating harus berupa angka.',
            'rating.min'               => 'Rating minimal 1 bintang.',
            'rating.max'               => 'Rating maksimal 5 bintang.',
            'komentar.max'             => 'Komentar maksimal 500 karakter.',
        ]);

        $modelClass = $validated['reviewable_type'] === 'produk'
            ? ProdukTahu::class
            : Limbah::class;

        $item = $modelClass::findOrFail($validated['reviewable_id']);

        $adaDiPesanan = $pesanan->detail()
            ->where('item_type', $modelClass)
            ->where('item_id', $item->id)
            ->exists();

        if (! $adaDiPesanan) {
            return back()->with('error', 'Anda hanya bisa memberi ulasan untuk produk yang Anda beli.');
        }

        $sudahReview = Review::where('user_id', Auth::id())
            ->where('pesanan_id', $pesanan->id)
            ->where('reviewable_type', $modelClass)
            ->where('reviewable_id', $item->id)
            ->exists();

        if ($sudahReview) {
            return back()->with('error', 'Anda sudah memberi ulasan untuk produk ini.');
        }

        Review::create([
            'user_id'         => Auth::id(),
            'pesanan_id'      => $pesanan->id,
            'reviewable_type' => $modelClass,
            'reviewable_id'   => $item->id,
            'rating'          => $validated['rating'],
            'komentar'        => $validated['komentar'] ?? null,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}