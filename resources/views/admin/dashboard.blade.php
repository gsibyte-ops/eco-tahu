@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Salam --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, Admin EcoTahu 👋</h2>
    <p class="text-sm text-gray-500">Pantau aktivitas bisnis EcoTahu hari ini.</p>
</div>

{{-- Statistik Cards (CLICKABLE) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

    {{-- Total Pesanan → ke halaman pesanan --}}
    <a href="{{ route('admin.pesanan.index') }}"
       class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">+12.5%</span>
        </div>
        <p class="text-sm text-gray-500 mb-1">Total Pesanan</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_pesanan']) }}</p>
    </a>

    {{-- Total Pendapatan → ke laporan --}}
    <a href="{{ route('admin.laporan.index') }}"
       class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">+8.2%</span>
        </div>
        <p class="text-sm text-gray-500 mb-1">Total Pendapatan</p>
        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
    </a>

    {{-- Stok Produk → ke produk tahu --}}
    <a href="{{ route('admin.produk-tahu.index') }}"
       class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">+15.4%</span>
        </div>
        <p class="text-sm text-gray-500 mb-1">Stok Produk</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_produk']) }} <span class="text-sm font-normal text-gray-500">unit</span></p>
    </a>

    {{-- Pelanggan → ke halaman pelanggan --}}
    <a href="{{ route('admin.pelanggan.index') }}"
       class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">+6.8%</span>
        </div>
        <p class="text-sm text-gray-500 mb-1">Pelanggan</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_pelanggan']) }}</p>
    </a>
</div>

{{-- Banner Refund Pending (CLICKABLE) --}}
@if ($stats['refund_pending'] > 0)
<a href="{{ route('admin.refund.index') }}"
   class="block bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 hover:bg-amber-100 transition">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-amber-800">Ada {{ $stats['refund_pending'] }} pengajuan refund yang menunggu diproses!</p>
                <p class="text-xs text-amber-600">Segera validasi agar pelanggan tidak menunggu lama.</p>
            </div>
        </div>
        <span class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition">Lihat Sekarang →</span>
    </div>
</a>
@endif

{{-- Grafik & Produk Terlaris --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    {{-- Grafik Penjualan --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Penjualan</h3>
            <div class="flex gap-2 text-xs">
                <button id="btnMinggu" onclick="switchChart('week')"
                        class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-semibold transition">
                    Minggu Ini
                </button>
                <button id="btnBulan" onclick="switchChart('month')"
                        class="px-3 py-1 text-gray-500 hover:bg-gray-50 rounded-lg transition">
                    Bulan Ini
                </button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Produk Terlaris --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Produk Terlaris</h3>
        <div class="space-y-4">
            @forelse ($produkTerlaris as $produk)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold text-sm">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $produk->nama_produk }}</p>
                        <p class="text-xs text-gray-500">{{ $produk->total_terjual }} terjual</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Pesanan Terbaru --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800">Pesanan Terbaru</h3>
        <a href="{{ route('admin.pesanan.index') }}"
           class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="pb-3">Order ID</th>
                    <th class="pb-3">Pelanggan</th>
                    <th class="pb-3">Tanggal</th>
                    <th class="pb-3">Total</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse ($pesananTerbaru as $p)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition cursor-pointer"
                        onclick="window.location='{{ route('admin.pesanan.show', $p->id) }}'">
                        <td class="py-3 font-semibold text-gray-800">#{{ $p->kode_pesanan }}</td>
                        <td class="py-3 text-gray-600">{{ $p->user->username ?? '-' }}</td>
                        <td class="py-3 text-gray-500">{{ $p->tanggal_order->format('d M Y') }}</td>
                        <td class="py-3 font-semibold text-gray-800">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="py-3">
                            @php
                                $badge = [
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'diproses' => 'bg-blue-50 text-blue-700',
                                    'dikirim' => 'bg-indigo-50 text-indigo-700',
                                    'selesai' => 'bg-emerald-50 text-emerald-700',
                                    'dibatalkan' => 'bg-red-50 text-red-700',
                                ][$p->order_status] ?? 'bg-gray-50 text-gray-700';
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $badge }}">
                                {{ ucfirst($p->order_status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartDataWeek = {
        labels: @json($chartLabels),
        data: @json($chartData)
    };

    const chartDataMonth = {
        labels: @json($chartLabelsBulanan),
        data: @json($chartDataBulanan)
    };

    let salesChart;

    function buildChart(labels, data) {
        const ctx = document.getElementById('salesChart');
        if (salesChart) salesChart.destroy();

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
                    pointBorderColor: '#fff',
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
                        backgroundColor: '#111827',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            callback: v => 'Rp ' + (v / 1000) + 'k',
                            font: { size: 11 }, color: '#9ca3af'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#9ca3af', maxRotation: 0 }
                    }
                }
            }
        });
    }

    function switchChart(mode) {
        const btnMinggu = document.getElementById('btnMinggu');
        const btnBulan = document.getElementById('btnBulan');

        if (mode === 'week') {
            buildChart(chartDataWeek.labels, chartDataWeek.data);
            btnMinggu.className = 'px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-semibold transition';
            btnBulan.className = 'px-3 py-1 text-gray-500 hover:bg-gray-50 rounded-lg transition';
        } else {
            buildChart(chartDataMonth.labels, chartDataMonth.data);
            btnBulan.className = 'px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-semibold transition';
            btnMinggu.className = 'px-3 py-1 text-gray-500 hover:bg-gray-50 rounded-lg transition';
        }
    }

    // Init chart minggu
    buildChart(chartDataWeek.labels, chartDataWeek.data);
</script>
@endpush