@extends('layouts.admin')
@section('title', 'Kelola Refund')
@section('page-title', 'Kelola Refund')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl p-4 shadow-md text-white" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Pending</p>
        <p class="text-3xl font-bold">{{ $stats['pending'] }}</p>
    </div>
    <div class="rounded-2xl p-4 shadow-md text-white" style="background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Diproses</p>
        <p class="text-3xl font-bold">{{ $stats['diproses'] }}</p>
    </div>
    <div class="rounded-2xl p-4 shadow-md text-white" style="background: linear-gradient(135deg, #34d399 0%, #10b981 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Selesai</p>
        <p class="text-3xl font-bold">{{ $stats['selesai'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-400 to-emerald-600"></div>
        <div class="pl-3">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center text-sm">💰</span>
                <p class="text-sm font-semibold text-gray-500">Total Refund</p>
            </div>
            <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari</label>
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Kode pesanan / nama pelanggan / nama produk..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div style="width: 180px;">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
            <select name="status"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
            Filter
        </button>

        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.refund.index') }}"
               class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60 border-b border-gray-100">
                    <th class="px-5 py-3">ID</th>
                    <th class="px-5 py-3">Kode Pesanan</th>
                    <th class="px-5 py-3">Pelanggan</th>
                    <th class="px-5 py-3">Produk</th>
                    <th class="px-5 py-3">Nominal</th>
                    <th class="px-5 py-3">Alasan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($refund as $r)
                    @php
                        $statusStyle = [
                            'pending'    => 'background:#fef3c7; color:#b45309;',
                            'diproses'   => 'background:#dbeafe; color:#1d4ed8;',
                            'selesai'    => 'background:#d1fae5; color:#047857;',
                            'ditolak'    => 'background:#fee2e2; color:#b91c1c;',
                        ][$r->status_refund] ?? 'background:#f3f4f6; color:#374151;';

                        $isUrgent = $r->status_refund === 'pending';
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                        <td class="px-5 py-4 text-sm text-gray-500">#{{ $r->id }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-gray-800 whitespace-nowrap">
                            #{{ $r->pesanan->kode_pesanan ?? '-' }}
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $r->pesanan->user->username ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $r->pesanan->user->email ?? '' }}</p>
                        </td>

                        {{-- Kolom Produk --}}
                        <td class="px-5 py-4">
                            @if ($r->pesanan && $r->pesanan->detail->count() > 0)
                                <div class="space-y-1 max-w-[240px]">
                                    @foreach ($r->pesanan->detail->take(2) as $d)
                                        <div class="flex items-center gap-1.5 text-sm">
                                            <span class="text-xs">{{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}</span>
                                            <span class="text-gray-700 truncate">{{ $d->nama_item }}</span>
                                            <span class="text-gray-400 text-xs flex-shrink-0">×{{ $d->jumlah }}</span>
                                        </div>
                                    @endforeach
                                    @if ($r->pesanan->detail->count() > 2)
                                        <p class="text-xs text-gray-400 pl-4">+{{ $r->pesanan->detail->count() - 2 }} produk lainnya</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-sm font-bold text-emerald-700 whitespace-nowrap">
                            Rp {{ number_format($r->nominal_refund, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            <p class="line-clamp-2 max-w-xs">{{ $r->alasan_batal }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold whitespace-nowrap" style="{{ $statusStyle }}">
                                {{ ucfirst($r->status_refund) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($isUrgent)
                                {{-- Tombol Proses — PROMINENT (urgent action) --}}
                                <a href="{{ route('admin.refund.show', $r->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold shadow-sm shadow-amber-200 transition whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Proses
                                </a>
                            @else
                                {{-- Tombol Detail — netral --}}
                                <a href="{{ route('admin.refund.show', $r->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                            @if (request('q'))
                                Tidak ada refund dengan kata kunci "<span class="font-semibold text-gray-600">{{ request('q') }}</span>"
                            @else
                                Belum ada pengajuan refund
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $refund->links() }}</div>

@endsection