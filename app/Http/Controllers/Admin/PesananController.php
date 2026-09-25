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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by kode pesanan / nama pelanggan / nama produk
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('kode_pesanan', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('username', 'like', "%{$q}%"))
                ->orWhereHas('detail', fn ($d) => $d->where('nama_item', 'like', "%{$q}%"));
            });
        }

        $pesanan = $query->paginate(10)->withQueryString();

        // Statistik ringkas
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
        if ($request->order_status === 'dibatalkan'
            && $pesanan->payment_method === 'Transfer'
            && $pesanan->payment_status === 'paid'
            && !$pesanan->refund) {
            Refund::create([
                'pesanan_id' => $pesanan->id,
                'nominal_refund' => $pesanan->total_harga,
                'alasan_batal' => 'Dibatalkan oleh admin',
                'status_refund' => 'pending',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Status pesanan berhasil diupdate!');
    }

    public function verifikasiPembayaran(Pesanan $pesanan)
    {
        if ($pesanan->pembayaran) {
            $pesanan->pembayaran->update([
                'status_pembayaran' => 'verified',
                'tanggal_bayar' => now(),
            ]);
            $pesanan->update(['payment_status' => 'paid']);
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi!');
    }
}