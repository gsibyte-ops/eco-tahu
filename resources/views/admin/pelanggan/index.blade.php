@extends('layouts.admin')
@section('title', 'Kelola Pelanggan')
@section('page-title', 'Kelola Pelanggan')

@section('content')

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    {{-- Total Pelanggan --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--brand));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--brand));">Total Pelanggan</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats['total_pelanggan'] }}</p>
    </div>

    {{-- Aktif 30 Hari --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--info));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--info));">Aktif (30 hari)</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats['pelanggan_aktif'] }}</p>
    </div>

    {{-- Total Transaksi --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--accent));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--accent));">Total Transaksi</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats['total_transaksi'] }}</p>
    </div>

    {{-- Rata-rata Belanja --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--success));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--success));">Rata-rata Belanja</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($stats['rata_rata_belanja'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Search + Export --}}
<div class="glass-card p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-semibold txt-secondary uppercase tracking-wider mb-2">Cari Pelanggan</label>
            <div class="relative">
                <svg class="w-4 h-4 txt-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, email, atau no. telepon..."
                       class="glass-input w-full pl-10 pr-4 py-2.5 text-sm"
                       style="color: rgb(var(--text-primary));">
            </div>
        </div>

        <button type="submit" class="btn-primary whitespace-nowrap">
            Cari
        </button>

        @if (request()->has('q'))
            <a href="{{ route('admin.pelanggan.index') }}"
               class="glass-btn whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </a>
        @endif

        {{-- Tombol Export --}}
        <a href="{{ route('admin.pelanggan.export', request()->only('q')) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap"
           style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong)); border: 1px solid rgb(var(--brand) / 0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </form>
</div>

{{-- Tabel --}}
<div class="glass-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
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
                    <tr class="transition" style="border-bottom: 1px solid rgb(var(--border-soft));">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold"
                                     style="background: rgb(var(--brand-soft)); color: rgb(var(--brand));">
                                    {{ strtoupper(substr($p->username, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold txt-primary">{{ $p->username }}</p>
                                    <p class="text-xs txt-secondary">{{ $p->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm txt-secondary">{{ $p->no_telepon ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold"
                                  style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                                {{ $p->pesanan_count }} pesanan
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold" style="color: rgb(var(--brand));">
                            Rp {{ number_format($p->total_belanja ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm txt-secondary">{{ $p->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.pelanggan.show', $p->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                               style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center txt-muted">
                        @if (request('q'))
                            Tidak ada pelanggan dengan kata kunci "<span class="font-semibold txt-secondary">{{ request('q') }}</span>"
                        @else
                            Belum ada pelanggan
                        @endif
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $pelanggan->links() }}</div>

@endsection