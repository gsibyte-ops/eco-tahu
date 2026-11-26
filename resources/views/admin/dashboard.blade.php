@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Salam --}}
<div class="mb-5 md:mb-6">
    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Selamat Datang, Admin</h2>
    <p class="text-xs md:text-sm mt-1" style="color: rgb(var(--text-secondary));">Pantau aktivitas bisnis EcoTahu hari ini.</p>
</div>

{{-- Statistik Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5 mb-5 md:mb-6">

    {{-- Card Pesanan --}}
    <a href="{{ route('admin.pesanan.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--info-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--info));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            @php $pGrowth = $stats['pesanan_growth']; @endphp
            @if ($pGrowth === null)
                <span class="text-[10px] md:text-xs font-bold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-muted));">-</span>
            @else
                <span class="text-[10px] md:text-xs font-semibold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" 
                      style="background: rgb(var(--{{ $pGrowth >= 0 ? 'success' : 'danger' }}-soft)); color: rgb(var(--{{ $pGrowth >= 0 ? 'success' : 'danger' }}));">
                    {{ $pGrowth > 100 ? '>100' : ($pGrowth > 0 ? '+' : '') . number_format($pGrowth, 1) }}%
                </span>
            @endif
        </div>
        <div>
            <p class="text-[11px] md:text-sm mb-1 truncate" style="color: rgb(var(--text-secondary));">Total Pesanan</p>
            <p id="stat-total-pesanan" class="text-lg md:text-2xl font-extrabold tabular-nums leading-none" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_pesanan']) }}</p>
        </div>
    </a>

    {{-- Card Pendapatan --}}
    <a href="{{ route('admin.laporan.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--brand-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--brand-strong));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            @php $rGrowth = $stats['pendapatan_growth']; @endphp
            @if ($rGrowth === null)
                <span class="text-[10px] md:text-xs font-bold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-muted));">-</span>
            @else
                <span class="text-[10px] md:text-xs font-semibold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" 
                      style="background: rgb(var(--{{ $rGrowth >= 0 ? 'success' : 'danger' }}-soft)); color: rgb(var(--{{ $rGrowth >= 0 ? 'success' : 'danger' }}));">
                    {{ $rGrowth > 100 ? '>100' : ($rGrowth > 0 ? '+' : '') . number_format($rGrowth, 1) }}%
                </span>
            @endif
        </div>
        <div>
            <p class="text-[11px] md:text-sm mb-1 truncate" style="color: rgb(var(--text-secondary));">Pendapatan</p>
            <p id="stat-total-pendapatan" class="text-base md:text-xl font-extrabold tabular-nums leading-none" style="color: rgb(var(--text-primary));">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
        </div>
    </a>

    {{-- Card Stok Produk (TANPA PERSENTASE) --}}
    <a href="{{ route('admin.produk-tahu.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--warning-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div>
            <p class="text-[11px] md:text-sm mb-1 truncate" style="color: rgb(var(--text-secondary));">Stok Produk</p>
            <p class="text-lg md:text-2xl font-extrabold tabular-nums leading-none" style="color: rgb(var(--text-primary));">
                <span id="stat-total-produk">{{ number_format($stats['total_produk']) }}</span>
                <span class="text-[10px] md:text-sm font-normal" style="color: rgb(var(--text-muted));">unit</span>
            </p>
            <div class="mt-2 pt-2 flex flex-col xl:flex-row xl:items-center gap-1 xl:gap-2 text-[10px] md:text-xs" style="border-top: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-secondary));">
                <span>Tahu: <b id="stat-total-tahu" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_tahu'] ?? 0) }}</b></span>
                <span class="hidden xl:block" style="color: rgb(var(--text-faint));">|</span>
                <span>Limbah: <b id="stat-total-limbah" style="color: rgb(var(--text-primary));">{{ number_format($stats['total_limbah'] ?? 0) }}</b></span>
            </div>
        </div>
    </a>

    {{-- Card Pelanggan --}}
    <a href="{{ route('admin.pelanggan.index') }}" class="glass-card p-3 md:p-5 hover:-translate-y-0.5 transition flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 md:mb-3 gap-2">
            <div class="w-9 h-9 md:w-11 md:h-11 rounded-lg md:rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--info-soft));">
                <svg class="w-4 h-4 md:w-5 md:h-5" style="color: rgb(var(--info));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            @php $cGrowth = $stats['pelanggan_growth']; @endphp
            @if ($cGrowth === null)
                <span class="text-[10px] md:text-xs font-bold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-muted));">-</span>
            @else
                <span class="text-[10px] md:text-xs font-semibold px-1.5 py-0.5 md:px-2 md:py-1 rounded-md md:rounded-lg" 
                      style="background: rgb(var(--{{ $cGrowth >= 0 ? 'success' : 'danger' }}-soft)); color: rgb(var(--{{ $cGrowth >= 0 ? 'success' : 'danger' }}));">
                    {{ $cGrowth > 100 ? '>100' : ($cGrowth > 0 ? '+' : '') . number_format($cGrowth, 1) }}%
                </span>
            @endif
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
            <span class="px-3 py-1.5 md:px-4 md:py-2 text-white text-xs md:text-sm font-semibold rounded-lg md:rounded-xl transition flex-shrink-0 text-center" style="background: rgb(var(--warning));">Lihat</span>
        </div>
    </a>
</div>

{{-- Ringkasan Konten & Ulasan --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-5 mb-5 md:mb-6">
    <a href="{{ route('admin.edukasi.index') }}" class="glass-card p-4 hover:-translate-y-0.5 transition flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] md:text-xs font-bold uppercase tracking-wider mb-1 inline-flex items-center gap-1.5" style="color: rgb(var(--brand));"><x-icon name="book-open" class="w-3.5 h-3.5" /> Edukasi</p>
            <p class="text-xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">
                <span id="stat-edukasi-publish">{{ number_format($stats['edukasi_publish'] ?? 0) }}</span>
                <span class="text-xs font-normal" style="color: rgb(var(--text-muted));">/ <span id="stat-total-edukasi">{{ number_format($stats['total_edukasi'] ?? 0) }}</span> tayang</span>
            </p>
            <p class="text-xs mt-1 tabular-nums flex items-center gap-1" style="color: rgb(var(--text-secondary));"><x-icon name="heart-solid" class="w-3.5 h-3.5 text-rose-500" /> <span id="stat-edukasi-likes">{{ number_format($stats['total_edukasi_likes'] ?? 0) }}</span> total suka</p>
        </div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand));"><x-icon name="book-open" class="w-5 h-5" /></div>
    </a>

    <a href="{{ route('admin.resep.index') }}" class="glass-card p-4 hover:-translate-y-0.5 transition flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] md:text-xs font-bold uppercase tracking-wider mb-1 inline-flex items-center gap-1.5" style="color: rgb(var(--info));"><x-icon name="utensils" class="w-3.5 h-3.5" /> Resep Tahu</p>
            <p class="text-xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">
                <span id="stat-resep-publish">{{ number_format($stats['resep_publish'] ?? 0) }}</span>
                <span class="text-xs font-normal" style="color: rgb(var(--text-muted));">/ <span id="stat-total-resep">{{ number_format($stats['total_resep'] ?? 0) }}</span> tayang</span>
            </p>
            <p class="text-xs mt-1 tabular-nums flex items-center gap-1" style="color: rgb(var(--text-secondary));"><x-icon name="heart-solid" class="w-3.5 h-3.5 text-rose-500" /> <span id="stat-resep-likes">{{ number_format($stats['total_resep_likes'] ?? 0) }}</span> total suka</p>
        </div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--info-soft)); color: rgb(var(--info));"><x-icon name="utensils" class="w-5 h-5" /></div>
    </a>

    <div class="glass-card p-4 flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] md:text-xs font-bold uppercase tracking-wider mb-1 inline-flex items-center gap-1.5" style="color: rgb(var(--warning));"><x-icon name="star-solid" class="w-3.5 h-3.5" /> Ulasan &amp; Media</p>
            <p class="text-xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">
                <span id="stat-total-ulasan">{{ number_format($stats['total_ulasan'] ?? 0) }}</span>
                <span class="text-xs font-semibold inline-flex items-center gap-1" style="color: rgb(var(--warning));">· <x-icon name="star-solid" class="w-3.5 h-3.5" /> <span id="stat-rating-rata">{{ number_format($stats['rating_rata'] ?? 0, 1) }}</span></span>
            </p>
            <p class="text-xs mt-1 tabular-nums flex items-center gap-1" style="color: rgb(var(--text-secondary));"><x-icon name="photo" class="w-3.5 h-3.5" /> <span id="stat-ulasan-bermedia">{{ number_format($stats['ulasan_bermedia'] ?? 0) }}</span> ulasan foto/video</p>
        </div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));"><x-icon name="chat-bubble" class="w-5 h-5" /></div>
    </div>
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
        <a href="{{ route('admin.pesanan.index') }}" class="text-xs md:text-sm font-semibold hover:opacity-80" style="color: rgb(var(--brand));">Lihat Semua</a>
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

{{-- Ulasan Terbaru (Foto/Video) & Pengajuan Refund Terbaru --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5 mt-5 md:mt-6">

    {{-- Ulasan Terbaru --}}
    <div class="glass-card p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-sm md:text-base flex items-center gap-2" style="color: rgb(var(--text-primary));"><x-icon name="star-solid" class="w-4 h-4 text-amber-400" /> Ulasan Terbaru</h3>
            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">
                {{ number_format($stats['ulasan_bermedia'] ?? 0) }} dengan foto/video
            </span>
        </div>

        <div class="space-y-3">
            @forelse ($ulasanTerbaru ?? [] as $rev)
                @php
                    $revItemName = $rev->reviewable?->nama_produk ?? $rev->reviewable?->nama_limbah ?? 'Produk';
                @endphp
                <div class="p-3.5 rounded-xl" style="background: rgb(var(--bg-secondary) / 0.55); border: 1px solid rgb(var(--border-soft));">
                    <div class="flex items-start justify-between gap-2 mb-1.5">
                        <div class="min-w-0">
                            <p class="text-xs md:text-sm font-bold truncate" style="color: rgb(var(--text-primary));">{{ $rev->user->username ?? 'Pelanggan' }}</p>
                            <p class="text-[11px] truncate" style="color: rgb(var(--text-muted));">{{ $revItemName }} · {{ $rev->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-0.5 whitespace-nowrap">
                            @for ($s = 1; $s <= 5; $s++)
                                <x-icon name="star-solid" class="w-3.5 h-3.5 {{ $s <= (int) $rev->rating ? 'text-amber-400' : '' }}" style="{{ $s > (int) $rev->rating ? 'color: rgb(var(--text-faint));' : '' }}" />
                            @endfor
                        </div>
                    </div>
                    @if ($rev->komentar)
                        <p class="text-xs leading-relaxed mb-2" style="color: rgb(var(--text-secondary));">{{ $rev->komentar }}</p>
                    @endif
                    @if ($rev->media_url)
                        @if ($rev->media_type === 'video')
                            <video src="{{ $rev->media_url }}" controls preload="metadata" class="w-full max-w-[260px] max-h-40 rounded-lg bg-black mt-1"></video>
                        @else
                            <img src="{{ $rev->media_url }}" alt="Foto ulasan"
                                 onclick="openDashLightbox('{{ $rev->media_url }}')"
                                 class="w-20 h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-90 transition mt-1"
                                 style="border: 1px solid rgb(var(--border));">
                        @endif
                    @endif
                </div>
            @empty
                <p class="text-sm text-center py-6" style="color: rgb(var(--text-muted));">Belum ada ulasan pelanggan</p>
            @endforelse
        </div>
    </div>

    {{-- Pengajuan Refund Terbaru --}}
    <div class="glass-card p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-sm md:text-base flex items-center gap-2" style="color: rgb(var(--text-primary));"><x-icon name="bank" class="w-4 h-4" style="color: rgb(var(--brand));" /> Pengajuan Refund &amp; Pembatalan</h3>
            <a href="{{ route('admin.refund.index') }}" class="text-xs md:text-sm font-semibold hover:opacity-80" style="color: rgb(var(--brand));">Kelola</a>
        </div>

        <div class="space-y-3">
            @forelse ($refundTerbaru ?? [] as $ref)
                @php
                    $refStyle = [
                        'pending'  => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning));',
                        'diproses' => 'background: rgb(var(--info-soft)); color: rgb(var(--info));',
                        'selesai'  => 'background: rgb(var(--success-soft)); color: rgb(var(--success));',
                        'ditolak'  => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger));',
                    ][$ref->status_refund] ?? 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));';
                @endphp
                <a href="{{ route('admin.refund.show', $ref->id) }}" class="block p-3.5 rounded-xl hover:opacity-90 transition"
                   style="background: rgb(var(--bg-secondary) / 0.55); border: 1px solid rgb(var(--border-soft));">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <div class="min-w-0">
                            <p class="text-xs md:text-sm font-bold truncate" style="color: rgb(var(--text-primary));">
                                #{{ $ref->pesanan->kode_pesanan ?? '-' }} · {{ $ref->pesanan->user->username ?? '-' }}
                            </p>
                            <p class="text-[11px] font-bold tabular-nums" style="color: rgb(var(--brand));">
                                Rp {{ number_format($ref->nominal_refund, 0, ',', '.') }}
                            </p>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold whitespace-nowrap" style="{{ $refStyle }}">
                            {{ ucfirst($ref->status_refund) }}
                        </span>
                    </div>
                    <p class="text-xs line-clamp-2 mb-1.5" style="color: rgb(var(--text-secondary));">{{ $ref->alasan_batal }}</p>
                    @if ($ref->nama_bank || $ref->nomor_rekening || $ref->atas_nama)
                        <div class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold flex items-center gap-1.5" style="background: rgb(var(--brand-soft) / 0.45); color: rgb(var(--brand-strong));">
                            <x-icon name="bank" class="w-3.5 h-3.5 shrink-0" />
                            <span>{{ $ref->nama_bank ?: '-' }} · {{ $ref->nomor_rekening ?: '-' }}@if($ref->atas_nama) (a.n. {{ $ref->atas_nama }})@endif</span>
                        </div>
                    @endif
                </a>
            @empty
                <p class="text-sm text-center py-6" style="color: rgb(var(--text-muted));">Belum ada pengajuan refund</p>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('modals')
{{-- LIGHTBOX FOTO ULASAN DASHBOARD (di luar <main> agar selalu menutup sidebar & header) --}}
<div id="dashLightbox" class="fixed inset-0 hidden" style="z-index: 99999; background: rgba(0,0,0,0.88); backdrop-filter: blur(8px);">
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 24px;" onclick="closeDashLightbox()">
        <img id="dashLightboxImg" src="" alt="Preview" onclick="event.stopPropagation()"
             style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
    </div>
    <button type="button" onclick="closeDashLightbox()"
            style="position: absolute; top: 20px; right: 20px; width: 48px; height: 48px; background: #ef4444; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100000; box-shadow: 0 4px 14px rgba(0,0,0,0.4);"
            title="Tutup">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function fetchRealtimeStats() {
        fetch('{{ route('admin.dashboard.stats') }}')
            .then(res => res.json())
            .then(data => {
                const revenue = parseFloat(data.total_pendapatan);
                
                document.getElementById('stat-total-pesanan').innerText = data.total_pesanan.toLocaleString('id-ID');
                document.getElementById('stat-total-pendapatan').innerText = 'Rp ' + Math.round(revenue).toLocaleString('id-ID');
                document.getElementById('stat-total-produk').innerText = data.total_produk.toLocaleString('id-ID');
                document.getElementById('stat-total-tahu').innerText = data.total_tahu.toLocaleString('id-ID');
                document.getElementById('stat-total-limbah').innerText = data.total_limbah.toLocaleString('id-ID');
                document.getElementById('stat-total-pelanggan').innerText = data.total_pelanggan.toLocaleString('id-ID');
                if (document.getElementById('stat-total-edukasi')) {
                    document.getElementById('stat-total-edukasi').innerText = (data.total_edukasi ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-edukasi-publish').innerText = (data.edukasi_publish ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-edukasi-likes').innerText = (data.total_edukasi_likes ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-total-resep').innerText = (data.total_resep ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-resep-publish').innerText = (data.resep_publish ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-resep-likes').innerText = (data.total_resep_likes ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-total-ulasan').innerText = (data.total_ulasan ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-ulasan-bermedia').innerText = (data.ulasan_bermedia ?? 0).toLocaleString('id-ID');
                    document.getElementById('stat-rating-rata').innerText = Number(data.rating_rata ?? 0).toFixed(1);
                }
                
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
    setInterval(fetchRealtimeStats, 5000);

    const chartDataWeek  = { labels: @json($chartLabels),        data: @json($chartData) };
    const chartDataMonth = { labels: @json($chartLabelsBulanan), data: @json($chartDataBulanan) };

    let salesChart;

    function buildChart(labels, data) {
        const ctx = document.getElementById('salesChart');
        if (salesChart) salesChart.destroy();

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const isDataEmpty = data.every(val => val === 0);

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
                        callbacks: { 
                            label: c => 'Rp ' + Math.round(c.parsed.y).toLocaleString('id-ID') 
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: isDataEmpty ? 100000 : undefined, 
                        grid: { color: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)' },
                        ticks: { 
                            callback: function(value) {
                                if (value === 0) return 'Rp 0';
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1).replace('.0', '') + 'Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(1).replace('.0', '') + 'k';
                                return 'Rp ' + Math.round(value);
                            },
                            font: { size: 10 }, 
                            color: isDark ? '#94a3b8' : '#9ca3af' 
                        }
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

    buildChart(chartDataWeek.labels, chartDataWeek.data);

    function openDashLightbox(url) {
        if (!url) return;
        document.getElementById('dashLightboxImg').src = url;
        document.getElementById('dashLightbox').classList.remove('hidden');
        document.body.classList.add('modal-active');
        document.body.style.overflow = 'hidden';
    }

    function closeDashLightbox() {
        document.getElementById('dashLightbox')?.classList.add('hidden');
        document.getElementById('dashLightboxImg').src = '';
        document.body.classList.remove('modal-active');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDashLightbox();
    });
</script>
@endpush