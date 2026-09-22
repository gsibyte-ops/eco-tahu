<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['detail', 'refund'])
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal_order');

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $pesanan = $query->paginate(10)->withQueryString();

        $stats = [
            'all' => Pesanan::where('user_id', Auth::id())->count(),
            'pending' => Pesanan::where('user_id', Auth::id())->where('order_status', 'pending')->count(),
            'diproses' => Pesanan::where('user_id', Auth::id())->where('order_status', 'diproses')->count(),
            'dikirim' => Pesanan::where('user_id', Auth::id())->where('order_status', 'dikirim')->count(),
            'selesai' => Pesanan::where('user_id', Auth::id())->where('order_status', 'selesai')->count(),
            'dibatalkan' => Pesanan::where('user_id', Auth::id())->where('order_status', 'dibatalkan')->count(),
        ];

        return view('user.pesanan.index', compact('pesanan', 'stats'));
    }

    public function show($kode)
    {
        $pesanan = Pesanan::with(['detail', 'pembayaran', 'refund'])
            ->where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('user.pesanan.show', compact('pesanan'));
    }

    public function cancel(Request $request, $kode)
    {
        $request->validate([
            'alasan_batal' => 'required|string|min:10|max:500',
        ]);

        $pesanan = Pesanan::where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Validasi: cuma bisa cancel kalau pending/diproses
        if (!in_array($pesanan->order_status, ['pending', 'diproses'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah ' . $pesanan->order_status . '.');
        }

        // Kalau sudah ada refund pending, gak bisa cancel lagi
        if ($pesanan->refund) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        DB::beginTransaction();

        try {
            // Update status pesanan jadi dibatalkan
            $pesanan->update(['order_status' => 'dibatalkan']);

            // Kembalikan stok
            foreach ($pesanan->detail as $d) {
                if ($d->item_type === 'App\\Models\\ProdukTahu') {
                    \App\Models\ProdukTahu::where('id', $d->item_id)->increment('stok', $d->jumlah);
                } else {
                    \App\Models\Limbah::where('id', $d->item_id)->increment('stok', $d->jumlah);
                }
            }

            // Kalau Transfer & sudah paid → auto-create refund
            if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status === 'paid') {
                Refund::create([
                    'pesanan_id' => $pesanan->id,
                    'nominal_refund' => $pesanan->total_harga,
                    'alasan_batal' => $request->alasan_batal,
                    'status_refund' => 'pending',
                ]);

                $pesanan->update(['payment_status' => 'refunded']);

                $msg = 'Pesanan dibatalkan. Dana akan dikembalikan ke rekening Anda dalam 1x24 jam.';
            } else {
                $msg = 'Pesanan berhasil dibatalkan.';
            }

            DB::commit();

            return redirect()->route('user.pesanan.show', $pesanan->kode_pesanan)
                ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    public function uploadBukti(Request $request, $kode)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $pesanan = Pesanan::with('pembayaran')
            ->where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$pesanan->pembayaran) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($pesanan->pembayaran->bukti_transfer && \Storage::disk('public')->exists($pesanan->pembayaran->bukti_transfer)) {
            \Storage::disk('public')->delete($pesanan->pembayaran->bukti_transfer);
        }

        $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');

        $pesanan->pembayaran->update(['bukti_transfer' => $path]);

        return back()->with('success', 'Bukti transfer berhasil diupload.');
    }
}