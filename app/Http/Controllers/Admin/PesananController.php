<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Refund;
use Illuminate\Http\Request;

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

        $pesanan->update(['order_status' => $request->order_status]);

        // Kalau status jadi 'dibatalkan' & metode Transfer & udah paid → auto-create refund
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

        // Kembalikan stok
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
        ]);

        // Auto-create refund kalau Transfer & udah paid
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
        // Handle COD yang belum punya record pembayaran
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
}