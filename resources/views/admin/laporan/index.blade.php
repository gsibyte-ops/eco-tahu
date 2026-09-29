@extends('layouts.admin')
@section('title', 'Laporan & Monitoring')
@section('page-title', 'Laporan & Monitoring')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

{{-- Filter Tanggal --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
            <input type="text" name="dari" id="dari" value="{{ $dari }}" readonly
                   inputmode="none" autocomplete="off"
                   onkeydown="return false" onpaste="return false" ondrop="return false"
                   class="datepicker w-44 px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition cursor-pointer">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
            <input type="text" name="sampai" id="sampai" value="{{ $sampai }}" readonly
                   inputmode="none" autocomplete="off"
                   onkeydown="return false" onpaste="return false" ondrop="return false"
                   class="datepicker w-44 px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition cursor-pointer">
        </div>

        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
            Terapkan Filter
        </button>

        <a href="{{ route('admin.laporan.index') }}"
           class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition whitespace-nowrap">
            Reset
        </a>

        <a href="{{ route('admin.laporan.export', ['dari' => $dari, 'sampai' => $sampai]) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>

        <div class="ml-auto text-xs text-gray-500">
            Periode: <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</span>
        </div>
    </form>
    <p class="text-xs text-gray-400 mt-2">💡 Maksimal rentang filter 1 tahun.</p>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    {{-- Total Pendapatan --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #34d399 0%, #10b981 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Total Pendapatan</p>
        <p class="text-xl font-bold">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</p>
    </div>

    {{-- Total Pesanan --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Total Pesanan</p>
        <p class="text-xl font-bold mb-1">{{ $ringkasan['total_pesanan'] }}</p>
        <div class="flex flex-wrap gap-1 text-[10px]">
            @if ($ringkasan['total_pending'] > 0)
                <span class="px-1.5 py-0.5 rounded bg-white/20">{{ $ringkasan['total_pending'] }} pending</span>
            @endif
            @if ($ringkasan['total_diproses'] > 0)
                <span class="px-1.5 py-0.5 rounded bg-white/20">{{ $ringkasan['total_diproses'] }} proses</span>
            @endif
            @if ($ringkasan['total_dikirim'] > 0)
                <span class="px-1.5 py-0.5 rounded bg-white/20">{{ $ringkasan['total_dikirim'] }} kirim</span>
            @endif
            @if ($ringkasan['total_selesai'] > 0)
                <span class="px-1.5 py-0.5 rounded bg-white/20">{{ $ringkasan['total_selesai'] }} selesai</span>
            @endif
            @if ($ringkasan['total_dibatalkan'] > 0)
                <span class="px-1.5 py-0.5 rounded bg-white/20">{{ $ringkasan['total_dibatalkan'] }} batal</span>
            @endif
        </div>
    </div>

    {{-- Rata-rata Belanja --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Rata-rata Belanja</p>
        <p class="text-xl font-bold">Rp {{ number_format($ringkasan['rata_rata'], 0, ',', '.') }}</p>
    </div>

    {{-- Metode Bayar --}}
    @php
        $totalMetode = $metodePembayaran->sum('jumlah');
    @endphp
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
        <p class="text-sm font-semibold opacity-95 mb-2">Metode Bayar</p>

        @if ($totalMetode > 0)
            <div class="space-y-1.5">
                @foreach ($metodePembayaran as $m)
                    @php $persen = $totalMetode > 0 ? round(($m->jumlah / $totalMetode) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between items-center mb-0.5">
                            <span class="text-xs font-semibold">{{ $m->payment_method }}</span>
                            <span class="text-xs font-bold">{{ $m->jumlah }}x · {{ $persen }}%</span>
                        </div>
                        <div class="h-1.5 bg-white/30 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-white" style="width: {{ $persen }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs opacity-80">Belum ada data</p>
        @endif
    </div>
</div>

{{-- Grafik Penjualan --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="font-bold text-gray-800">Grafik Penjualan</h3>
            @php
                $totalHari = \Carbon\Carbon::parse($dari)->diffInDays(\Carbon\Carbon::parse($sampai)) + 1;
                $hariAdaTransaksi = $grafikHarian->count();
                $persenAktif = $totalHari > 0 ? round(($hariAdaTransaksi / $totalHari) * 100, 1) : 0;
            @endphp
            <p class="text-xs text-gray-500 mt-1">
                📅 Periode: <span class="font-semibold text-gray-700">{{ $totalHari }} hari</span>
                · Transaksi aktif: <span class="font-semibold text-emerald-700">{{ $hariAdaTransaksi }} hari</span>
                <span class="text-gray-400">({{ $persenAktif }}%)</span>
                @if ($modeGrafik === 'bulanan')
                    <span class="ml-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded font-semibold">Tampilan Bulanan</span>
                    <span class="ml-1 text-gray-400">(periode > 31 hari)</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="text-gray-400">💡 Scroll untuk zoom, drag untuk pan</span>
            <button type="button" onclick="resetZoom()"
                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition">
                Reset Zoom
            </button>
        </div>
    </div>
    <div class="h-80">
        <canvas id="laporanChart"></canvas>
    </div>
</div>

{{-- Grid Top Produk & Limbah --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

    {{-- Produk Tahu Terlaris --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">🏆 Produk Tahu Terlaris</h3>
            <button type="button" onclick="openRankingModal('produk')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Lihat Semua Ranking
            </button>
        </div>
        @if ($semuaProduk->count() > 0)
            <div class="space-y-2">
                @foreach ($semuaProduk->take(5) as $i => $p)
                    <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-emerald-50 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0"
                             style="background:#d1fae5; color:#047857;">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $p->nama_produk }}</p>
                            <p class="text-xs text-gray-500">{{ $p->total_qty }} terjual · Rp {{ number_format($p->total_omzet, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
                @if ($semuaProduk->count() > 5)
                    <p class="text-xs text-gray-400 text-center pt-1">+{{ $semuaProduk->count() - 5 }} produk lainnya · Klik "Lihat Semua Ranking"</p>
                @endif
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada produk</p>
        @endif
    </div>

    {{-- Produk Limbah Terlaris --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">♻️ Produk Limbah Terlaris</h3>
            <button type="button" onclick="openRankingModal('limbah')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Lihat Semua Ranking
            </button>
        </div>
        @if ($semuaLimbah->count() > 0)
            <div class="space-y-2">
                @foreach ($semuaLimbah->take(5) as $i => $l)
                    <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-amber-50 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0"
                             style="background:#fef3c7; color:#b45309;">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $l->nama_produk }}</p>
                            <p class="text-xs text-gray-500">{{ $l->total_qty }} {{ $l->satuan }} terjual · Rp {{ number_format($l->total_omzet, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
                @if ($semuaLimbah->count() > 5)
                    <p class="text-xs text-gray-400 text-center pt-1">+{{ $semuaLimbah->count() - 5 }} limbah lainnya · Klik "Lihat Semua Ranking"</p>
                @endif
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada limbah</p>
        @endif
    </div>
</div>

{{-- Top Pelanggan --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800">👑 Top Pelanggan</h3>
        <a href="{{ route('admin.pelanggan.index') }}"
           class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
            Lihat Semua Pelanggan →
        </a>
    </div>
    @if ($topPelanggan->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3">Rank</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Total Pesanan</th>
                        <th class="pb-3 text-right">Total Belanja</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($topPelanggan as $i => $c)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                            <td class="py-3">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                     style="background:#d1fae5; color:#047857;">
                                    {{ $i + 1 }}
                                </div>
                            </td>
                            <td class="py-3">
                                <p class="font-semibold text-gray-800">{{ $c->username }}</p>
                                <p class="text-xs text-gray-500">{{ $c->email }}</p>
                            </td>
                            <td class="py-3 text-gray-600">{{ $c->total_pesanan }}x</td>
                            <td class="py-3 text-right font-bold text-emerald-700">Rp {{ number_format($c->total_belanja ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.pelanggan.show', $c->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
    @endif
</div>

{{-- Peringatan Stok Menipis --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Stok Produk Menipis --}}
    <div class="{{ $stokMenipis->count() > 0 ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }} border rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $stokMenipis->count() > 0 ? 'bg-red-100' : 'bg-emerald-100' }}">
                    @if ($stokMenipis->count() > 0)
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <h3 class="font-bold {{ $stokMenipis->count() > 0 ? 'text-red-800' : 'text-emerald-800' }}">
                    Stok Produk Tahu Menipis (≤10)
                </h3>
            </div>
            @if ($stokMenipis->count() > 0)
                <a href="{{ route('admin.produk-tahu.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">
                    Kelola →
                </a>
            @endif
        </div>

        @if ($stokMenipis->count() > 0)
            <div class="space-y-2">
                @foreach ($stokMenipis as $p)
                    <a href="{{ route('admin.produk-tahu.edit', $p->id) }}"
                       class="flex justify-between items-center bg-white rounded-xl px-3 py-2.5 shadow-sm hover:shadow-md transition">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-800 font-medium truncate">{{ $p->nama_produk }}</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($p->harga, 0, ',', '.') }} · {{ $p->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded-lg flex-shrink-0 ml-2" style="background:#fee2e2; color:#b91c1c;">
                            {{ $p->stok }} sisa
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-emerald-700 text-center py-4">Semua stok produk tahu aman ✓</p>
        @endif
    </div>

    {{-- Stok Limbah Menipis --}}
    <div class="{{ $limbahMenipis->count() > 0 ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }} border rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $limbahMenipis->count() > 0 ? 'bg-red-100' : 'bg-emerald-100' }}">
                    @if ($limbahMenipis->count() > 0)
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <h3 class="font-bold {{ $limbahMenipis->count() > 0 ? 'text-red-800' : 'text-emerald-800' }}">
                    Stok Produk Limbah Menipis (≤10)
                </h3>
            </div>
            @if ($limbahMenipis->count() > 0)
                <a href="{{ route('admin.limbah.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">
                    Kelola →
                </a>
            @endif
        </div>

        @if ($limbahMenipis->count() > 0)
            <div class="space-y-2">
                @foreach ($limbahMenipis as $l)
                    <a href="{{ route('admin.limbah.edit', $l->id) }}"
                       class="flex justify-between items-center bg-white rounded-xl px-3 py-2.5 shadow-sm hover:shadow-md transition">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-800 font-medium truncate">{{ $l->nama_limbah }}</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($l->harga, 0, ',', '.') }}/{{ $l->satuan }} · {{ $l->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded-lg flex-shrink-0 ml-2" style="background:#fee2e2; color:#b91c1c;">
                            {{ $l->stok }} {{ $l->satuan }} sisa
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-emerald-700 text-center py-4">Semua stok produk limbah aman ✓</p>
        @endif
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: Ranking Produk / Limbah --}}
{{-- ============================================ --}}
<div id="rankingModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">
    <div style="width: 100%; max-width: 720px; max-height: 85vh;"
         class="bg-white rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-5 py-4 bg-emerald-600 text-white flex-shrink-0">
            <div>
                <p class="text-xs opacity-80">Ranking</p>
                <h3 class="text-base font-bold" id="rankingTitle">Produk Terlaris</h3>
                <p class="text-xs opacity-80 mt-0.5">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
            </div>
            <button onclick="closeRankingModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3 pr-3">Rank</th>
                        <th class="pb-3">Nama Produk</th>
                        <th class="pb-3 text-center">Stok</th>
                        <th class="pb-3 text-center">Terjual</th>
                        <th class="pb-3 text-right">Omzet</th>
                    </tr>
                </thead>
                <tbody id="rankingBody" class="text-sm"></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
    // ==== DATA RANKING (dari server) ====
    const SEMUA_PRODUK = @json($semuaProdukJson);
    const SEMUA_LIMBAH = @json($semuaLimbahJson);

    // ==== FLATPICKR — 2 INSTANCE TERPISAH BIAR BISA SALING BATASI ====
    let dariPicker, sampaiPicker;

    function makeFlatpickrConfig(extra) {
        return Object.assign({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            allowInput: false,
            monthSelectorType: "static",
            clickOpens: true,
            onReady: function(selectedDates, dateStr, instance) {
                // (1) Paksa altInput readonly — input yang keliatan user
                instance.altInput.setAttribute('readonly', 'readonly');
                instance.altInput.setAttribute('inputmode', 'none');
                instance.altInput.setAttribute('autocomplete', 'off');
                instance.altInput.style.cursor = 'pointer';
                instance.altInput.style.caretColor = 'transparent';

                // (2) Fix input TAHUN di header kalender — block huruf e, +, -, dll
                const yearInput = instance.calendarContainer.querySelector('.cur-year');
                if (yearInput) {
                    yearInput.setAttribute('type', 'text');
                    yearInput.setAttribute('inputmode', 'numeric');
                    yearInput.setAttribute('pattern', '[0-9]*');
                    yearInput.setAttribute('autocomplete', 'off');

                    yearInput.addEventListener('keydown', function(e) {
                        const allowed = ['Backspace','Delete','Tab','Escape','Enter',
                                         'ArrowLeft','ArrowRight','ArrowUp','ArrowDown',
                                         'Home','End'];
                        if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
                        if (!/^[0-9]$/.test(e.key)) e.preventDefault();
                    });

                    yearInput.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
                }
            }
        }, extra || {});
    }

    dariPicker = flatpickr("#dari", makeFlatpickrConfig({
        onChange: function(selectedDates) {
            if (selectedDates[0] && sampaiPicker) {
                const maxDate = new Date(selectedDates[0].getTime());
                maxDate.setFullYear(maxDate.getFullYear() + 1);
                sampaiPicker.set('minDate', selectedDates[0]);
                sampaiPicker.set('maxDate', maxDate);
            }
        }
    }));

    sampaiPicker = flatpickr("#sampai", makeFlatpickrConfig({
        onChange: function(selectedDates) {
            if (selectedDates[0] && dariPicker) {
                const minDate = new Date(selectedDates[0].getTime());
                minDate.setFullYear(minDate.getFullYear() - 1);
                dariPicker.set('minDate', minDate);
                dariPicker.set('maxDate', selectedDates[0]);
            }
        }
    }));

    // ==== RANKING MODAL ====
    function openRankingModal(tipe) {
        const modal = document.getElementById('rankingModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        const data = tipe === 'produk' ? SEMUA_PRODUK : SEMUA_LIMBAH;
        const title = tipe === 'produk' ? 'Ranking Produk Tahu' : 'Ranking Produk Limbah';
        document.getElementById('rankingTitle').textContent = title;

        const tbody = document.getElementById('rankingBody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada data</td></tr>';
            return;
        }

        data.forEach((item, i) => {
            const rankBg = i === 0 ? '#fbbf24' : i === 1 ? '#e5e7eb' : i === 2 ? '#fcd34d' : '#f3f4f6';
            const rankColor = i < 3 ? '#78350f' : '#6b7280';
            const stokBadge = item.stok <= 10 
                ? `<span style="background:#fee2e2; color:#b91c1c; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">${item.stok}${item.satuan ? ' ' + item.satuan : ''}</span>`
                : `<span style="color: #6b7280; font-size: 12px;">${item.stok}${item.satuan ? ' ' + item.satuan : ''}</span>`;
            
            tbody.innerHTML += `
                <tr class="border-b border-gray-50 hover:bg-gray-50/60">
                    <td class="py-3 pr-3">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: ${rankBg}; color: ${rankColor}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                            ${i + 1}
                        </div>
                    </td>
                    <td class="py-3">
                        <p class="font-semibold text-gray-800">${item.nama}</p>
                    </td>
                    <td class="py-3 text-center">${stokBadge}</td>
                    <td class="py-3 text-center font-bold" style="color: #047857;">${item.total_qty}${item.satuan ? ' ' + item.satuan : ''}</td>
                    <td class="py-3 text-right font-bold text-gray-800">Rp ${Number(item.total_omzet).toLocaleString('id-ID')}</td>
                </tr>
            `;
        });
    }

    function closeRankingModal() {
        const modal = document.getElementById('rankingModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // ==== CLOSE ON OUTSIDE CLICK ====
    document.getElementById('rankingModal').addEventListener('click', function(e) {
        if (e.target === this) closeRankingModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRankingModal();
        }
    });

    // ==== CHART ====
    let laporanChart;
    const ctx = document.getElementById('laporanChart');
    if (ctx) {
        laporanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Penjualan',
                    data: @json($chartData),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    },
                    zoom: {
                        pan: { enabled: true, mode: 'x' },
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'x' },
                        limits: { x: { minRange: 3 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            callback: function(v) { return 'Rp ' + (v / 1000) + 'k'; },
                            font: { size: 11 }, color: '#9ca3af'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 15,
                        }
                    }
                }
            }
        });
    }

    function resetZoom() {
        if (laporanChart) laporanChart.resetZoom();
    }
</script>
@endpush