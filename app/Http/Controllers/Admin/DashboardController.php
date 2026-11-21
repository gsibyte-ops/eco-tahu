<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Refund;
use App\Models\Resep;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ============ 1. STATISTIK UTAMA ============
        $totalTahu   = ProdukTahu::sum('stok');
        $totalLimbah = Limbah::sum('stok');
        $totalProduk = $totalTahu + $totalLimbah;

        $statusLunas = ['paid', 'verified', 'success', 'lunas', 'settlement'];

        $stats = [
            'total_pesanan'    => Pesanan::count(),
            'total_pendapatan' => (int) Pesanan::where(function($q) use ($statusLunas) {
                $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
            })->sum('total_harga'),
            'total_produk'        => $totalProduk,
            'total_tahu'          => $totalTahu,
            'total_limbah'        => $totalLimbah,
            'total_pelanggan'     => User::where('role_id', 2)->count(),
            'refund_pending'      => Refund::where('status_refund', 'pending')->count(),
            'total_edukasi'       => Edukasi::count(),
            'edukasi_publish'     => Edukasi::visibleToUser()->count(),
            'total_edukasi_likes' => DB::table('edukasi_likes')->count(),
            'total_resep'         => Resep::count(),
            'resep_publish'       => Resep::visibleToUser()->count(),
            'total_resep_likes'   => DB::table('resep_likes')->count(),
            'total_ulasan'        => Review::count(),
            'ulasan_bermedia'     => Review::whereNotNull('media_path')->count(),
            'rating_rata'         => round((float) (Review::avg('rating') ?? 0), 1),
        ];

        // ============ 2. KALKULASI PERSENTASE (APPLES-TO-APPLES) ============
        $startThisWeek = now()->startOfWeek();
        $endThisWeek = now();
        
        $startLastWeek = now()->subWeek()->startOfWeek();
        $endLastWeek = now()->subWeek(); // Sampai di hari dan jam yang sama persis minggu lalu

        // 1. Growth Pesanan
        $pesananThisWeek = Pesanan::whereBetween('tanggal_order', [$startThisWeek, $endThisWeek])->count();
        $pesananLastWeek = Pesanan::whereBetween('tanggal_order', [$startLastWeek, $endLastWeek])->count();
        $stats['pesanan_growth'] = $this->hitungGrowth($pesananThisWeek, $pesananLastWeek);

        // 2. Growth Pendapatan
        $pendapatanThisWeek = Pesanan::where(function($q) use ($statusLunas) {
            $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
        })->whereBetween('tanggal_order', [$startThisWeek, $endThisWeek])->sum('total_harga');
        
        $pendapatanLastWeek = Pesanan::where(function($q) use ($statusLunas) {
            $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
        })->whereBetween('tanggal_order', [$startLastWeek, $endLastWeek])->sum('total_harga');
        
        $stats['pendapatan_growth'] = $this->hitungGrowth($pendapatanThisWeek, $pendapatanLastWeek);

        // 3. Growth Pelanggan
        $pelangganThisWeek = User::where('role_id', 2)->whereBetween('created_at', [$startThisWeek, $endThisWeek])->count();
        $pelangganLastWeek = User::where('role_id', 2)->whereBetween('created_at', [$startLastWeek, $endLastWeek])->count();
        $stats['pelanggan_growth'] = $this->hitungGrowth($pelangganThisWeek, $pelangganLastWeek);


        // ============ 3. GRAFIK PENJUALAN ============
        // 7 Hari
        $startDateWeek = now()->subDays(6)->startOfDay(); 
        $penjualanHarian = Pesanan::select(
                DB::raw('DATE(tanggal_order) as date_only'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where(function($q) use ($statusLunas) {
                $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
            })
            ->where('tanggal_order', '>=', $startDateWeek)
            ->groupBy('date_only')
            ->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            $found = $penjualanHarian->firstWhere('date_only', $dateStr);
            $chartData[] = $found ? (float) $found->total : 0;
        }

        // 30 Hari
        $startDateMonth = now()->subDays(29)->startOfDay();
        $penjualanBulanan = Pesanan::select(
                DB::raw('DATE(tanggal_order) as date_only'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where(function($q) use ($statusLunas) {
                $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
            })
            ->where('tanggal_order', '>=', $startDateMonth)
            ->groupBy('date_only')
            ->get();

        $chartLabelsBulanan = [];
        $chartDataBulanan = [];
        for ($i = 29; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $chartLabelsBulanan[] = now()->subDays($i)->format('d M');
            $found = $penjualanBulanan->firstWhere('date_only', $dateStr);
            $chartDataBulanan[] = $found ? (float) $found->total : 0;
        }

        // ============ 4. PRODUK TERLARIS & PESANAN TERBARU ============
        $produkTahuTerlaris = DB::table('detail_pesanan')
            ->join('produk_tahu', 'detail_pesanan.item_id', '=', 'produk_tahu.id')
            ->where('detail_pesanan.item_type', 'App\\Models\\ProdukTahu')
            ->select('produk_tahu.nama_produk', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->groupBy('produk_tahu.id', 'produk_tahu.nama_produk')
            ->orderByDesc('total_terjual')->limit(5)->get();

        $produkLimbahTerlaris = DB::table('detail_pesanan')
            ->join('limbah', 'detail_pesanan.item_id', '=', 'limbah.id')
            ->where('detail_pesanan.item_type', 'App\\Models\\Limbah')
            ->select('limbah.nama_limbah as nama_produk', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->groupBy('limbah.id', 'limbah.nama_limbah')
            ->orderByDesc('total_terjual')->limit(5)->get();

        $produkTerlaris = $produkTahuTerlaris->concat($produkLimbahTerlaris)->sortByDesc('total_terjual')->take(5)->values();

        $pesananTerbaru = Pesanan::with('user')->orderByDesc('tanggal_order')->limit(5)->get();
        $ulasanTerbaru  = Review::with(['user', 'reviewable'])->orderByDesc('id')->limit(5)->get();
        $refundTerbaru  = Refund::with('pesanan.user')->orderByDesc('id')->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'chartLabels', 'chartData', 'chartLabelsBulanan', 'chartDataBulanan',
            'produkTerlaris', 'pesananTerbaru', 'ulasanTerbaru', 'refundTerbaru'
        ));
    }

    private function hitungGrowth($current, $previous)
    {
        // Kalau minggu lalu 0, kita kembalikan null (nanti di UI ditampilin strip '-')
        if ($previous == 0) {
            return null; 
        }
        $growth = (($current - $previous) / $previous) * 100;
        return round($growth, 1); // Dibulatkan 1 desimal
    }

    public function getStats()
    {
        $statusLunas = ['paid', 'verified', 'success', 'lunas', 'settlement'];
        $totalTahu   = ProdukTahu::sum('stok');
        $totalLimbah = Limbah::sum('stok');
        $totalProduk = $totalTahu + $totalLimbah;

        return response()->json([
            'total_pesanan'       => Pesanan::count(),
            'total_pendapatan'    => (int) Pesanan::where(function($q) use ($statusLunas) {
                $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
            })->sum('total_harga'),
            'total_produk'        => $totalProduk,
            'total_tahu'          => $totalTahu,
            'total_limbah'        => $totalLimbah,
            'total_pelanggan'     => User::where('role_id', 2)->count(),
            'refund_pending'      => Refund::where('status_refund', 'pending')->count(),
            'total_edukasi'       => Edukasi::count(),
            'edukasi_publish'     => Edukasi::visibleToUser()->count(),
            'total_edukasi_likes' => DB::table('edukasi_likes')->count(),
            'total_resep'         => Resep::count(),
            'resep_publish'       => Resep::visibleToUser()->count(),
            'total_resep_likes'   => DB::table('resep_likes')->count(),
            'total_ulasan'        => Review::count(),
            'ulasan_bermedia'     => Review::whereNotNull('media_path')->count(),
            'rating_rata'         => round((float) (Review::avg('rating') ?? 0), 1),
        ]);
    }
}