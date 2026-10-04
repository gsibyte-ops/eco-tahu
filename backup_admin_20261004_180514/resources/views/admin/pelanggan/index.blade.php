@extends('layouts.admin')
@section('title', 'Kelola Pelanggan')
@section('page-title', 'Kelola Pelanggan')

@section('content')

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    {{-- Total Pelanggan (Gradient Emerald) --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #34d399 0%, #10b981 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Total Pelanggan</p>
        <p class="text-2xl font-bold">{{ $stats['total_pelanggan'] }}</p>
    </div>

    {{-- Aktif 30 Hari (Gradient Sky) --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Aktif (30 hari)</p>
        <p class="text-2xl font-bold">{{ $stats['pelanggan_aktif'] }}</p>
    </div>

    {{-- Total Transaksi (Gradient Violet) --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Total Transaksi</p>
        <p class="text-2xl font-bold">{{ $stats['total_transaksi'] }}</p>
    </div>

    {{-- Rata-rata Belanja (Putih + Accent Emerald) --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-400 to-emerald-600"></div>
        <div class="pl-3">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center text-sm">💰</span>
                <p class="text-sm font-semibold text-gray-500">Rata-rata Belanja</p>
            </div>
            <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($stats['rata_rata_belanja'], 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Search + Export --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari Pelanggan</label>
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, email, atau no. telepon..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
            Cari
        </button>

        @if (request()->has('q'))
            <a href="{{ route('admin.pelanggan.index') }}"
               class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </a>
        @endif

        {{-- Tombol Export --}}
        <a href="{{ route('admin.pelanggan.export', request()->only('q')) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60 border-b border-gray-100">
                <th class="px-6 py-3">Pelanggan</th>
                <th class="px-6 py-3">Kontak</th>
                <th class="px-6 py-3">Total Pesanan</th>
                <th class="px-6 py-3">Total Belanja</th>
                <th class="px-6 py-3">Terdaftar</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelanggan as $p)
                <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                {{ strtoupper(substr($p->username, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $p->username }}</p>
                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $p->no_telepon ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:#dbeafe; color:#1d4ed8;">
                            {{ $p->pesanan_count }} pesanan
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-emerald-700">
                        Rp {{ number_format($p->total_belanja ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.pelanggan.show', $p->id) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">
                    @if (request('q'))
                        Tidak ada pelanggan dengan kata kunci "<span class="font-semibold text-gray-600">{{ request('q') }}</span>"
                    @else
                        Belum ada pelanggan
                    @endif
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pelanggan->links() }}</div>

@endsection