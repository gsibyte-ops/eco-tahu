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
            'alasan_batal'   => 'required|string|min:10|max:500',
            'bukti_transfer' => \App\Support\MediaGuard::imageRules(false, 2048),
            'nama_bank'      => 'nullable|regex:/^[a-zA-Z0-9\s\-]+$/|max:50',
            'nomor_rekening' => 'nullable|regex:/^[0-9]+$/|max:50',
            'atas_nama'      => 'nullable|regex:/^[a-zA-Z\s]+$/|max:100',
        ], [
            'alasan_batal.required'     => 'Alasan pembatalan wajib diisi.',
            'alasan_batal.min'          => 'Alasan minimal 10 karakter.',
            'bukti_transfer.image'      => 'Bukti transfer hanya boleh berupa file foto (JPG, PNG, WEBP).',
            'bukti_transfer.mimes'      => 'Format bukti transfer harus JPG, PNG, atau WEBP.',
            'bukti_transfer.mimetypes'  => 'Bukti transfer hanya boleh berupa file foto asli.',
            'bukti_transfer.max'        => 'Ukuran bukti transfer maksimal 2MB.',
            'nama_bank.regex'           => 'Nama bank/e-wallet hanya boleh berisi huruf, angka, spasi, atau tanda hubung.',
            'nomor_rekening.regex'      => 'Nomor rekening hanya boleh berisi angka.',
            'atas_nama.regex'           => 'Nama pemilik rekening hanya boleh berisi huruf dan spasi.',
        ]);

        if (!in_array($pesanan->order_status, ['pending', 'diproses'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah ' . $pesanan->order_status . '.');
        }

        if ($pesanan->refund) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        DB::beginTransaction();

        try {
            $pesanan->update([
                'order_status' => 'dibatalkan',
                'alasan_batal' => $request->alasan_batal,
            ]);

            foreach ($pesanan->detail as $d) {
                if ($d->item_type === 'App\\Models\\ProdukTahu') {
                    \App\Models\ProdukTahu::where('id', $d->item_id)->increment('stok', $d->jumlah);
                } else {
                    \App\Models\Limbah::where('id', $d->item_id)->increment('stok', $d->jumlah);
                }
            }

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

            $nominalRefund = 0;
            if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status === 'paid') {
                $nominalRefund = $pesanan->total_harga;
            }

            Refund::create([
                'pesanan_id'     => $pesanan->id,
                'nominal_refund' => $nominalRefund,
                'alasan_batal'   => $request->alasan_batal,
                'nama_bank'      => $pesanan->payment_method === 'Transfer' ? $request->nama_bank : null,
                'nomor_rekening' => $pesanan->payment_method === 'Transfer' ? $request->nomor_rekening : null,
                'atas_nama'      => $pesanan->payment_method === 'Transfer' ? $request->atas_nama : null,
                'status_refund'  => 'pending',
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
            'bukti_transfer' => \App\Support\MediaGuard::imageRules(true, 2048),
        ], [
            'bukti_transfer.required'  => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.image'     => 'Bukti transfer hanya boleh berupa file foto (JPG, PNG, WEBP).',
            'bukti_transfer.mimes'     => 'Format bukti transfer harus JPG, PNG, atau WEBP.',
            'bukti_transfer.mimetypes' => 'Bukti transfer hanya boleh berupa file foto asli.',
            'bukti_transfer.max'       => 'Ukuran bukti transfer maksimal 2MB.',
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

    /**
     * Endpoint polling untuk tracking kurir (dipanggil tiap 10 detik).
     */
    public function tracking(string $kode)
    {
        $pesanan = Pesanan::where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Tracking cuma aktif kalau status = dikirim & ada data kurir
        $active = $pesanan->order_status === 'dikirim'
               && $pesanan->lat_kurir !== null
               && $pesanan->lng_kurir !== null;

        return response()->json([
            'active' => $active,
            'order_status' => $pesanan->order_status,
            'kurir' => $active ? [
                'lat' => (float) $pesanan->lat_kurir,
                'lng' => (float) $pesanan->lng_kurir,
                'updated_at' => $pesanan->lokasi_updated_at?->toIso8601String(),
            ] : null,
            'toko' => [
                'lat' => (float) config('toko.lat'),
                'lng' => (float) config('toko.lng'),
                'nama' => config('toko.nama'),
            ],
            'tujuan' => $pesanan->lat_tujuan && $pesanan->lng_tujuan ? [
                'lat' => (float) $pesanan->lat_tujuan,
                'lng' => (float) $pesanan->lng_tujuan,
            ] : null,
        ]);
    }
}