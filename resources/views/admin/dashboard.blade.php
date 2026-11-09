@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Salam --}}
<div class="mb-5 md:mb-6">
    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Selamat Datang, Admin 👋</h2>
    <p class="text-xs md:text-sm mt-1" style="color: rgb(var(--text-secondary));">Pantau aktivitas bisnis EcoTahu hari ini.</p>
</div>

{{-- Statistik Cards (Di-mobile jadi 2 kolom biar ga sesak) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5 mb-5 md:mb-6">

    <a href="{{ route('admin.pesanan.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--info-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--info));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <span class="text-[10px] md:text-xs font-semibold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+12.5%</span>
        </div>
        <div>
            <p class="text-[11px] md:text-sm mb-1 truncate" style="color: rgb(var(--text-secondary));">Total Pesanan</p>
            <p id="stat-total-pesanan" class="text-lg md:text-2xl font-extrabold tabular-nums leading-none" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_pesanan']) }}</p>
        </div>
    </a>

    <a href="{{ route('admin.laporan.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--brand-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--brand-strong));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] md:text-xs font-semibold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+8.2%</span>
        </div>
        <div>
            <p class="text-[11px] md:text-sm mb-1 truncate" style="color: rgb(var(--text-secondary));">Pendapatan</p>
            <p id="stat-total-pendapatan" class="text-base md:text-xl font-extrabold tabular-nums leading-none" style="color: rgb(var(--text-primary));">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
        </div>
    </a>

    <a href="{{ route('admin.produk-tahu.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--warning-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <span class="text-[10px] md:text-xs font-semibold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+15.4%</span>
        </div>
        <div>
            <p class="text-[11px] md:text-sm mb-1 truncate" style="color: rgb(var(--text-secondary));">Stok Produk</p>
            <p class="text-lg md:text-2xl font-extrabold tabular-nums leading-none" style="color: rgb(var(--text-primary));">
                <span id="stat-total-produk">{{ number_format($stats['total_produk']) }}</span>
                <span class="text-[10px] md:text-sm font-normal" style="color: rgb(var(--text-muted));">unit</span>
            </p>
            <div class="mt-2 pt-2 flex flex-col xl:flex-row xl:items-center gap-1 xl:gap-2 text-[10px] md:text-xs" style="border-top: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-secondary));">
                <span>📦 Tahu: <b id="stat-total-tahu" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_tahu'] ?? 0) }}</b></span>
                <span class="hidden xl:block" style="color: rgb(var(--text-faint));">|</span>
                <span>♻️ Limbah: <b id="stat-total-limbah" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_limbah'] ?? 0) }}</b></span>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.pelanggan.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--info-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--info));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-[10px] md:text-xs font-semibold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">+6.8%</span>
        </div>
        <div>
            <p class="text-[11px] md:text-sm mb-1 truncate" style="color: rgb(var(--text-secondary));">Pelanggan</p>
            <p id="stat-total-pelanggan" class="text-lg md:text-2xl font-extrabold tabular-nums leading-none" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_pelanggan']) }}</p>
        </div>
    </a>
</div>

{{-- Refund Pending Banner --}}
<div id="refund-banner" class="{{ $stats['refund_pending'] > 0 ? '' : 'hidden' }}">
    <a href="{{ route('admin.refund.index') }}" class="block glass-amber rounded-xl md:rounded-2xl p-3 md:p-4 mb-5 md:mb-6 hover:opacity-90 transition" style="border-color: rgb(var(--warning) / 0.4);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgb(var(--warning) / 0.2);">
                    <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-sm md:text-base font-bold" style="color: rgb(var(--warning));">Ada <span id="stat-refund-pending">{{ $stats['refund_pending'] }}</span> refund menunggu!</p>
                    <p class="text-[10px] md:text-xs opacity-90 mt-0.5" style="color: rgb(var(--warning));">Segera validasi agar pelanggan tidak menunggu lama.</p>
                </div>
            </div>
            <span class="px-3 py-1.5 md:px-4 md:py-2 text-white text-xs md:text-sm font-semibold rounded-lg md:rounded-xl transition flex-shrink-0 text-center" style="background: rgb(var(--warning));">Lihat →</span>
        </div>
    </a>
</div>

{{-- Grafik & Produk Terlaris --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5 mb-5 md:mb-6">

    <div class="lg:col-span-2 glass-card p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-3">
            <h3 class="font-bold text-sm md:text-base" style="color: rgb(var(--text-primary));">Penjualan</h3>
            <div class="flex gap-2 text-[10px] md:text-xs">
                <button id="btnMinggu" onclick="switchChart('week')" class="px-2.5 py-1 md:px-3 md:py-1.5 rounded-lg font-semibold transition" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">Minggu Ini</button>
                <button id="btnBulan" onclick="switchChart('month')" class="px-2.5 py-1 md:px-3 md:py-1.5 rounded-lg transition" style="color: rgb(var(--text-secondary));">Bulan Ini</button>
            </div>
        </div>
        <div class="h-56 md:h-64"><canvas id="salesChart"></canvas></div>
    </div>

    <div class="glass-card p-4 md:p-6">
        <h3 class="font-bold text-sm md:text-base mb-4" style="color: rgb(var(--text-primary));">Produk Terlaris</h3>
        <div class="space-y-3 md:space-y-4">
            @forelse ($produkTerlaris as $produk)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center font-bold text-xs md:text-sm shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">{{ $loop->iteration }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs md:text-sm font-semibold truncate" style="color: rgb(var(--text-primary));">{{ $produk->nama_produk }}</p>
                        <p class="text-[10px] md:text-xs" style="color: rgb(var(--text-secondary));">{{ $produk->total_terjual }} terjual</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-center py-4" style="color: rgb(var(--text-muted));">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Pesanan Terbaru --}}
<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-sm md:text-base" style="color: rgb(var(--text-primary));">Pesanan Terbaru</h3>
        <a href="{{ route('admin.pesanan.index') }}" class="text-xs md:text-sm font-semibold hover:opacity-80" style="color: rgb(var(--brand));">Lihat Semua →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[600px]">
            <thead>
                <tr class="text-[10px] md:text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
                    <th class="pb-3 pr-2">Order ID</th>
                    <th class="pb-3 pr-2">Pelanggan</th>
                    <th class="pb-3 pr-2">Tanggal</th>
                    <th class="pb-3 pr-2">Total</th>
                    <th class="pb-3 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="text-xs md:text-sm">
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
                        <td class="py-3 pr-2 font-semibold" style="color: rgb(var(--text-primary));">#{{ $p->kode_pesanan }}</td>
                        <td class="py-3 pr-2" style="color: rgb(var(--text-secondary));">{{ $p->user->username ?? '-' }}</td>
                        <td class="py-3 pr-2" style="color: rgb(var(--text-secondary));">{{ $p->tanggal_order->format('d M Y') }}</td>
                        <td class="py-3 pr-2 font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="py-3 text-right">
                            <span class="px-2 py-1 rounded-md md:rounded-lg text-[10px] md:text-xs font-bold whitespace-nowrap" style="{{ $badgeStyle }}">{{ ucfirst($p->order_status) }}</span>
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
    // ==== REAL-TIME STATS ====
    function fetchRealtimeStats() {
        fetch('{{ route('admin.dashboard.stats') }}')
            .then(res => res.json())
            .then(data => {
                document.getElementById('stat-total-pesanan').innerText = data.total_pesanan.toLocaleString('id-ID');
                document.getElementById('stat-total-pendapatan').innerText = 'Rp ' + data.total_pendapatan.toLocaleString('id-ID');
                document.getElementById('stat-total-produk').innerText = data.total_produk.toLocaleString('id-ID');
                document.getElementById('stat-total-tahu').innerText = data.total_tahu.toLocaleString('id-ID');
                document.getElementById('stat-total-limbah').innerText = data.total_limbah.toLocaleString('id-ID');
                document.getElementById('stat-total-pelanggan').innerText = data.total_pelanggan.toLocaleString('id-ID');
                
                // Handle Refund Banner
                const refundBanner = document.getElementById('refund-banner');
                const refundPendingSpan = document.getElementById('stat-refund-pending');
                if(data.refund_pending > 0) {
                    refundBanner.classList.remove('hidden');
                    refundPendingSpan.innerText = data.refund_pending;
                } else {
                    refundBanner.classList.add('hidden');
                }
            })
            .catch(err => console.error('Gagal fetch stats:', err));
    }
    // Polling tiap 5 detik
    setInterval(fetchRealtimeStats, 5000);

    // ==== CHART.JS ====
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
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: isDark ? '#0f172a' : '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#111827',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: c => 'Rp ' + c.parsed.y.toLocaleString('id-ID') }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)' },
                        ticks: { callback: v => 'Rp ' + (v / 1000) + 'k', font: { size: 10 }, color: isDark ? '#94a3b8' : '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, color: isDark ? '#94a3b8' : '#9ca3af', maxRotation: 0 }
                    }
                }
            }
        });
    }

    function switchChart(mode) {
        const btnMinggu = document.getElementById('btnMinggu');
        const btnBulan  = document.getElementById('btnBulan');
        const activeStyle = 'background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));';
        const inactiveStyle = 'color: rgb(var(--text-secondary));';

        if (mode === 'week') {
            buildChart(chartDataWeek.labels, chartDataWeek.data);
            btnMinggu.style.cssText = activeStyle;
            btnBulan.style.cssText  = inactiveStyle;
        } else {
            buildChart(chartDataMonth.labels, chartDataMonth.data);
            btnBulan.style.cssText  = activeStyle;
            btnMinggu.style.cssText = inactiveStyle;
        }
    }

    // Inisialisasi chart pertama kali
    buildChart(chartDataWeek.labels, chartDataWeek.data);
</script>
@endpush