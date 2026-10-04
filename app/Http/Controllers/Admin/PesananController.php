<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Refund;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['user', 'detail', 'refund', 'pembayaran'])
            ->orderByDesc('tanggal_order');

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('kode_pesanan', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('username', 'like', "%{$q}%"))
                ->orWhereHas('detail', fn ($d) => $d->where('nama_item', 'like', "%{$q}%"));
            });
        }

        $dari = $request->filled('dari') ? $request->dari : null;
        $sampai = $request->filled('sampai') ? $request->sampai : null;

        if ($dari && Carbon::parse($dari)->isFuture()) {
            $dari = now()->format('Y-m-d');
        }
        if ($sampai && Carbon::parse($sampai)->isFuture()) {
            $sampai = now()->format('Y-m-d');
        }

        if ($dari && $sampai && Carbon::parse($dari)->gt(Carbon::parse($sampai))) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        if ($dari) {
            $query->where('tanggal_order', '>=', $dari . ' 00:00:00');
        }
        if ($sampai) {
            $query->where('tanggal_order', '<=', $sampai . ' 23:59:59');
        }

        $pesanan = $query->paginate(10)->withQueryString();

        $stats = [
            'pending' => Pesanan::where('order_status', 'pending')->count(),
            'diproses' => Pesanan::where('order_status', 'diproses')->count(),
            'dikirim' => Pesanan::where('order_status', 'dikirim')->count(),
            'selesai' => Pesanan::where('order_status', 'selesai')->count(),
            'dibatalkan' => Pesanan::where('order_status', 'dibatalkan')->count(),
        ];

        return view('admin.pesanan.index', compact('pesanan', 'stats'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'detail', 'pembayaran', 'refund']);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'order_status' => 'required|in:pending,diproses,dikirim,selesai,dibatalkan',
        ]);

        $data = ['order_status' => $request->order_status];

        // Kalau bukan dikirim lagi, clear lokasi kurir
        if ($request->order_status !== 'dikirim') {
            $data['lat_kurir'] = null;
            $data['lng_kurir'] = null;
            $data['lokasi_updated_at'] = null;
        }

        $pesanan->update($data);

        $refundBaru = null;
        if ($request->order_status === 'dibatalkan'
            && $pesanan->payment_method === 'Transfer'
            && $pesanan->payment_status === 'paid'
            && !$pesanan->refund) {

            $refundBaru = Refund::create([
                'pesanan_id' => $pesanan->id,
                'nominal_refund' => $pesanan->total_harga,
                'alasan_batal' => 'Dibatalkan oleh admin',
                'status_refund' => 'pending',
            ]);
        }

        if ($refundBaru) {
            return redirect()->route('admin.refund.show', $refundBaru->id)
                ->with('success', 'Pesanan Transfer dibatalkan. Silakan proses refund di halaman ini.');
        }

        return redirect()->back()
            ->with('success', 'Status pesanan berhasil diupdate!');
    }

    public function batalkan(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'alasan_batal' => 'required|string|max:255',
        ]);

        foreach ($pesanan->detail as $d) {
            if ($d->item_type === 'App\\Models\\ProdukTahu') {
                \App\Models\ProdukTahu::where('id', $d->item_id)->increment('stok', $d->jumlah);
            } else {
                \App\Models\Limbah::where('id', $d->item_id)->increment('stok', $d->jumlah);
            }
        }

        $pesanan->update([
            'order_status' => 'dibatalkan',
            'alasan_batal' => $request->alasan_batal,
            'lat_kurir' => null,
            'lng_kurir' => null,
            'lokasi_updated_at' => null,
        ]);

        $refundBaru = null;
        if ($pesanan->payment_method === 'Transfer'
            && $pesanan->payment_status === 'paid'
            && !$pesanan->refund) {

            $refundBaru = Refund::create([
                'pesanan_id' => $pesanan->id,
                'nominal_refund' => $pesanan->total_harga,
                'alasan_batal' => $request->alasan_batal,
                'status_refund' => 'pending',
            ]);
        }

        if ($refundBaru) {
            return redirect()->route('admin.refund.show', $refundBaru->id)
                ->with('success', 'Pesanan Transfer dibatalkan. Silakan proses refund di halaman ini.');
        }

        return redirect()->back()
            ->with('success', 'Pesanan berhasil dibatalkan dan alasan telah dicatat!');
    }

    public function verifikasiPembayaran(Pesanan $pesanan)
    {
        if (!$pesanan->pembayaran) {
            $pesanan->pembayaran()->create([
                'metode_pembayaran' => $pesanan->payment_method,
                'status_pembayaran' => 'verified',
                'jumlah_bayar' => $pesanan->total_harga,
                'tanggal_bayar' => now(),
            ]);
        } else {
            $pesanan->pembayaran->update([
                'status_pembayaran' => 'verified',
                'tanggal_bayar' => now(),
            ]);
        }

        $pesanan->update(['payment_status' => 'paid']);

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    // ============================================================
    // TRACK KURIR
    // ============================================================

    /**
     * Admin klik "Mulai Antar" — set lokasi kurir awal.
     */
    public function startTracking(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        if ($pesanan->order_status !== 'dikirim') {
            return response()->json([
                'success' => false,
                'message' => 'Status pesanan harus "dikirim" untuk memulai tracking.',
            ], 422);
        }

        $pesanan->update([
            'lat_kurir' => $request->lat,
            'lng_kurir' => $request->lng,
            'lokasi_updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tracking dimulai.',
            'updated_at' => $pesanan->lokasi_updated_at->toIso8601String(),
        ]);
    }

    /**
     * Update lokasi kurir (dipanggil tiap 30 detik dari HP admin).
     */
    public function updateLokasi(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        if ($pesanan->order_status !== 'dikirim') {
            return response()->json([
                'success' => false,
                'message' => 'Tracking tidak aktif untuk pesanan ini.',
            ], 422);
        }

        $pesanan->update([
            'lat_kurir' => $request->lat,
            'lng_kurir' => $request->lng,
            'lokasi_updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'updated_at' => $pesanan->lokasi_updated_at->toIso8601String(),
        ]);
    }

    /**
     * Admin klik "Selesai Antar" — clear lokasi kurir.
     */
    public function stopTracking(Pesanan $pesanan)
    {
        $pesanan->update([
            'lat_kurir' => null,
            'lng_kurir' => null,
            'lokasi_updated_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tracking dihentikan.',
        ]);
    }
}