<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = Refund::with(['pesanan.user'])->orderByDesc('id');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_refund', $request->status);
        }

        // Search by kode pesanan / nama pelanggan
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('pesanan', function ($w) use ($q) {
                $w->where('kode_pesanan', 'like', "%{$q}%")
                  ->orWhereHas('user', fn ($u) => $u->where('username', 'like', "%{$q}%"));
            });
        }

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
            'bukti_transfer_balik' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = ['status_refund' => $request->status_refund];

        if ($request->hasFile('bukti_transfer_balik')) {
            // Hapus bukti lama kalau ada
            if ($refund->bukti_transfer_balik && Storage::disk('public')->exists($refund->bukti_transfer_balik)) {
                Storage::disk('public')->delete($refund->bukti_transfer_balik);
            }
            $data['bukti_transfer_balik'] = $request->file('bukti_transfer_balik')->store('refund-bukti', 'public');
        }

        $refund->update($data);

        // Kalau refund selesai, update payment_status pesanan jadi 'refunded'
        if ($request->status_refund === 'selesai') {
            $refund->pesanan->update(['payment_status' => 'refunded']);
        }

        return redirect()->route('admin.refund.show', $refund->id)
            ->with('success', 'Status refund berhasil diupdate!');
    }

    public function destroy(Refund $refund)
    {
        if ($refund->bukti_transfer_balik && Storage::disk('public')->exists($refund->bukti_transfer_balik)) {
            Storage::disk('public')->delete($refund->bukti_transfer_balik);
        }

        $refund->delete();

        return redirect()->route('admin.refund.index')
            ->with('success', 'Data refund berhasil dihapus!');
    }
}