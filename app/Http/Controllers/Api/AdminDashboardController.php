<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Refund;
use App\Models\Resep;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/admin/overview
     * Endpoint read-only untuk Dashboard Overview Admin di aplikasi mobile.
     */
    public function overview(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('role');

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya untuk admin.',
            ], 403);
        }

        $statusLunas = ['paid', 'verified', 'success', 'lunas', 'settlement'];
        $totalTahu   = (int) ProdukTahu::sum('stok');
        $totalLimbah = (int) Limbah::sum('stok');

        $stats = [
            'total_pesanan'        => Pesanan::count(),
            'pesanan_pending'      => Pesanan::where('order_status', 'pending')->count(),
            'pesanan_diproses'     => Pesanan::where('order_status', 'diproses')->count(),
            'pesanan_dikirim'      => Pesanan::where('order_status', 'dikirim')->count(),
            'pesanan_selesai'      => Pesanan::where('order_status', 'selesai')->count(),
            'pesanan_dibatalkan'   => Pesanan::where('order_status', 'dibatalkan')->count(),
            'total_pendapatan'     => (int) Pesanan::where(function ($q) use ($statusLunas) {
                $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
            })->sum('total_harga'),
            'pendapatan_bulan_ini' => (int) Pesanan::where(function ($q) use ($statusLunas) {
                $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
            })->where('tanggal_order', '>=', now()->startOfMonth())->sum('total_harga'),
            'total_produk'         => $totalTahu + $totalLimbah,
            'total_tahu'           => $totalTahu,
            'total_limbah'         => $totalLimbah,
            'total_pelanggan'      => User::where('role_id', 2)->count(),
            'refund_pending'       => Refund::where('status_refund', 'pending')->count(),
            'total_refund'         => Refund::count(),
            'total_edukasi'        => Edukasi::count(),
            'edukasi_publish'      => Edukasi::visibleToUser()->count(),
            'total_edukasi_likes'  => DB::table('edukasi_likes')->count(),
            'total_resep'          => Resep::count(),
            'resep_publish'        => Resep::visibleToUser()->count(),
            'total_resep_likes'    => DB::table('resep_likes')->count(),
            'total_ulasan'         => Review::count(),
            'ulasan_bermedia'      => Review::whereNotNull('media_path')->count(),
            'rating_rata'          => round((float) (Review::avg('rating') ?? 0), 1),
        ];

        $startDate = now()->subDays(6)->startOfDay();
        $penjualanHarian = Pesanan::select(
                DB::raw('DATE(tanggal_order) as date_only'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where(function ($q) use ($statusLunas) {
                $q->whereIn('payment_status', $statusLunas)->orWhere('order_status', 'selesai');
            })
            ->where('tanggal_order', '>=', $startDate)
            ->groupBy('date_only')
            ->get();

        $chart7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');
            $found   = $penjualanHarian->firstWhere('date_only', $dateStr);
            $chart7Hari[] = [
                'date'  => $dateStr,
                'label' => $dateObj->format('d M'),
                'total' => $found ? (int) $found->total : 0,
            ];
        }

        $produkTahuTerlaris = DB::table('detail_pesanan')
            ->join('produk_tahu', 'detail_pesanan.item_id', '=', 'produk_tahu.id')
            ->where('detail_pesanan.item_type', ProdukTahu::class)
            ->select('produk_tahu.nama_produk', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->groupBy('produk_tahu.id', 'produk_tahu.nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $produkLimbahTerlaris = DB::table('detail_pesanan')
            ->join('limbah', 'detail_pesanan.item_id', '=', 'limbah.id')
            ->where('detail_pesanan.item_type', Limbah::class)
            ->select('limbah.nama_limbah as nama_produk', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->groupBy('limbah.id', 'limbah.nama_limbah')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $produkTerlaris = $produkTahuTerlaris
            ->concat($produkLimbahTerlaris)
            ->sortByDesc('total_terjual')
            ->take(5)
            ->values()
            ->map(fn ($item) => [
                'nama_produk'   => $item->nama_produk,
                'total_terjual' => (int) $item->total_terjual,
            ]);

        $pesananTerbaru = Pesanan::with('user:id,username')
            ->orderByDesc('tanggal_order')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'id'             => $p->id,
                'kode_pesanan'   => $p->kode_pesanan,
                'customer'       => $p->user->username ?? 'Pelanggan',
                'total_harga'    => (int) $p->total_harga,
                'order_status'   => $p->order_status,
                'payment_method' => $p->payment_method,
                'payment_status' => $p->payment_status,
                'tanggal'        => $p->tanggal_order?->format('d M Y H:i'),
            ]);

        $refundTerbaru = Refund::with('pesanan.user:id,username')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'id'             => $r->id,
                'kode_pesanan'   => $r->pesanan->kode_pesanan ?? '-',
                'customer'       => $r->pesanan?->user?->username ?? 'Pelanggan',
                'nominal_refund' => (int) $r->nominal_refund,
                'status_refund'  => $r->status_refund,
                'alasan_batal'   => $r->alasan_batal,
                'nama_bank'      => $r->nama_bank,
                'nomor_rekening' => $r->nomor_rekening,
                'atas_nama'      => $r->atas_nama,
                'tanggal'        => $r->tanggal_refund?->format('d M Y H:i'),
            ]);

        $ulasanTerbaru = Review::with(['user:id,username', 'reviewable'])
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn ($rev) => [
                'id'         => $rev->id,
                'customer'   => $rev->user->username ?? 'Pelanggan',
                'item_name'  => $rev->reviewable?->nama_produk ?? $rev->reviewable?->nama_limbah ?? 'Produk',
                'rating'     => (int) $rev->rating,
                'komentar'   => $rev->komentar,
                'media_type' => $rev->media_type,
                'media_url'  => $rev->media_url,
                'tanggal'    => $rev->created_at?->format('d M Y H:i'),
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'stats'           => $stats,
                'chart_7_hari'    => $chart7Hari,
                'produk_terlaris' => $produkTerlaris,
                'pesanan_terbaru' => $pesananTerbaru,
                'refund_terbaru'  => $refundTerbaru,
                'ulasan_terbaru'  => $ulasanTerbaru,
            ],
        ], 200);
    }
}
