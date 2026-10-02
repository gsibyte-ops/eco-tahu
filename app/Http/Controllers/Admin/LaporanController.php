<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Limbah;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari = $request->filled('dari')
            ? $request->dari
            : now()->startOfMonth()->format('Y-m-d');

        $sampai = $request->filled('sampai')
            ? $request->sampai
            : now()->format('Y-m-d');

        $dariCarbon = Carbon::parse($dari);
        $sampaiCarbon = Carbon::parse($sampai);

        // 👈 TAMBAH: gak boleh masa depan
        if ($dariCarbon->isFuture()) {
            $dariCarbon = now();
            $dari = $dariCarbon->format('Y-m-d');
        }
        if ($sampaiCarbon->isFuture()) {
            $sampaiCarbon = now();
            $sampai = $sampaiCarbon->format('Y-m-d');
        }

        // ============ VALIDASI: MAX RANGE 1 TAHUN ============
        if ($dariCarbon->diffInDays($sampaiCarbon) > 365) {
            $sampaiCarbon = $dariCarbon->copy()->addYear()->subDay();
            $sampai = $sampaiCarbon->format('Y-m-d');
        }

        // Swap kalau kebalik
        if ($dariCarbon->gt($sampaiCarbon)) {
            [$dariCarbon, $sampaiCarbon] = [$sampaiCarbon, $dariCarbon];
            $dari = $dariCarbon->format('Y-m-d');
            $sampai = $sampaiCarbon->format('Y-m-d');
        }

        $periode = [
            'dari' => $dari . ' 00:00:00',
            'sampai' => $sampai . ' 23:59:59',
        ];

        // ============ RINGKASAN ============
        $ringkasan = [
            'total_pendapatan' => Pesanan::where('payment_status', 'paid')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])
                ->sum('total_harga'),

            'total_pesanan' => Pesanan::whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])->count(),

            'total_pending' => Pesanan::where('order_status', 'pending')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])->count(),

            'total_diproses' => Pesanan::where('order_status', 'diproses')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])->count(),

            'total_dikirim' => Pesanan::where('order_status', 'dikirim')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])->count(),

            'total_selesai' => Pesanan::where('order_status', 'selesai')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])->count(),

            'total_dibatalkan' => Pesanan::where('order_status', 'dibatalkan')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])->count(),

            'rata_rata' => Pesanan::where('payment_status', 'paid')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])
                ->avg('total_harga') ?? 0,
        ];

        // ============ METODE BAYAR ============
        $metodePembayaran = Pesanan::select(
                'payment_method',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('order_status', '!=', 'dibatalkan')
            ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])
            ->groupBy('payment_method')
            ->get();

        // ============ GRAFIK HARIAN ============
        $grafikHarian = Pesanan::select(
                DB::raw('DATE(tanggal_order) as tanggal'),
                DB::raw('SUM(total_harga) as total'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('payment_status', 'paid')
            ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // ============ CHART DATA — HARIAN ATAU BULANAN ============
        $selisihHari = $dariCarbon->diffInDays($sampaiCarbon);
        $modeGrafik = $selisihHari > 31 ? 'bulanan' : 'harian';

        $chartLabels = [];
        $chartData = [];

        if ($modeGrafik === 'harian') {
            $dataHarian = $grafikHarian->keyBy('tanggal');
            $cursor = $dariCarbon->copy();
            while ($cursor->lte($sampaiCarbon)) {
                $tglStr = $cursor->format('Y-m-d');
                $chartLabels[] = $cursor->format('d M Y');
                $chartData[] = isset($dataHarian[$tglStr]) ? (float) $dataHarian[$tglStr]->total : 0;
                $cursor->addDay();
            }
        } else {
            $dataBulanan = Pesanan::select(
                    DB::raw("DATE_FORMAT(tanggal_order, '%Y-%m') as bulan"),
                    DB::raw('SUM(total_harga) as total')
                )
                ->where('payment_status', 'paid')
                ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get()
                ->keyBy('bulan');

            $cursor = $dariCarbon->copy()->startOfMonth();
            $end = $sampaiCarbon->copy()->startOfMonth();
            while ($cursor->lte($end)) {
                $keyBulan = $cursor->format('Y-m');
                $chartLabels[] = $cursor->format('M Y');
                $chartData[] = isset($dataBulanan[$keyBulan]) ? (float) $dataBulanan[$keyBulan]->total : 0;
                $cursor->addMonth();
            }
        }

        // ============ SEMUA PRODUK ============
        $semuaProduk = DB::table('produk_tahu')
            ->leftJoin('detail_pesanan', function ($join) use ($periode) {
                $join->on('produk_tahu.id', '=', 'detail_pesanan.item_id')
                     ->where('detail_pesanan.item_type', '=', 'App\\Models\\ProdukTahu')
                     ->whereIn('detail_pesanan.pesanan_id', function ($sub) use ($periode) {
                         $sub->select('id')
                             ->from('pesanan')
                             ->where('payment_status', 'paid')
                             ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']]);
                     });
            })
            ->select(
                'produk_tahu.id',
                'produk_tahu.nama_produk',
                'produk_tahu.harga',
                'produk_tahu.stok',
                DB::raw('COALESCE(SUM(detail_pesanan.jumlah), 0) as total_qty'),
                DB::raw('COALESCE(SUM(detail_pesanan.subtotal), 0) as total_omzet')
            )
            ->groupBy('produk_tahu.id', 'produk_tahu.nama_produk', 'produk_tahu.harga', 'produk_tahu.stok')
            ->orderByDesc('total_qty')
            ->get();

        // ============ SEMUA LIMBAH ============
        $semuaLimbah = DB::table('limbah')
            ->leftJoin('detail_pesanan', function ($join) use ($periode) {
                $join->on('limbah.id', '=', 'detail_pesanan.item_id')
                     ->where('detail_pesanan.item_type', '=', 'App\\Models\\Limbah')
                     ->whereIn('detail_pesanan.pesanan_id', function ($sub) use ($periode) {
                         $sub->select('id')
                             ->from('pesanan')
                             ->where('payment_status', 'paid')
                             ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']]);
                     });
            })
            ->select(
                'limbah.id',
                'limbah.nama_limbah as nama_produk',
                'limbah.harga',
                'limbah.stok',
                'limbah.satuan',
                DB::raw('COALESCE(SUM(detail_pesanan.jumlah), 0) as total_qty'),
                DB::raw('COALESCE(SUM(detail_pesanan.subtotal), 0) as total_omzet')
            )
            ->groupBy('limbah.id', 'limbah.nama_limbah', 'limbah.harga', 'limbah.stok', 'limbah.satuan')
            ->orderByDesc('total_qty')
            ->get();

        // ============ TOP PELANGGAN ============
        $topPelanggan = User::where('role_id', 2)
            ->withCount(['pesanan as total_pesanan' => function ($q) use ($periode) {
                $q->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']]);
            }])
            ->withSum(['pesanan as total_belanja' => function ($q) use ($periode) {
                $q->where('payment_status', 'paid')
                  ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']]);
            }], 'total_harga')
            ->with(['pesanan' => function ($q) use ($periode) {
                $q->with(['detail', 'refund', 'pembayaran'])
                  ->whereBetween('tanggal_order', [$periode['dari'], $periode['sampai']])
                  ->orderByDesc('tanggal_order')
                  ->limit(20);
            }])
            ->having('total_pesanan', '>', 0)
            ->orderByDesc('total_belanja')
            ->limit(10)
            ->get();

        // ============ STOK MENIPIS ============
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

        // ============ JSON UNTUK MODAL ============
        $semuaProdukJson = $semuaProduk->map(function ($p) {
            return [
                'nama' => $p->nama_produk,
                'stok' => $p->stok,
                'total_qty' => (int) $p->total_qty,
                'total_omzet' => (int) $p->total_omzet,
            ];
        })->values();

        $semuaLimbahJson = $semuaLimbah->map(function ($l) {
            return [
                'nama' => $l->nama_produk,
                'stok' => $l->stok,
                'satuan' => $l->satuan,
                'total_qty' => (int) $l->total_qty,
                'total_omzet' => (int) $l->total_omzet,
            ];
        })->values();

        return view('admin.laporan.index', compact(
            'dari', 'sampai', 'ringkasan', 'grafikHarian',
            'chartLabels', 'chartData', 'modeGrafik',
            'semuaProduk', 'semuaLimbah', 'topPelanggan',
            'metodePembayaran', 'stokMenipis', 'limbahMenipis',
            'semuaProdukJson', 'semuaLimbahJson'
        ));
    }

    public function export(Request $request)
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->filled('sampai') ? $request->sampai : now()->format('Y-m-d');

        $dariCarbon = Carbon::parse($dari);
        $sampaiCarbon = Carbon::parse($sampai);

        // 👈 TAMBAH: gak boleh masa depan
        if ($dariCarbon->isFuture()) {
            $dariCarbon = now();
            $dari = $dariCarbon->format('Y-m-d');
        }
        if ($sampaiCarbon->isFuture()) {
            $sampaiCarbon = now();
            $sampai = $sampaiCarbon->format('Y-m-d');
        }

        // Validasi max 1 tahun
        if ($dariCarbon->diffInDays($sampaiCarbon) > 365) {
            $sampaiCarbon = $dariCarbon->copy()->addYear()->subDay();
            $sampai = $sampaiCarbon->format('Y-m-d');
        }

        // Swap kalau kebalik
        if ($dariCarbon->gt($sampaiCarbon)) {
            [$dariCarbon, $sampaiCarbon] = [$sampaiCarbon, $dariCarbon];
            $dari = $dariCarbon->format('Y-m-d');
            $sampai = $sampaiCarbon->format('Y-m-d');
        }

        $pesanan = Pesanan::with(['user', 'detail'])
            ->whereBetween('tanggal_order', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
            ->orderBy('tanggal_order')
            ->get();

        $filename = 'laporan-penjualan-' . $dari . '-sd-' . $sampai . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($pesanan, $dari, $sampai) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['LAPORAN PENJUALAN ECOTAHU']);
            fputcsv($file, ['Periode', $dari . ' s/d ' . $sampai]);
            fputcsv($file, []);

            fputcsv($file, [
                'No', 'Kode Pesanan', 'Tanggal', 'Pelanggan', 'Email',
                'Produk', 'Qty', 'Subtotal (Rp)', 'Ongkir (Rp)', 'Total (Rp)',
                'Metode Bayar', 'Status Bayar', 'Status Pesanan',
            ]);

            $no = 1;
            foreach ($pesanan as $p) {
                if ($p->detail->count() > 0) {
                    foreach ($p->detail as $i => $d) {
                        fputcsv($file, [
                            $no,
                            $i === 0 ? $p->kode_pesanan : '',
                            $i === 0 ? $p->tanggal_order->format('d M Y H:i') : '',
                            $i === 0 ? ($p->user->username ?? '-') : '',
                            $i === 0 ? ($p->user->email ?? '-') : '',
                            $d->nama_item,
                            $d->jumlah,
                            $d->subtotal,
                            $i === 0 ? $p->ongkir : '',
                            $i === 0 ? $p->total_harga : '',
                            $i === 0 ? $p->payment_method : '',
                            $i === 0 ? ucfirst($p->payment_status) : '',
                            $i === 0 ? ucfirst($p->order_status) : '',
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        $no, $p->kode_pesanan, $p->tanggal_order->format('d M Y H:i'),
                        $p->user->username ?? '-', $p->user->email ?? '-',
                        '-', 0, 0, $p->ongkir, $p->total_harga,
                        $p->payment_method, ucfirst($p->payment_status), ucfirst($p->order_status),
                    ]);
                }
                $no++;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}