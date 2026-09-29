<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    public function show(string $kode)
    {
        $pesanan = Pesanan::with(['detail', 'pembayaran', 'refund'])
            ->where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('user.pesanan.show', compact('pesanan'));
    }

    public function cancel(Request $request, string $kode)
    {
        $pesanan = Pesanan::with(['pembayaran', 'detail'])
            ->where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'alasan_batal' => 'required|string|min:10|max:500',
            'bukti_transfer' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'alasan_batal.required' => 'Alasan pembatalan wajib diisi.',
            'alasan_batal.min' => 'Alasan minimal 10 karakter.',
        ]);

        // Validasi: cuma bisa cancel kalau status pending/diproses
        if (!in_array($pesanan->order_status, ['pending', 'diproses'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah ' . $pesanan->order_status . '.');
        }

        // Cek kalau sudah ada refund
        if ($pesanan->refund) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        DB::beginTransaction();

        try {
            // 1. Update status pesanan
            $pesanan->update([
                'order_status' => 'dibatalkan',
                'alasan_batal' => $request->alasan_batal,
            ]);

            // 2. Kembalikan stok
            foreach ($pesanan->detail as $d) {
                if ($d->item_type === 'App\\Models\\ProdukTahu') {
                    \App\Models\ProdukTahu::where('id', $d->item_id)->increment('stok', $d->jumlah);
                } else {
                    \App\Models\Limbah::where('id', $d->item_id)->increment('stok', $d->jumlah);
                }
            }

            // 3. Upload bukti transfer (opsional)
            // CEK: kalau user klik X (batalkan_bukti = 1), SKIP upload
            $uploadBukti = $request->hasFile('bukti_transfer')
                        && $request->input('batalkan_bukti') != '1';

            if ($uploadBukti) {
                if (!$pesanan->pembayaran) {
                    $pesanan->pembayaran()->create([
                        'metode_pembayaran' => $pesanan->payment_method,
                        'status_pembayaran' => 'pending',
                        'jumlah_bayar' => $pesanan->total_harga,
                    ]);
                    $pesanan->refresh();
                    $pesanan->load('pembayaran');
                }

                if ($pesanan->pembayaran->bukti_transfer && Storage::disk('public')->exists($pesanan->pembayaran->bukti_transfer)) {
                    Storage::disk('public')->delete($pesanan->pembayaran->bukti_transfer);
                }

                $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');
                $pesanan->pembayaran->update(['bukti_transfer' => $path]);
            }

            // 4. Buat data Refund
            $nominalRefund = 0;
            if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status === 'paid') {
                $nominalRefund = $pesanan->total_harga;
            }

            Refund::create([
                'pesanan_id' => $pesanan->id,
                'nominal_refund' => $nominalRefund,
                'alasan_batal' => $request->alasan_batal,
                'status_refund' => 'pending',
            ]);

            if ($nominalRefund > 0) {
                $pesanan->update(['payment_status' => 'refunded']);
                $msg = 'Pesanan dibatalkan. Dana akan dikembalikan setelah diverifikasi admin.';
            } else {
                $msg = 'Pesanan dibatalkan. Pengajuan sudah dikirim ke admin untuk diproses.';
            }

            DB::commit();
            return redirect()->route('user.pesanan.show', $pesanan->kode_pesanan)->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    public function uploadBukti(Request $request, string $kode)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $pesanan = Pesanan::with('pembayaran')
            ->where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$pesanan->pembayaran) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($pesanan->expired_at && $pesanan->expired_at->isPast()) {
            return back()->with('error', 'Waktu pembayaran sudah habis.');
        }

        if ($pesanan->pembayaran->bukti_transfer && Storage::disk('public')->exists($pesanan->pembayaran->bukti_transfer)) {
            Storage::disk('public')->delete($pesanan->pembayaran->bukti_transfer);
        }

        $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');

        $pesanan->pembayaran->update(['bukti_transfer' => $path]);

        return back()->with('success', 'Bukti transfer berhasil diupload.');
    }
}