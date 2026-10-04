@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Salam --}}
<div class="mb-6">
    <h2 class="text-2xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Selamat Datang, Admin EcoTahu 👋</h2>
    <p class="text-sm mt-1" style="color: rgb(var(--text-secondary));">Pantau aktivitas bisnis EcoTahu hari ini.</p>
</div>

{{-- Statistik Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

    <a href="{{ route('admin.pesanan.index') }}" class="glass-card p-5 hover:-translate-y-0.5 transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgb(var(--info-soft));">
                <svg class="w-5 h-5" style="color: rgb(var(--info));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+12.5%</span>
        </div>
        <p class="text-sm mb-1" style="color: rgb(var(--text-secondary));">Total Pesanan</p>
        <p class="text-2xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_pesanan']) }}</p>
    </a>

    <a href="{{ route('admin.laporan.index') }}" class="glass-card p-5 hover:-translate-y-0.5 transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgb(var(--brand-soft));">
                <svg class="w-5 h-5" style="color: rgb(var(--brand-strong));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+8.2%</span>
        </div>
        <p class="text-sm mb-1" style="color: rgb(var(--text-secondary));">Total Pendapatan</p>
        <p class="text-xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
    </a>

    <a href="{{ route('admin.produk-tahu.index') }}" class="glass-card p-5 hover:-translate-y-0.5 transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgb(var(--warning-soft));">
                <svg class="w-5 h-5" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+15.4%</span>
        </div>
        <p class="text-sm mb-1" style="color: rgb(var(--text-secondary));">Stok Produk</p>
        <p class="text-2xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">
            {{ number_format($stats['total_produk']) }}
            <span class="text-sm font-normal" style="color: rgb(var(--text-muted));">unit</span>
        </p>
        <div class="mt-3 pt-3 flex items-center gap-2 text-xs" style="border-top: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-secondary));">
            <span>📦 Tahu: <b style="color: rgb(var(--text-primary));">{{ number_format($stats['total_tahu'] ?? 0) }}</b></span>
            <span style="color: rgb(var(--text-faint));">|</span>
            <span>♻️ Limbah: <b style="color: rgb(var(--text-primary));">{{ number_format($stats['total_limbah'] ?? 0) }}</b></span>
        </div>
    </a>

    <a href="{{ route('admin.pelanggan.index') }}" class="glass-card p-5 hover:-translate-y-0.5 transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgb(var(--info-soft));">
                <svg class="w-5 h-5" style="color: rgb(var(--info));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+6.8%</span>
        </div>
        <p class="text-sm mb-1" style="color: rgb(var(--text-secondary));">Pelanggan</p>
        <p class="text-2xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_pelanggan']) }}</p>
    </a>
</div>

{{-- Refund Pending Banner --}}
@if ($stats['refund_pending'] > 0)
<a href="{{ route('admin.refund.index') }}" class="block glass-amber rounded-2xl p-4 mb-6 hover:opacity-90 transition" style="border-color: rgb(var(--warning) / 0.4);">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgb(var(--warning) / 0.2);">
                <svg class="w-5 h-5" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="font-bold" style="color: rgb(var(--warning));">Ada {{ $stats['refund_pending'] }} pengajuan refund menunggu diproses!</p>
                <p class="text-xs opacity-90" style="color: rgb(var(--warning));">Segera validasi agar pelanggan tidak menunggu lama.</p>
            </div>
        </div>
        <span class="px-4 py-2 text-white text-sm font-semibold rounded-xl transition flex-shrink-0" style="background: rgb(var(--warning));">Lihat →</span>
    </div>
</a>
@endif

{{-- Grafik & Produk Terlaris --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    <div class="lg:col-span-2 glass-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold" style="color: rgb(var(--text-primary));">Penjualan</h3>
            <div class="flex gap-2 text-xs">
                <button id="btnMinggu" onclick="switchChart('week')" class="px-3 py-1 rounded-lg font-semibold transition" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">Minggu Ini</button>
                <button id="btnBulan" onclick="switchChart('month')" class="px-3 py-1 rounded-lg transition" style="color: rgb(var(--text-secondary));">Bulan Ini</button>
            </div>
        </div>
        <div class="h-64"><canvas id="salesChart"></canvas></div>
    </div>

    <div class="glass-card p-6">
        <h3 class="font-bold mb-4" style="color: rgb(var(--text-primary));">Produk Terlaris</h3>
        <div class="space-y-4">
            @forelse ($produkTerlaris as $produk)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">{{ $loop->iteration }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate" style="color: rgb(var(--text-primary));">{{ $produk->nama_produk }}</p>
                        <p class="text-xs" style="color: rgb(var(--text-secondary));">{{ $produk->total_terjual }} terjual</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-center py-4" style="color: rgb(var(--text-muted));">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Pesanan Terbaru --}}
<div class="glass-card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold" style="color: rgb(var(--text-primary));">Pesanan Terbaru</h3>
        <a href="{{ route('admin.pesanan.index') }}" class="text-sm font-semibold hover:opacity-80" style="color: rgb(var(--brand));">Lihat Semua →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
                    <th class="pb-3">Order ID</th>
                    <th class="pb-3">Pelanggan</th>
                    <th class="pb-3">Tanggal</th>
                    <th class="pb-3">Total</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse ($pesananTerbaru as $p)
                    @php
                        $badgeStyle = [
                            'pending'    => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning));',
                            'diproses'   => 'background: rgb(var(--info-soft)); color: rgb(var(--info));',
                            'dikirim'    => 'background: rgb(var(--info-soft)); color: rgb(var(--info));',
                            'selesai'    => 'background: rgb(var(--success-soft)); color: rgb(var(--success));',
                            'dibatalkan' => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger));',
                        ][$p->order_status] ?? 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));';
                    @endphp
                    <tr class="cursor-pointer hover:opacity-80 transition" style="border-bottom: 1px solid rgb(var(--border-soft));" onclick="window.location='{{ route('admin.pesanan.show', $p->id) }}'">
                        <td class="py-3 font-semibold" style="color: rgb(var(--text-primary));">#{{ $p->kode_pesanan }}</td>
                        <td class="py-3" style="color: rgb(var(--text-secondary));">{{ $p->user->username ?? '-' }}</td>
                        <td class="py-3" style="color: rgb(var(--text-secondary));">{{ $p->tanggal_order->format('d M Y') }}</td>
                        <td class="py-3 font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="py-3">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="{{ $badgeStyle }}">{{ ucfirst($p->order_status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center" style="color: rgb(var(--text-muted));">Belum ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartDataWeek  = { labels: @json($chartLabels),        data: @json($chartData) };
    const chartDataMonth = { labels: @json($chartLabelsBulanan), data: @json($chartDataBulanan) };

    let salesChart;

    function buildChart(labels, data) {
        const ctx = document.getElementById('salesChart');
        if (salesChart) salesChart.destroy();

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: isDark ? '#0f172a' : '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#111827',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: { label: c => 'Rp ' + c.parsed.y.toLocaleString('id-ID') }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)' },
                        ticks: { callback: v => 'Rp ' + (v / 1000) + 'k', font: { size: 11 }, color: isDark ? '#94a3b8' : '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: isDark ? '#94a3b8' : '#9ca3af', maxRotation: 0 }
                    }
                }
            }
        });
    }

    function switchChart(mode) {
        const btnMinggu = document.getElementById('btnMinggu');
        const btnBulan  = document.getElementById('btnBulan');
        const activeStyle = 'background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));';

        if (mode === 'week') {
            buildChart(chartDataWeek.labels, chartDataWeek.data);
            btnMinggu.style.cssText = activeStyle;
            btnBulan.style.cssText  = 'color: rgb(var(--text-secondary));';
        } else {
            buildChart(chartDataMonth.labels, chartDataMonth.data);
            btnBulan.style.cssText  = activeStyle;
            btnMinggu.style.cssText = 'color: rgb(var(--text-secondary));';
        }
    }

    buildChart(chartDataWeek.labels, chartDataWeek.data);
</script>
@endpush