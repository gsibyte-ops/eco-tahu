@extends('layouts.admin')
@section('title', 'Laporan & Monitoring')
@section('page-title', 'Laporan & Monitoring')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

{{-- Filter Tanggal --}}
<div class="glass-card filter-card p-4 mb-6 relative z-30">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Dari Tanggal</label>
            <input type="text" name="dari" id="dari" value="{{ $dari }}" readonly
                   inputmode="none" autocomplete="off"
                   onkeydown="return false" onpaste="return false" ondrop="return false"
                   class="datepicker glass-input w-44 px-4 py-2.5 text-sm cursor-pointer"
                   style="color: rgb(var(--text-primary));">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Sampai Tanggal</label>
            <input type="text" name="sampai" id="sampai" value="{{ $sampai }}" readonly
                   inputmode="none" autocomplete="off"
                   onkeydown="return false" onpaste="return false" ondrop="return false"
                   class="datepicker glass-input w-44 px-4 py-2.5 text-sm cursor-pointer"
                   style="color: rgb(var(--text-primary));">
        </div>

        <button type="submit" class="btn-primary whitespace-nowrap">
            Terapkan Filter
        </button>

        <a href="{{ route('admin.laporan.index') }}"
           class="glass-btn whitespace-nowrap">
            Reset
        </a>

        <a href="{{ route('admin.laporan.export', ['dari' => $dari, 'sampai' => $sampai]) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap"
           style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong)); border: 1px solid rgb(var(--brand) / 0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>

        <div class="ml-auto text-xs txt-secondary">
            Periode: <span class="font-semibold txt-primary">{{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</span>
        </div>
    </form>
    <p class="text-xs txt-muted mt-2">💡 Maksimal rentang filter 1 tahun.</p>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    {{-- Total Pendapatan --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--brand));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--brand));">Total Pendapatan</p>
        <p class="text-2xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</p>
    </div>

    {{-- Total Pesanan --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--info));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--info));">Total Pesanan</p>
        <p class="text-2xl font-extrabold tabular-nums mb-2" style="color: rgb(var(--text-primary));">{{ $ringkasan['total_pesanan'] }}</p>
        <div class="flex flex-wrap gap-1 text-[10px]">
            @if ($ringkasan['total_pending'] > 0)
                <span class="px-1.5 py-0.5 rounded font-bold" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">{{ $ringkasan['total_pending'] }} pending</span>
            @endif
            @if ($ringkasan['total_diproses'] > 0)
                <span class="px-1.5 py-0.5 rounded font-bold" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">{{ $ringkasan['total_diproses'] }} proses</span>
            @endif
            @if ($ringkasan['total_dikirim'] > 0)
                <span class="px-1.5 py-0.5 rounded font-bold" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">{{ $ringkasan['total_dikirim'] }} kirim</span>
            @endif
            @if ($ringkasan['total_selesai'] > 0)
                <span class="px-1.5 py-0.5 rounded font-bold" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">{{ $ringkasan['total_selesai'] }} selesai</span>
            @endif
            @if ($ringkasan['total_dibatalkan'] > 0)
                <span class="px-1.5 py-0.5 rounded font-bold" style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">{{ $ringkasan['total_dibatalkan'] }} batal</span>
            @endif
        </div>
    </div>

    {{-- Rata-rata Belanja --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--accent));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--accent));">Rata-rata Belanja</p>
        <p class="text-2xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($ringkasan['rata_rata'], 0, ',', '.') }}</p>
    </div>

    {{-- Metode Bayar --}}
    @php
        $totalMetode = $metodePembayaran->sum('jumlah');
    @endphp
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--success));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--success));">Metode Bayar</p>

        @if ($totalMetode > 0)
            <div class="space-y-2">
                @foreach ($metodePembayaran as $m)
                    @php $persen = $totalMetode > 0 ? round(($m->jumlah / $totalMetode) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold" style="color: rgb(var(--text-primary));">{{ $m->payment_method }}</span>
                            <span class="text-xs font-bold" style="color: rgb(var(--text-secondary));">{{ $m->jumlah }}x · {{ $persen }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgb(var(--bg-secondary));">
                            <div class="h-full rounded-full" style="width: {{ $persen }}%; background: rgb(var(--success));"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs txt-muted">Belum ada data</p>
        @endif
    </div>
</div>

{{-- Grafik Penjualan --}}
<div class="glass-card p-6 mb-6">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="font-bold txt-primary">Grafik Penjualan</h3>
            @php
                $totalHari = \Carbon\Carbon::parse($dari)->diffInDays(\Carbon\Carbon::parse($sampai)) + 1;
                $hariAdaTransaksi = $grafikHarian->count();
                $persenAktif = $totalHari > 0 ? round(($hariAdaTransaksi / $totalHari) * 100, 1) : 0;
            @endphp
            <p class="text-xs txt-secondary mt-1">
                📅 Periode: <span class="font-semibold txt-primary">{{ $totalHari }} hari</span>
                · Transaksi aktif: <span class="font-semibold txt-brand">{{ $hariAdaTransaksi }} hari</span>
                <span class="txt-muted">({{ $persenAktif }}%)</span>
                @if ($modeGrafik === 'bulanan')
                    <span class="ml-1 px-2 py-0.5 rounded font-semibold" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">Tampilan Bulanan</span>
                    <span class="ml-1 txt-muted">(periode > 31 hari)</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="txt-muted">💡 Scroll untuk zoom, drag untuk pan</span>
            <button type="button" onclick="resetZoom()"
                    class="glass-btn !py-1.5 !px-3 !text-xs">
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

    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold txt-primary">🏆 Produk Tahu Terlaris</h3>
            <button type="button" onclick="openRankingModal('produk')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                    style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Lihat Semua Ranking
            </button>
        </div>
        @if ($semuaProduk->count() > 0)
            <div class="space-y-2">
                @foreach ($semuaProduk->take(5) as $i => $p)
                    <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-emerald-500/10 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0"
                             style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold txt-primary truncate">{{ $p->nama_produk }}</p>
                            <p class="text-xs txt-secondary">{{ $p->total_qty }} terjual · Rp {{ number_format($p->total_omzet, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
                @if ($semuaProduk->count() > 5)
                    <p class="text-xs txt-muted text-center pt-1">+{{ $semuaProduk->count() - 5 }} produk lainnya · Klik "Lihat Semua Ranking"</p>
                @endif
            </div>
        @else
            <p class="text-sm txt-muted text-center py-4">Belum ada produk</p>
        @endif
    </div>

    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold txt-primary">♻️ Produk Limbah Terlaris</h3>
            <button type="button" onclick="openRankingModal('limbah')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                    style="background: rgb(var(--accent-soft)); color: rgb(var(--accent-hover));">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Lihat Semua Ranking
            </button>
        </div>
        @if ($semuaLimbah->count() > 0)
            <div class="space-y-2">
                @foreach ($semuaLimbah->take(5) as $i => $l)
                    <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-amber-500/10 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0"
                             style="background: rgb(var(--accent-soft)); color: rgb(var(--accent-hover));">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold txt-primary truncate">{{ $l->nama_produk }}</p>
                            <p class="text-xs txt-secondary">{{ $l->total_qty }} {{ $l->satuan }} terjual · Rp {{ number_format($l->total_omzet, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
                @if ($semuaLimbah->count() > 5)
                    <p class="text-xs txt-muted text-center pt-1">+{{ $semuaLimbah->count() - 5 }} limbah lainnya · Klik "Lihat Semua Ranking"</p>
                @endif
            </div>
        @else
            <p class="text-sm txt-muted text-center py-4">Belum ada limbah</p>
        @endif
    </div>
</div>

{{-- Top Pelanggan --}}
<div class="glass-card p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold txt-primary">👑 Top Pelanggan</h3>
        <a href="{{ route('admin.pelanggan.index') }}"
           class="text-xs font-semibold" style="color: rgb(var(--brand));">
            Lihat Semua Pelanggan →
        </a>
    </div>
    @if ($topPelanggan->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
                        <th class="pb-3">Rank</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Total Pesanan</th>
                        <th class="pb-3 text-right">Total Belanja</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($topPelanggan as $i => $c)
                        <tr style="border-bottom: 1px solid rgb(var(--border-soft));">
                            <td class="py-3">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                     style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                                    {{ $i + 1 }}
                                </div>
                            </td>
                            <td class="py-3">
                                <p class="font-semibold txt-primary">{{ $c->username }}</p>
                                <p class="text-xs txt-secondary">{{ $c->email }}</p>
                            </td>
                            <td class="py-3 txt-secondary">{{ $c->total_pesanan }}x</td>
                            <td class="py-3 text-right font-bold" style="color: rgb(var(--brand));">Rp {{ number_format($c->total_belanja ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.pelanggan.show', $c->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                   style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
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
        <p class="text-sm txt-muted text-center py-4">Belum ada data</p>
    @endif
</div>

{{-- Peringatan Stok Menipis --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Stok Produk Tahu --}}
    @php $dangerProduk = $stokMenipis->count() > 0; @endphp
    <div class="rounded-2xl p-6"
         style="background: rgb(var(--{{ $dangerProduk ? 'danger-soft' : 'success-soft' }})); border: 1px solid rgb(var(--{{ $dangerProduk ? 'danger' : 'success' }}) / 0.3);">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgb(var(--{{ $dangerProduk ? 'danger' : 'success' }}) / 0.15);">
                    @if ($dangerProduk)
                        <svg class="w-5 h-5" style="color: rgb(var(--danger));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-5 h-5" style="color: rgb(var(--success));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <h3 class="font-bold" style="color: rgb(var(--{{ $dangerProduk ? 'danger' : 'success' }}));">
                    Stok Produk Tahu Menipis (≤10)
                </h3>
            </div>
            @if ($dangerProduk)
                <a href="{{ route('admin.produk-tahu.index') }}" class="text-xs font-semibold" style="color: rgb(var(--danger));">
                    Kelola →
                </a>
            @endif
        </div>

        @if ($dangerProduk)
            <div class="space-y-2">
                @foreach ($stokMenipis as $p)
                    <a href="{{ route('admin.produk-tahu.edit', $p->id) }}"
                       class="flex justify-between items-center rounded-xl px-3 py-2.5 transition glass-card"
                       style="border: 1px solid rgb(var(--border-soft));">
                        <div class="min-w-0">
                            <p class="text-sm font-medium txt-primary truncate">{{ $p->nama_produk }}</p>
                            <p class="text-xs txt-secondary">Rp {{ number_format($p->harga, 0, ',', '.') }} · {{ $p->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded-lg flex-shrink-0 ml-2"
                              style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                            {{ $p->stok }} sisa
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-center py-4" style="color: rgb(var(--success));">Semua stok produk tahu aman ✓</p>
        @endif
    </div>

    {{-- Stok Produk Limbah --}}
    @php $dangerLimbah = $limbahMenipis->count() > 0; @endphp
    <div class="rounded-2xl p-6"
         style="background: rgb(var(--{{ $dangerLimbah ? 'danger-soft' : 'success-soft' }})); border: 1px solid rgb(var(--{{ $dangerLimbah ? 'danger' : 'success' }}) / 0.3);">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: rgb(var(--{{ $dangerLimbah ? 'danger' : 'success' }}) / 0.15);">
                    @if ($dangerLimbah)
                        <svg class="w-5 h-5" style="color: rgb(var(--danger));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-5 h-5" style="color: rgb(var(--success));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <h3 class="font-bold" style="color: rgb(var(--{{ $dangerLimbah ? 'danger' : 'success' }}));">
                    Stok Produk Limbah Menipis (≤10)
                </h3>
            </div>
            @if ($dangerLimbah)
                <a href="{{ route('admin.limbah.index') }}" class="text-xs font-semibold" style="color: rgb(var(--danger));">
                    Kelola →
                </a>
            @endif
        </div>

        @if ($dangerLimbah)
            <div class="space-y-2">
                @foreach ($limbahMenipis as $l)
                    <a href="{{ route('admin.limbah.edit', $l->id) }}"
                       class="flex justify-between items-center rounded-xl px-3 py-2.5 transition glass-card"
                       style="border: 1px solid rgb(var(--border-soft));">
                        <div class="min-w-0">
                            <p class="text-sm font-medium txt-primary truncate">{{ $l->nama_limbah }}</p>
                            <p class="text-xs txt-secondary">Rp {{ number_format($l->harga, 0, ',', '.') }}/{{ $l->satuan }} · {{ $l->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded-lg flex-shrink-0 ml-2"
                              style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                            {{ $l->stok }} {{ $l->satuan }} sisa
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-center py-4" style="color: rgb(var(--success));">Semua stok produk limbah aman ✓</p>
        @endif
    </div>
</div>

{{-- MODAL: Ranking Produk / Limbah --}}
<div id="rankingModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 720px; max-height: 85vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-start justify-between gap-4 px-5 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div class="min-w-0 flex-1">
                <p class="text-xs opacity-80 leading-tight">Ranking</p>
                <h3 class="text-base font-bold leading-tight mt-0.5" id="rankingTitle">Produk Terlaris</h3>
                <p class="text-xs opacity-80 mt-1">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
            </div>
            <div class="flex-shrink-0 pt-1">
                <button onclick="closeRankingModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
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
    const SEMUA_PRODUK = @json($semuaProdukJson);
    const SEMUA_LIMBAH = @json($semuaLimbahJson);

    // ==== FLATPICKR — VALIDASI TANGGAL ====
    const today = new Date();
    today.setHours(23, 59, 59, 999);

    let dariPicker, sampaiPicker;
    let isClearing = false;

    function clearPickerSilent(picker) {
        if (!picker) return;
        try { picker.clear(false); } catch (e) { picker.setDate([], false); }
        if (picker.altInput) picker.altInput.value = '';
        if (picker._input) picker._input.value = '';
        picker.selectedDates = [];
    }

    function resolveConflict(lastTouched) {
        if (!dariPicker || !sampaiPicker) return;
        const d = dariPicker.selectedDates[0];
        const s = sampaiPicker.selectedDates[0];
        if (!d || !s) return;
        if (d.getTime() <= s.getTime()) return;

        if (lastTouched === 'dari') {
            clearPickerSilent(sampaiPicker);
        } else {
            clearPickerSilent(dariPicker);
        }
    }

    function makeFlatpickrConfig(extra) {
        return Object.assign({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            allowInput: false,
            monthSelectorType: "static",
            maxDate: today,
            clickOpens: true,
            onReady: function(selectedDates, dateStr, instance) {
                instance.altInput.setAttribute('readonly', 'readonly');
                instance.altInput.setAttribute('inputmode', 'none');
                instance.altInput.setAttribute('autocomplete', 'off');
                instance.altInput.style.cursor = 'pointer';
                instance.altInput.style.caretColor = 'transparent';
                instance.altInput.classList.add('glass-input');
                instance.altInput.style.color = 'rgb(var(--text-primary))';

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
            },
            onChange: function(selectedDates) {
                if (isClearing) return;
                const isDari = this.input.id === 'dari';
                Promise.resolve().then(function() {
                    resolveConflict(isDari ? 'dari' : 'sampai');
                });
            }
        }, extra || {});
    }

    dariPicker = flatpickr("#dari", makeFlatpickrConfig({}));
    sampaiPicker = flatpickr("#sampai", makeFlatpickrConfig({}));

    // Initial validation
    setTimeout(function() {
        const d = dariPicker?.selectedDates[0];
        const s = sampaiPicker?.selectedDates[0];
        if (d && s && d > s) clearPickerSilent(sampaiPicker);
    }, 150);

    // Safety net form submit
    document.querySelector('#filterForm')?.addEventListener('submit', function(e) {
        const d = dariPicker?.selectedDates[0];
        const s = sampaiPicker?.selectedDates[0];
        if (d && s && d > s) {
            e.preventDefault();
            alert('⚠️ Tanggal "Dari" tidak boleh lebih besar dari "Sampai".');
            return false;
        }
    });

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
            tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center txt-muted">Belum ada data</td></tr>';
            return;
        }

        data.forEach((item, i) => {
            let rankStyle = 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));';
            if (i === 0) rankStyle = 'background: #fbbf24; color: #78350f;';
            else if (i === 1) rankStyle = 'background: #e5e7eb; color: #374151;';
            else if (i === 2) rankStyle = 'background: #fcd34d; color: #78350f;';

            const stokBadge = item.stok <= 10
                ? `<span style="background: rgb(var(--danger-soft)); color: rgb(var(--danger)); padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">${item.stok}${item.satuan ? ' ' + item.satuan : ''}</span>`
                : `<span style="color: rgb(var(--text-secondary)); font-size: 12px;">${item.stok}${item.satuan ? ' ' + item.satuan : ''}</span>`;

            tbody.innerHTML += `
                <tr style="border-bottom: 1px solid rgb(var(--border-soft));">
                    <td class="py-3 pr-3">
                        <div style="width: 32px; height: 32px; border-radius: 8px; ${rankStyle} display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                            ${i + 1}
                        </div>
                    </td>
                    <td class="py-3">
                        <p class="font-semibold txt-primary">${item.nama}</p>
                    </td>
                    <td class="py-3 text-center">${stokBadge}</td>
                    <td class="py-3 text-center font-bold" style="color: rgb(var(--brand));">${item.total_qty}${item.satuan ? ' ' + item.satuan : ''}</td>
                    <td class="py-3 text-right font-bold txt-primary">Rp ${Number(item.total_omzet).toLocaleString('id-ID')}</td>
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

    document.getElementById('rankingModal').addEventListener('click', function(e) {
        if (e.target === this) closeRankingModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRankingModal();
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
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: {
                            callback: function(v) { return 'Rp ' + (v / 1000) + 'k'; },
                            font: { size: 11 },
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#94a3b8',
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