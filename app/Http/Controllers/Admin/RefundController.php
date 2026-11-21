<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Notifications\UpdatePesanan; // TAMBAHAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = Refund::with(['pesanan.user', 'pesanan.detail', 'pesanan.pembayaran'])->orderByDesc('id');

        if ($request->filled('status')) { $query->where('status_refund', $request->status); }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('pesanan', function ($w) use ($q) {
                $w->where('kode_pesanan', 'like', "%{$q}%")
                  ->orWhereHas('user', fn ($u) => $u->where('username', 'like', "%{$q}%"))
                  ->orWhereHas('detail', fn ($d) => $d->where('nama_item', 'like', "%{$q}%"));
            });
        }

        $dari = $request->filled('dari') ? $request->dari : null;
        $sampai = $request->filled('sampai') ? $request->sampai : null;

        if ($dari && Carbon::parse($dari)->isFuture()) { $dari = now()->format('Y-m-d'); }
        if ($sampai && Carbon::parse($sampai)->isFuture()) { $sampai = now()->format('Y-m-d'); }
        if ($dari && $sampai && Carbon::parse($dari)->gt(Carbon::parse($sampai))) { [$dari, $sampai] = [$sampai, $dari]; }

        if ($dari) { $query->where('tanggal_refund', '>=', $dari . ' 00:00:00'); }
        if ($sampai) { $query->where('tanggal_refund', '<=', $sampai . ' 23:59:59'); }

        $refund = $query->paginate(10)->withQueryString();

        $stats = [
            'pending' => Refund::where('status_refund', 'pending')->count(),
            'diproses' => Refund::where('status_refund', 'diproses')->count(),
            'selesai' => Refund::where('status_refund', 'selesai')->count(),
            'ditolak' => Refund::where('status_refund', 'ditolak')->count(),
            'total_nominal' => Refund::where('status_refund', 'selesai')->sum('nominal_refund'),
        ];

        return view('admin.refund.index', compact('refund', 'stats'));
    }

    public function show(Refund $refund)
    {
        $refund->load(['pesanan.user', 'pesanan.detail', 'pesanan.pembayaran']);
        return view('admin.refund.show', compact('refund'));
    }

    public function update(Request $request, Refund $refund)
    {
        $request->validate([
            'status_refund' => 'required|in:pending,diproses,selesai,ditolak',
            'catatan_admin' => 'nullable|string|max:1000',
            'bukti_transfer_balik' => \App\Support\MediaGuard::imageRules(false, 5120),
        ], [
            'bukti_transfer_balik.image'     => 'Bukti transfer balik harus berupa file foto (JPG, PNG, WEBP).',
            'bukti_transfer_balik.mimes'     => 'Format bukti transfer balik harus JPG, PNG, atau WEBP.',
            'bukti_transfer_balik.mimetypes' => 'Hanya file gambar asli (JPG, PNG, WEBP) yang diperbolehkan.',
            'bukti_transfer_balik.max'       => 'Ukuran bukti transfer balik maksimal 5MB.',
        ]);

        try {
            $data = [
                'status_refund' => $request->status_refund,
                'catatan_admin' => $request->catatan_admin,
            ];

            if ($request->hasFile('bukti_transfer_balik')) {
                if ($refund->bukti_transfer_balik && Storage::disk('public')->exists($refund->bukti_transfer_balik)) {
                    Storage::disk('public')->delete($refund->bukti_transfer_balik);
                }
                $data['bukti_transfer_balik'] = $request->file('bukti_transfer_balik')->store('refund-bukti', 'public');
            }

            $refund->update($data);

            $isPaid = in_array($refund->pesanan->payment_status, ['paid', 'success', 'lunas', 'settlement', 'refunded']);

            if ($request->status_refund === 'selesai') {
                $orderData = ['order_status' => 'dibatalkan'];
                if ($isPaid) { $orderData['payment_status'] = 'refunded'; }
                $refund->pesanan->update($orderData);
            }

            // NOTIFIKASI KE USER
            $refund->pesanan->user->notify(new UpdatePesanan(
                "Status refund pesanan #{$refund->pesanan->kode_pesanan} menjadi: " . ucfirst($request->status_refund),
                route('user.pesanan.show', $refund->pesanan->kode_pesanan)
            ));

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Status refund berhasil diupdate!']);
            }

            return redirect()->route('admin.refund.index')->with('success', 'Status refund berhasil diupdate!');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function destroy(Refund $refund)
    {
        if ($refund->bukti_transfer_balik && Storage::disk('public')->exists($refund->bukti_transfer_balik)) {
            Storage::disk('public')->delete($refund->bukti_transfer_balik);
        }
        $refund->delete();
        return redirect()->route('admin.refund.index')->with('success', 'Data refund berhasil dihapus!');
    }
}