<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\User;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pesanan' => Pesanan::count(),
            'total_pendapatan' => Pesanan::where('payment_status', 'paid')->sum('total_harga'),
            'total_produk' => ProdukTahu::sum('stok'),
            'total_pelanggan' => User::where('role_id', 2)->count(),
            'refund_pending' => Refund::where('status_refund', 'pending')->count(),
        ];

        // ============ GRAFIK 7 HARI ============
        $penjualanHarian = Pesanan::select(
                DB::raw('DATE(tanggal_order) as tanggal'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('payment_status', 'paid')
            ->where('tanggal_order', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            $found = $penjualanHarian->firstWhere('tanggal', $tanggal);
            $chartData[] = $found ? (float) $found->total : 0;
        }

        // ============ GRAFIK 30 HARI ============
        $penjualanBulanan = Pesanan::select(
                DB::raw('DATE(tanggal_order) as tanggal'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('payment_status', 'paid')
            ->where('tanggal_order', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chartLabelsBulanan = [];
        $chartDataBulanan = [];
        for ($i = 29; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $chartLabelsBulanan[] = now()->subDays($i)->format('d M');
            $found = $penjualanBulanan->firstWhere('tanggal', $tanggal);
            $chartDataBulanan[] = $found ? (float) $found->total : 0;
        }

        // ============ PRODUK TERLARIS ============
        $produkTerlaris = DB::table('detail_pesanan')
            ->join('produk_tahu', 'detail_pesanan.item_id', '=', 'produk_tahu.id')
            ->where('detail_pesanan.item_type', 'App\\Models\\ProdukTahu')
            ->select('produk_tahu.nama_produk', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->groupBy('produk_tahu.id', 'produk_tahu.nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // ============ PESANAN TERBARU ============
        $pesananTerbaru = Pesanan::with('user')
            ->orderByDesc('tanggal_order')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'chartLabels', 'chartData',
            'chartLabelsBulanan', 'chartDataBulanan',
            'produkTerlaris', 'pesananTerbaru'
        ));
    }
}