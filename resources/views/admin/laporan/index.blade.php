@extends('layouts.admin')
@section('title', 'Laporan & Monitoring')
@section('page-title', 'Laporan & Monitoring')

@section('content')

{{-- Filter Tanggal --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}"
                   class="px-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}"
                   class="px-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            Terapkan Filter
        </button>

        <a href="{{ route('admin.laporan.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
            Reset
        </a>

        <div class="ml-auto text-xs text-gray-500">
            Periode: <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</span>
        </div>
    </form>
</div>

{{-- Ringkasan --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Pendapatan</p>
        <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Pesanan</p>
        <p class="text-xl font-bold text-gray-800">{{ $ringkasan['total_pesanan'] }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $ringkasan['total_selesai'] }} selesai · {{ $ringkasan['total_dibatalkan'] }} batal</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Rata-rata Belanja</p>
        <p class="text-xl font-bold text-blue-600">Rp {{ number_format($ringkasan['rata_rata'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Metode Bayar</p>
        @forelse ($metodePembayaran as $m)
            <p class="text-xs text-gray-700 mt-1">{{ $m->payment_method }}: <strong>{{ $m->jumlah }}x</strong></p>
        @empty
            <p class="text-xs text-gray-400">Belum ada data</p>
        @endforelse
    </div>
</div>

{{-- Grafik Penjualan --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="font-bold text-gray-800 mb-4">Grafik Penjualan Harian</h3>
    <div class="h-72">
        <canvas id="laporanChart"></canvas>
    </div>
</div>

{{-- Grid Top Produk & Limbah --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

    {{-- Top Produk Tahu --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">🏆 Top Produk Tahu</h3>
        @if ($topProduk->count() > 0)
            <div class="space-y-3">
                @foreach ($topProduk as $i => $p)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold text-xs">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $p->nama_item }}</p>
                            <p class="text-xs text-gray-500">{{ $p->total_qty }} terjual · Rp {{ number_format($p->total_omzet, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
        @endif
    </div>

    {{-- Top Limbah --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">♻️ Top Limbah Terjual</h3>
        @if ($topLimbah->count() > 0)
            <div class="space-y-3">
                @foreach ($topLimbah as $i => $l)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 font-bold text-xs">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $l->nama_item }}</p>
                            <p class="text-xs text-gray-500">{{ $l->total_qty }} terjual · Rp {{ number_format($l->total_omzet, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
        @endif
    </div>
</div>

{{-- Top Pelanggan --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="font-bold text-gray-800 mb-4">👑 Top Pelanggan</h3>
    @if ($topPelanggan->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3">Rank</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Total Pesanan</th>
                        <th class="pb-3 text-right">Total Belanja</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($topPelanggan as $i => $c)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                            <td class="py-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold text-xs">
                                    {{ $i + 1 }}
                                </div>
                            </td>
                            <td class="py-3">
                                <p class="font-semibold text-gray-800">{{ $c->username }}</p>
                                <p class="text-xs text-gray-500">{{ $c->email }}</p>
                            </td>
                            <td class="py-3 text-gray-600">{{ $c->total_pesanan }}x</td>
                            <td class="py-3 text-right font-bold text-emerald-700">Rp {{ number_format($c->total_belanja ?? 0, 0, ',', '.') }}</td>
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
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-bold text-amber-800">Stok Produk Menipis (≤10)</h3>
        </div>

        @if ($stokMenipis->count() > 0)
            <div class="space-y-2">
                @foreach ($stokMenipis as $p)
                    <div class="flex justify-between items-center bg-white/60 rounded-xl px-3 py-2">
                        <span class="text-sm text-gray-800">{{ $p->nama_produk }}</span>
                        <span class="text-xs font-bold text-red-600">{{ $p->stok }} sisa</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-amber-700 text-center py-4">Semua stok produk aman ✅</p>
        @endif
    </div>

    {{-- Stok Limbah Menipis --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-bold text-amber-800">Stok Limbah Menipis (≤10)</h3>
        </div>

        @if ($limbahMenipis->count() > 0)
            <div class="space-y-2">
                @foreach ($limbahMenipis as $l)
                    <div class="flex justify-between items-center bg-white/60 rounded-xl px-3 py-2">
                        <span class="text-sm text-gray-800">{{ $l->nama_limbah }}</span>
                        <span class="text-xs font-bold text-red-600">{{ $l->stok }} {{ $l->satuan }} sisa</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-amber-700 text-center py-4">Semua stok limbah aman ✅</p>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('laporanChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($grafikHarian->pluck('tanggal')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))),
                datasets: [{
                    label: 'Penjualan',
                    data: @json($grafikHarian->pluck('total')),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: '#10b981',
                    borderWidth: 2,
                    borderRadius: 8,
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
                            callback: function(v) { return 'Rp ' + (v / 1000) + 'k'; },
                            font: { size: 11 }, color: '#9ca3af'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af' }
                    }
                }
            }
        });
    }
</script>
@endpush