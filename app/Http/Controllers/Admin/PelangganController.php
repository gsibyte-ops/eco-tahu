<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role_id', 2)
            ->withCount('pesanan')
            ->withSum(['pesanan as total_belanja' => function ($q) {
                $q->where('payment_status', 'paid');
            }], 'total_harga')
            ->orderByDesc('id');

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('username', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('no_telepon', 'like', "%{$q}%");
            });
        }

        $pelanggan = $query->paginate(10)->withQueryString();

        // Statistik
        $stats = [
            'total_pelanggan' => User::where('role_id', 2)->count(),
            'pelanggan_aktif' => User::where('role_id', 2)
                ->whereHas('pesanan', function ($q) {
                    $q->where('tanggal_order', '>=', now()->subDays(30));
                })->count(),
            'total_transaksi' => Pesanan::where('payment_status', 'paid')->count(),
            'rata_rata_belanja' => Pesanan::where('payment_status', 'paid')->avg('total_harga') ?? 0,
        ];

        return view('admin.pelanggan.index', compact('pelanggan', 'stats'));
    }

    public function show(User $pelanggan)
    {
        // Pastikan yang diakses adalah pelanggan (bukan admin)
        abort_if($pelanggan->role_id !== 2, 404);

        $pelanggan->load(['pesanan' => function ($q) {
            $q->orderByDesc('tanggal_order')->limit(10);
        }]);

        $statistik = [
            'total_pesanan' => $pelanggan->pesanan()->count(),
            'total_belanja' => $pelanggan->pesanan()->where('payment_status', 'paid')->sum('total_harga'),
            'pesanan_selesai' => $pelanggan->pesanan()->where('order_status', 'selesai')->count(),
            'pesanan_dibatalkan' => $pelanggan->pesanan()->where('order_status', 'dibatalkan')->count(),
        ];

        return view('admin.pelanggan.show', compact('pelanggan', 'statistik'));
    }
}