<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Default: bulan ini
        $dari = $request->filled('dari')
            ? $request->dari
            : now()->startOfMonth()->format('Y-m-d');

        $sampai = $request->filled('sampai')
            ? $request->sampai
            : now()->format('Y-m-d');

        // Ringkasan
        $ringkasan = [
            'total_pendapatan' => Pesanan::where('payment_status', 'paid')
                ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
                ->sum('total_harga'),

            'total_pesanan' => Pesanan::whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->count(),

            'total_selesai' => Pesanan::where('order_status', 'selesai')
                ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
                ->count(),

            'total_dibatalkan' => Pesanan::where('order_status', 'dibatalkan')
                ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
                ->count(),

            'rata_rata' => Pesanan::where('payment_status', 'paid')
                ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
                ->avg('total_harga') ?? 0,
        ];

        // Grafik penjualan harian
        $grafikHarian = Pesanan::select(
                DB::raw('DATE(tanggal_order) as tanggal'),
                DB::raw('SUM(total_harga) as total'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('payment_status', 'paid')
            ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Top produk tahu terjual
        $topProduk = DB::table('detail_pesanan')
            ->join('pesanan', 'detail_pesanan.pesanan_id', '=', 'pesanan.id')
            ->where('detail_pesanan.item_type', 'App\\Models\\ProdukTahu')
            ->where('pesanan.payment_status', 'paid')
            ->whereBetween('pesanan.tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
            ->select(
                'detail_pesanan.nama_item',
                DB::raw('SUM(detail_pesanan.jumlah) as total_qty'),
                DB::raw('SUM(detail_pesanan.subtotal) as total_omzet')
            )
            ->groupBy('detail_pesanan.nama_item')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Top limbah terjual
        $topLimbah = DB::table('detail_pesanan')
            ->join('pesanan', 'detail_pesanan.pesanan_id', '=', 'pesanan.id')
            ->where('detail_pesanan.item_type', 'App\\Models\\Limbah')
            ->where('pesanan.payment_status', 'paid')
            ->whereBetween('pesanan.tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
            ->select(
                'detail_pesanan.nama_item',
                DB::raw('SUM(detail_pesanan.jumlah) as total_qty'),
                DB::raw('SUM(detail_pesanan.subtotal) as total_omzet')
            )
            ->groupBy('detail_pesanan.nama_item')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Top pelanggan
        $topPelanggan = User::where('role_id', 2)
            ->withCount(['pesanan as total_pesanan' => function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59']);
            }])
            ->withSum(['pesanan as total_belanja' => function ($q) use ($dari, $sampai) {
                $q->where('payment_status', 'paid')
                  ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59']);
            }], 'total_harga')
            ->having('total_pesanan', '>', 0)
            ->orderByDesc('total_belanja')
            ->limit(10)
            ->get();

        // Metode pembayaran
        $metodePembayaran = Pesanan::select(
                'payment_method',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('payment_status', 'paid')
            ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
            ->groupBy('payment_method')
            ->get();

        // Stok menipis
        $stokMenipis = ProdukTahu::where('stok', '<=', 10)
            ->where('status', 'aktif')
            ->orderBy('stok')
            ->limit(10)
            ->get();

        $limbahMenipis = Limbah::where('stok', '<=', 10)
            ->where('status', 'aktif')
            ->orderBy('stok')
            ->limit(10)
            ->get();

        return view('admin.laporan.index', compact(
            'dari', 'sampai', 'ringkasan', 'grafikHarian',
            'topProduk', 'topLimbah', 'topPelanggan',
            'metodePembayaran', 'stokMenipis', 'limbahMenipis'
        ));
    }
}