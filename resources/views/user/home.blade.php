@extends('layouts.user')

@section('title', 'Beranda')

@section('content')

@php
    // Helper format angka
    $formatAngka = function ($n) {
        if ($n >= 1000000) return round($n / 1000000, 1) . 'jt+';
        if ($n >= 1000)    return round($n / 1000, 1) . 'k+';
        return (string) $n;
    };

    // Warna avatar testimoni (rotasi)
    $warnaAvatar = [
        'linear-gradient(135deg, #10b981, #047857)',
        'linear-gradient(135deg, #f59e0b, #d97706)',
        'linear-gradient(135deg, #8b5cf6, #6d28d9)',
        'linear-gradient(135deg, #3b82f6, #1d4ed8)',
    ];

    $useRealTestimoni = isset($testimoniReal) && $testimoniReal->count() > 0;
@endphp

{{-- ============================================================ --}}
{{-- HERO --}}
{{-- ============================================================ --}}
<section class="relative overflow-hidden">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12 py-12 lg:py-20">
        <div class="grid md:grid-cols-2 gap-10 lg:gap-16 items-center">

            {{-- TEXT --}}
            <div class="order-2 md:order-1">
                <span class="inline-flex items-center gap-2 pill mb-6">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: rgb(var(--brand));"></span>
                    100% Organik & Ramah Lingkungan
                </span>

                <h1 class="text-4xl lg:text-6xl font-extrabold leading-[1.05] tracking-tight mb-5 text-balance"
                    style="color: rgb(var(--text-primary));">
                    Tahu Sehat,
                    <span class="text-gradient-green">Lingkungan Kuat</span>
                </h1>

                <p class="text-base lg:text-lg leading-relaxed mb-8 max-w-lg text-pretty"
                   style="color: rgb(var(--text-secondary));">
                    Nikmati kelezatan tahu premium hasil pengolahan modern yang minim limbah. Sehat untuk keluarga, aman bagi bumi.
                </p>

                <div class="flex flex-wrap gap-3 mb-12">
                    <a href="{{ route('user.produk.index') }}" class="btn-primary">
                        Mulai Belanja
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                    <a href="#kategori" class="btn-ghost">
                        Pelajari Dampak Lingkungan
                    </a>
                </div>

                {{-- STATS REAL --}}
                <div class="grid grid-cols-3 gap-6 max-w-lg">
                    <div>
                        <p class="text-3xl font-extrabold tnum leading-none tracking-tight text-gradient-green">
                            {{ $formatAngka($stats['total_pesanan_selesai']) }}
                        </p>
                        <p class="text-xs mt-2" style="color: rgb(var(--text-muted));">Pesanan Sukses</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold tnum leading-none tracking-tight text-gradient-green">
                            @if ($stats['total_review'] > 0)
                                {{ number_format($stats['rating_rata'], 1) }}/5
                            @else
                                —
                            @endif
                        </p>
                        <p class="text-xs mt-2" style="color: rgb(var(--text-muted));">
                            @if ($stats['total_review'] > 0)
                                Dari {{ $stats['total_review'] }} ulasan
                            @else
                                Belum ada ulasan
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold tnum leading-none tracking-tight text-gradient-green">
                            {{ $stats['total_produk'] }}
                        </p>
                        <p class="text-xs mt-2" style="color: rgb(var(--text-muted));">Produk Tersedia</p>
                    </div>
                </div>
            </div>

            {{-- 3D MODEL --}}
            <div class="order-1 md:order-2 relative">
                <div class="absolute inset-0 pointer-events-none"
                     style="background: radial-gradient(ellipse at center, rgb(var(--brand) / 0.35) 0%, rgb(var(--accent) / 0.15) 45%, transparent 70%); filter: blur(60px);"></div>

                <div class="relative tofu-float">
                    <model-viewer
                        src="{{ asset('models/tofu.glb') }}"
                        alt="Tahu EcoTahu 3D"
                        auto-rotate
                        auto-rotate-delay="0"
                        rotation-per-second="20deg"
                        camera-controls
                        disable-zoom
                        shadow-intensity="1.2"
                        exposure="1.1"
                        environment-image="neutral"
                        interaction-prompt="none"
                        loading="eager"
                        reveal="auto"
                        class="w-full"
                        style="height: 480px; outline: none; --poster-color: transparent;">
                    </model-viewer>

                    <div id="modelLoading" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-full border-4 animate-spin"
                                 style="border-color: rgb(var(--brand) / 0.2); border-top-color: rgb(var(--brand));"></div>
                            <p class="text-xs font-medium" style="color: rgb(var(--text-muted));">Memuat model 3D...</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 relative">
                    <p class="text-2xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Tahu Premium</p>
                    <p class="text-sm mt-1" style="color: rgb(var(--text-secondary));">Sutra & Organik · Fresh Setiap Hari</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- TRUST BAR --}}
{{-- ============================================================ --}}
<section class="py-10" style="border-top: 1px solid rgb(var(--border-soft)); border-bottom: 1px solid rgb(var(--border-soft)); background: rgb(var(--bg-secondary) / 0.4);">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $trust = [
                    ['icon' => 'shield', 'title' => '100% Halal', 'desc' => 'Bersertifikat MUI', 'color' => 'brand'],
                    ['icon' => 'lightning', 'title' => 'Pengiriman Cepat', 'desc' => 'Same-day Jember', 'color' => 'brand'],
                    ['icon' => 'globe', 'title' => 'Zero Waste', 'desc' => 'Limbah jadi produk', 'color' => 'accent'],
                    ['icon' => 'money', 'title' => 'Harga Terjangkau', 'desc' => 'Langsung dari produsen', 'color' => 'brand'],
                ];
            @endphp

            @foreach ($trust as $t)
                @php
                    $isAccent = $t['color'] === 'accent';
                    $bg = $isAccent ? 'rgb(var(--accent-soft))' : 'rgb(var(--brand-soft))';
                    $fg = $isAccent ? 'rgb(var(--accent))' : 'rgb(var(--brand-hover))';
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: {{ $bg }}; color: {{ $fg }};">
                        @if ($t['icon'] === 'shield')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @elseif ($t['icon'] === 'lightning')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        @elseif ($t['icon'] === 'globe')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-tight" style="color: rgb(var(--text-primary));">{{ $t['title'] }}</p>
                        <p class="text-xs mt-0.5" style="color: rgb(var(--text-muted));">{{ $t['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- KATEGORI — redesigned --}}
{{-- ============================================================ --}}
<section id="kategori" class="py-20">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
            <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-3 text-balance" style="color: rgb(var(--text-primary));">
                Kategori Produk
            </h2>
            <p class="max-w-xl mx-auto text-pretty" style="color: rgb(var(--text-secondary));">
                Pilih berbagai varian tahu segar & produk limbah bernilai tinggi sesuai kebutuhan.
            </p>
        </div>

        @php
            $emojiProduk = ['🥛', '🍚', '🍳', '🥟', '🧈', '🍲'];
            $emojiLimbah = ['🌾', '♻️', '🌱', '🍃'];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @forelse ($kategori as $i => $k)
                @php
                    $isLimbah = $k->tipe === 'limbah';
                    $emoji = $isLimbah ? $emojiLimbah[$i % count($emojiLimbah)] : $emojiProduk[$i % count($emojiProduk)];
                    $count = $isLimbah ? $k->limbah_count : $k->produk_tahu_count;
                    $route = $isLimbah
                        ? route('user.limbah.index', ['kategori' => $k->id])
                        : route('user.produk.index', ['kategori' => $k->id]);
                @endphp
                <a href="{{ $route }}"
                   class="category-card group relative flex flex-col items-center justify-center p-8 rounded-2xl transition overflow-hidden"
                   style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft));">
                    {{-- Gradient bg overlay --}}
                    <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 transition duration-500"
                         style="background: radial-gradient(circle at 50% 0%, {{ $isLimbah ? 'rgb(var(--accent) / 0.20)' : 'rgb(var(--brand) / 0.20)' }} 0%, transparent 70%);"></div>

                    {{-- Badge tipe --}}
                    <span class="absolute top-3 right-3 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full z-10
                                 {{ $isLimbah ? 'text-amber-700 bg-amber-100' : 'text-emerald-700 bg-emerald-100' }}">
                        {{ $isLimbah ? 'Limbah' : 'Tahu' }}
                    </span>

                    {{-- Icon --}}
                    <div class="relative z-10 w-20 h-20 mb-4 rounded-2xl flex items-center justify-center text-5xl transition duration-500 group-hover:scale-110 group-hover:-rotate-6"
                         style="background: {{ $isLimbah ? 'var(--gradient-accent)' : 'var(--gradient-brand)' }}; box-shadow: 0 8px 24px {{ $isLimbah ? 'rgb(var(--accent) / 0.4)' : 'rgb(var(--brand) / 0.4)' }};">
                        {{ $emoji }}
                    </div>

                    <p class="font-bold text-base tracking-tight relative text-center z-10 mb-1" style="color: rgb(var(--text-primary));">
                        {{ $k->nama_kategori }}
                    </p>
                    <p class="text-xs relative z-10" style="color: rgb(var(--text-muted));">
                        {{ $count }} produk
                    </p>

                    <div class="mt-3 flex items-center gap-1 text-xs font-semibold opacity-0 group-hover:opacity-100 transition duration-300 relative z-10"
                         style="color: {{ $isLimbah ? 'rgb(var(--accent))' : 'rgb(var(--brand))' }};">
                        Lihat
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                </a>
            @empty
                <p class="col-span-4 text-center" style="color: rgb(var(--text-muted));">Belum ada kategori</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- PRODUK PILIHAN --}}
{{-- ============================================================ --}}
<section class="py-20">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="flex items-end justify-between mb-12 gap-4">
            <div>
                <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-3" style="color: rgb(var(--text-primary));">Produk Pilihan</h2>
                <p style="color: rgb(var(--text-secondary));">Produk terlaris yang paling banyak dicintai pelanggan kami.</p>
            </div>
            <a href="{{ route('user.produk.index') }}"
               class="hidden md:inline-flex items-center gap-1.5 text-sm font-semibold shrink-0 transition arrow-link"
               style="color: rgb(var(--brand));">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse ($produkPilihan as $i => $p)
                @php
                    $avgRating = $p->averageRating();
                    $reviewCount = $p->reviewCount();
                @endphp
                <div class="product-card group flex flex-col rounded-2xl overflow-hidden transition"
                     style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                    <a href="{{ route('user.produk.show', $p->slug) }}" class="block relative overflow-hidden">
                        <div class="aspect-square flex items-center justify-center relative" style="background: rgb(var(--bg-secondary));">
                            @if ($p->gambar)
                                <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}"
                                     class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            @else
                                <div class="text-6xl">🥛</div>
                            @endif

                            @if ($i === 0)
                                <span class="absolute top-3 left-3 text-[10px] font-extrabold tracking-wider uppercase text-white px-2.5 py-1 rounded-full"
                                      style="background: var(--gradient-accent); box-shadow: 0 4px 12px rgb(var(--accent) / 0.4);">
                                    🔥 Terlaris
                                </span>
                            @endif
                        </div>
                    </a>

                    <div class="p-4 flex flex-col flex-1">
                        <span class="pill text-[10px] self-start mb-2">
                            {{ $p->kategori->nama_kategori ?? '-' }}
                        </span>

                        <h3 class="font-bold text-sm leading-snug line-clamp-2 min-h-[40px] mb-2 tracking-tight" style="color: rgb(var(--text-primary));">
                            {{ $p->nama_produk }}
                        </h3>

                        <div class="flex items-center gap-1 text-xs mb-3">
                            @for ($j = 1; $j <= 5; $j++)
                                <svg class="w-3.5 h-3.5 {{ $j <= round($avgRating) ? 'text-amber-400 fill-current' : 'fill-current' }}"
                                     style="{{ $j > round($avgRating) ? 'color: rgb(var(--text-faint))' : '' }}"
                                     viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endfor
                            <span class="ml-1" style="color: rgb(var(--text-muted));">
                                @if ($reviewCount > 0)
                                    {{ number_format($avgRating, 1) }} ({{ $reviewCount }})
                                @else
                                    Baru
                                @endif
                            </span>
                        </div>

                        <div class="mb-4 mt-auto">
                            <span class="price text-lg">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                        </div>

                        <button type="button"
                                onclick="addToCart('produk', {{ $p->id }})"
                                class="btn-primary w-full !text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
            @empty
                <p class="col-span-4 text-center py-8" style="color: rgb(var(--text-muted));">Belum ada produk</p>
            @endforelse
        </div>

        <div class="md:hidden mt-6 text-center">
            <a href="{{ route('user.produk.index') }}" class="text-sm font-semibold arrow-link" style="color: rgb(var(--brand));">
                Lihat Semua Produk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- BANNER ZERO WASTE (ganti dari promo 20%) --}}
{{-- ============================================================ --}}
<section class="py-20">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="relative rounded-3xl overflow-hidden p-8 lg:p-14 grid md:grid-cols-2 gap-10 items-center"
             style="background: var(--gradient-brand); box-shadow: 0 24px 56px -16px rgb(var(--brand) / 0.4);">

            <div class="absolute top-0 right-0 w-72 h-72 rounded-full -translate-y-36 translate-x-36 blur-2xl pointer-events-none"
                 style="background: rgb(255 255 255 / 0.15);"></div>
            <div class="absolute bottom-0 left-0 w-56 h-56 rounded-full translate-y-28 -translate-x-28 blur-2xl pointer-events-none"
                 style="background: rgb(255 255 255 / 0.15);"></div>

            <div class="relative z-10">
                <span class="inline-block text-xs font-bold uppercase tracking-widest text-white/80 mb-4">
                    ♻️ Zero Waste Movement
                </span>
                <h3 class="text-3xl lg:text-4xl font-extrabold text-white mb-4 leading-tight tracking-tight">
                    Setiap Tahu yang Kami Produksi<br>Juga Bernilai untuk Bumi
                </h3>
                <p class="text-white/90 mb-7 text-pretty max-w-md">
                    Ampas tahu yang biasanya terbuang, kami olah menjadi produk bernilai — pakan ternak, pupuk organik, dan olahan pangan. Dukung sirkular ekonomi bersama kami.
                </p>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user.limbah.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white font-semibold rounded-2xl hover:bg-amber-50 transition whitespace-nowrap shadow-lg"
                       style="color: rgb(var(--brand-hover));">
                        Lihat Produk Limbah
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('user.edukasi.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-semibold rounded-2xl transition whitespace-nowrap border border-white/30">
                        Baca Edukasi
                    </a>
                </div>
            </div>

            <div class="hidden md:flex justify-end relative z-10">
                <div class="w-56 h-56 rounded-full flex items-center justify-center border"
                     style="background: rgb(255 255 255 / 0.15); backdrop-filter: blur(20px); border-color: rgb(255 255 255 / 0.3);">
                    <div class="text-center text-white">
                        <p class="text-7xl leading-none mb-2">♻️</p>
                        <p class="text-xs font-semibold tracking-widest">SDG 12</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- LIMBAH PILIHAN --}}
{{-- ============================================================ --}}
@if ($limbahPilihan->count() > 0)
<section class="py-20">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
            <span class="pill-accent mb-4">♻️ Zero Waste Movement</span>
            <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-3 mt-4 text-balance" style="color: rgb(var(--text-primary));">
                Ampas Tahu Bermanfaat
            </h2>
            <p class="max-w-xl mx-auto text-pretty" style="color: rgb(var(--text-secondary));">
                Kami mengubah limbah ampas tahu menjadi produk bernilai untuk pakan ternak, pupuk, dan olahan pangan.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($limbahPilihan as $i => $l)
                @php
                    $avgRating = $l->averageRating();
                    $reviewCount = $l->reviewCount();
                @endphp
                <div class="limbah-card group flex flex-col rounded-2xl overflow-hidden transition"
                     style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                    <a href="{{ route('user.limbah.show', $l->slug) }}" class="block relative overflow-hidden">
                        <div class="aspect-square flex items-center justify-center relative" style="background: rgb(var(--bg-secondary));">
                            @if ($l->gambar)
                                <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_limbah }}"
                                     class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            @else
                                <div class="text-6xl">🌾</div>
                            @endif

                            @if ($i === 0)
                                <span class="absolute top-3 left-3 text-[10px] font-extrabold tracking-wider uppercase text-white px-2.5 py-1 rounded-full"
                                      style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.4);">
                                    🌱 Populer
                                </span>
                            @endif
                        </div>
                    </a>

                    <div class="p-4 flex flex-col flex-1">
                        <span class="pill-accent text-[10px] self-start mb-2">
                            {{ $l->kategori->nama_kategori ?? 'Limbah' }}
                        </span>

                        <h3 class="font-bold text-sm leading-snug line-clamp-2 min-h-[40px] mb-2 tracking-tight" style="color: rgb(var(--text-primary));">
                            {{ $l->nama_limbah }}
                        </h3>

                        <div class="flex items-center gap-1 text-xs mb-3">
                            @for ($j = 1; $j <= 5; $j++)
                                <svg class="w-3.5 h-3.5 {{ $j <= round($avgRating) ? 'text-amber-400 fill-current' : 'fill-current' }}"
                                     style="{{ $j > round($avgRating) ? 'color: rgb(var(--text-faint))' : '' }}"
                                     viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endfor
                            <span class="ml-1" style="color: rgb(var(--text-muted));">
                                @if ($reviewCount > 0)
                                    {{ number_format($avgRating, 1) }} ({{ $reviewCount }})
                                @else
                                    Baru
                                @endif
                            </span>
                        </div>

                        <div class="mb-4 mt-auto">
                            <span class="price text-lg" style="color: rgb(var(--accent-hover));">
                                Rp {{ number_format($l->harga, 0, ',', '.') }}
                            </span>
                            <span class="text-xs ml-1" style="color: rgb(var(--text-muted));">/ {{ $l->satuan }}</span>
                        </div>

                        <button type="button"
                                onclick="addToCart('limbah', {{ $l->id }})"
                                class="glass-btn-amber w-full !text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- TESTIMONI — dari review real --}}
{{-- ============================================================ --}}
<section class="py-20">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
            <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-3 text-balance" style="color: rgb(var(--text-primary));">
                Apa Kata Mereka?
            </h2>
            <p class="max-w-lg mx-auto text-pretty" style="color: rgb(var(--text-secondary));">
                @if ($useRealTestimoni)
                    Ulasan asli dari pelanggan yang sudah merasakan tahu sehat EcoTahu.
                @else
                    Ribuan pelanggan sudah merasakan manfaat tahu sehat EcoTahu.
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @if ($useRealTestimoni)
                @foreach ($testimoniReal as $i => $r)
                    @php $warna = $warnaAvatar[$i % count($warnaAvatar)]; @endphp
                    <div class="testimoni-card p-6 rounded-2xl transition relative overflow-hidden"
                         style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                        {{-- Quote decoration --}}
                        <div class="absolute top-3 right-4 text-6xl leading-none font-serif opacity-[0.06]" style="color: rgb(var(--brand));">"</div>

                        <div class="flex items-center gap-3 mb-3 relative z-10">
                            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                                 style="background: {{ $warna }}; box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);">
                                {{ strtoupper(substr($r->user->username ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-sm tracking-tight truncate" style="color: rgb(var(--text-primary));">
                                    {{ $r->user->username ?? 'User' }}
                                </p>
                                <p class="text-xs" style="color: rgb(var(--text-muted));">
                                    {{ $r->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-amber-400 text-sm mb-3 relative z-10">
                            {{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}
                        </div>
                        <p class="text-sm italic leading-relaxed mb-3 line-clamp-4 relative z-10" style="color: rgb(var(--text-secondary));">
                            "{{ $r->komentar }}"
                        </p>
                        <div class="flex items-center gap-1.5 text-xs font-semibold relative z-10" style="color: rgb(var(--brand));">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pembeli Terverifikasi
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Placeholder kalau belum ada review real --}}
                @php
                    $testimoniDefault = [
                        ['nama' => 'Pelanggan EcoTahu', 'emoji' => '👤', 'teks' => 'Jadilah yang pertama memberi ulasan setelah pesanan pertama Anda selesai!'],
                        ['nama' => 'Pelanggan EcoTahu', 'emoji' => '👤', 'teks' => 'Bagikan pengalaman Anda tentang tahu premium & ampas tahu bermanfaat dari kami.'],
                        ['nama' => 'Pelanggan EcoTahu', 'emoji' => '👤', 'teks' => 'Ulasan Anda membantu pelanggan lain memilih produk terbaik.'],
                    ];
                @endphp
                @foreach ($testimoniDefault as $i => $t)
                    @php $warna = $warnaAvatar[$i % count($warnaAvatar)]; @endphp
                    <div class="p-6 rounded-2xl transition relative overflow-hidden"
                         style="background: rgb(var(--surface)); border: 1px dashed rgb(var(--border)); opacity: 0.75;">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white text-xl flex-shrink-0"
                                 style="background: {{ $warna }};">
                                {{ $t['emoji'] }}
                            </div>
                            <div>
                                <p class="font-bold text-sm tracking-tight" style="color: rgb(var(--text-primary));">{{ $t['nama'] }}</p>
                                <p class="text-xs" style="color: rgb(var(--text-muted));">Menunggu ulasan pertama</p>
                            </div>
                        </div>
                        <div class="text-gray-300 text-sm mb-3">☆☆☆☆☆</div>
                        <p class="text-sm italic leading-relaxed" style="color: rgb(var(--text-muted));">
                            "{{ $t['teks'] }}"
                        </p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- EDUKASI --}}
{{-- ============================================================ --}}
<section class="py-20">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="flex items-end justify-between mb-12 gap-4">
            <div>
                <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-3" style="color: rgb(var(--text-primary));">Edukasi Lingkungan</h2>
                <p style="color: rgb(var(--text-secondary));">Belajar bareng tentang pelestarian lingkungan & gaya hidup berkelanjutan.</p>
            </div>
            <a href="{{ route('user.edukasi.index') }}"
               class="hidden md:inline-flex items-center gap-1.5 text-sm font-semibold shrink-0 transition arrow-link"
               style="color: rgb(var(--brand));">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse ($edukasi as $e)
                <a href="{{ route('user.edukasi.show', $e->slug) }}"
                   class="edukasi-card group block rounded-2xl overflow-hidden transition"
                   style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                    <div class="aspect-video flex items-center justify-center overflow-hidden" style="background: rgb(var(--bg-secondary));">
                        @if ($e->thumbnail)
                            <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}"
                                 class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        @else
                            <div class="text-6xl">📖</div>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="pill text-[10px]">📚 Artikel</span>
                            <span class="text-xs tnum" style="color: rgb(var(--text-muted));">
                                {{ ($e->published_at ?? $e->tanggal_mengunggah)->format('d M Y') }}
                            </span>
                        </div>
                        <h3 class="font-bold text-base mb-3 line-clamp-2 leading-snug tracking-tight" style="color: rgb(var(--text-primary));">
                            {{ $e->judul }}
                        </h3>
                        <p class="text-sm line-clamp-2 text-pretty leading-relaxed" style="color: rgb(var(--text-secondary));">
                            {{ Str::limit(strip_tags($e->konten), 100) }}
                        </p>
                    </div>
                </a>
            @empty
                <p class="col-span-3 text-center py-8" style="color: rgb(var(--text-muted));">Belum ada artikel</p>
            @endforelse
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    /* ============================================================
       3D MODEL ANIMATION
       ============================================================ */
    .tofu-float {
        animation: tofu-float 6s ease-in-out infinite;
        will-change: transform;
    }
    @keyframes tofu-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-16px); }
    }

    /* ============================================================
       KATEGORI CARD
       ============================================================ */
    .category-card {
        box-shadow: 0 1px 3px rgb(15 23 42 / 0.05);
    }
    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px -12px rgb(var(--brand) / 0.25);
        border-color: rgb(var(--brand) / 0.5) !important;
    }

    /* ============================================================
       PRODUCT CARD — fix hover (pakai !important biar nggak ke-override inline style)
       ============================================================ */
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 48px -12px rgb(var(--brand) / 0.28) !important;
        border-color: rgb(var(--brand) / 0.6) !important;
    }

    /* LIMBAH CARD — hover kuning/amber */
    .limbah-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 48px -12px rgb(var(--accent) / 0.28) !important;
        border-color: rgb(var(--accent) / 0.6) !important;
    }

    /* EDUKASI CARD — hover emerald (sama kaya produk) */
    .edukasi-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 48px -12px rgb(var(--brand) / 0.28) !important;
        border-color: rgb(var(--brand) / 0.6) !important;
    }

    /* TESTIMONI CARD */
    .testimoni-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgb(var(--brand) / 0.2) !important;
        border-color: rgb(var(--brand) / 0.4) !important;
    }

    /* Arrow link */
    .arrow-link { gap: 0.375rem; transition: gap 0.25s ease, color 0.25s ease; }
    .arrow-link:hover { gap: 0.625rem; }

    /* Smooth scroll */
    html { scroll-behavior: smooth; }

    /* Mobile */
    @media (max-width: 640px) {
        model-viewer { height: 350px !important; }
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .tofu-float { animation: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    function addToCart(type, id) {
        fetch('{{ route('user.cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ type: type, id: id, qty: 1 })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Ditambahkan ke keranjang!');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Gagal menambahkan', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Terjadi kesalahan', 'error');
        });
    }

    function showToast(message, type = 'success') {
        const isSuccess = type === 'success';
        const toast = document.createElement('div');
        toast.className = 'fixed top-24 right-4 z-[999] text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold flex items-center gap-2';
        toast.style.background = isSuccess
            ? 'linear-gradient(135deg, #10b981 0%, #047857 100%)'
            : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        toast.style.transform = 'translateX(400px)';
        toast.style.transition = 'transform 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
        toast.style.boxShadow = isSuccess
            ? '0 12px 32px rgb(16 185 129 / 0.4)'
            : '0 12px 32px rgb(239 68 68 / 0.4)';
        toast.innerHTML = (isSuccess
            ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
            : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>'
        ) + '<span>' + message + '</span>';
        document.body.appendChild(toast);

        setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 50);
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 350);
        }, 2500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const mv = document.querySelector('model-viewer');
        const loading = document.getElementById('modelLoading');
        if (mv && loading) {
            mv.addEventListener('load', () => {
                loading.style.opacity = '0';
                setTimeout(() => loading.remove(), 500);
            });
            setTimeout(() => {
                if (loading && document.body.contains(loading)) {
                    loading.style.opacity = '0';
                    setTimeout(() => loading.remove(), 500);
                }
            }, 5000);
        }
    });
</script>
@endpush