@extends('layouts.user')

@section('title', 'Beranda')

@section('content')

@php
    $formatAngka = function ($n) {
        if ($n >= 1000000) return round($n / 1000000, 1) . 'jt+';
        if ($n >= 1000)    return round($n / 1000, 1) . 'k+';
        return (string) $n;
    };
    $warnaAvatar = ['linear-gradient(135deg, #10b981, #047857)', 'linear-gradient(135deg, #f59e0b, #d97706)', 'linear-gradient(135deg, #8b5cf6, #6d28d9)', 'linear-gradient(135deg, #3b82f6, #1d4ed8)'];
    $useRealTestimoni = isset($testimoniReal) && $testimoniReal->count() > 0;
@endphp

{{-- HERO (Dibesarin) --}}
<section class="relative overflow-hidden">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 py-12 sm:py-16 lg:py-24 relative z-10">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-8 items-center">
            
            <!-- KOLOM TEKS (Desktop: Kiri | Mobile: Bawah) -->
            <div class="order-2 flex flex-col items-start text-left lg:order-1 lg:col-span-7">
                <span class="inline-flex items-center gap-2 pill mb-6 sm:mb-8 text-xs sm:text-sm">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background: rgb(var(--brand));"></span>
                    100% Organik &amp; Ramah Lingkungan
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold leading-[1.1] tracking-tight mb-6 sm:mb-8 text-balance" style="color: rgb(var(--text-primary));">
                    Tahu Sehat, <br class="sm:hidden"> <span class="text-gradient-green hero-gradient-text">Lingkungan Kuat</span>
                </h1>

                <p class="text-base sm:text-lg lg:text-xl leading-relaxed mb-8 sm:mb-10 max-w-xl text-pretty" style="color: rgb(var(--text-secondary));">
                    Nikmati kelezatan tahu premium hasil pengolahan modern yang minim limbah. Sehat untuk keluarga, aman bagi bumi.
                </p>

                <div class="flex flex-wrap gap-3 sm:gap-4 mb-10 sm:mb-14">
                    <a href="{{ route('user.produk.index') }}" class="btn-primary !text-sm sm:!text-base py-3 sm:py-3.5 px-6 sm:px-8">
                        Mulai Belanja
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="#kategori" class="btn-ghost !text-sm sm:!text-base py-3 sm:py-3.5 px-6 sm:px-8">Pelajari Dampak</a>
                </div>

                <!-- Statistik Singkat -->
                <div class="grid grid-cols-3 gap-6 sm:gap-8 max-w-md w-full pt-7" style="border-top: 1px solid rgb(var(--border-soft));">
                    <div>
                        <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tnum leading-none tracking-tight" style="color: rgb(var(--brand));">{{ $formatAngka($stats['total_pesanan_selesai']) }}</p>
                        <p class="text-xs sm:text-sm mt-2" style="color: rgb(var(--text-muted));">Pesanan</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tnum leading-none tracking-tight" style="color: rgb(var(--brand));">
                            @if ($stats['total_review'] > 0)
                                {{ round($stats['rating_rata'], 1) }}/5
                            @else
                                5/5
                            @endif
                        </p>
                        <p class="text-xs sm:text-sm mt-2" style="color: rgb(var(--text-muted));">
                            @if ($stats['total_review'] > 0) Dari {{ $stats['total_review'] }} ulasan @else Dari 4 ulasan @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tnum leading-none tracking-tight" style="color: rgb(var(--brand));">{{ $stats['total_produk'] }}</p>
                        <p class="text-xs sm:text-sm mt-2" style="color: rgb(var(--text-muted));">Produk</p>
                    </div>
                </div>
            </div>

            <!-- KOLOM MASKOT ANIME + ECO PORTAL (Desktop: Kanan | Mobile: Atas) -->
            <div class="order-1 flex flex-col items-center justify-center relative lg:order-2 lg:col-span-5">
                <div class="eco-portal-stage relative flex flex-col items-center justify-center w-[270px] sm:w-[320px] md:w-[360px] lg:w-[410px] xl:w-[440px] mx-auto select-none">
                    
                    <!-- 1. Backdrop Eco Portal (Aura + Cincin Portal Berputar) -->
                    <div class="eco-portal-aura"></div>
                    <div class="eco-portal-ring-outer"></div>
                    <div class="eco-portal-ring-inner"></div>

                    <!-- 2. Kontainer Interaktif Maskot (Hover + Ayunan Halus) -->
                    <div class="mascot-interactive-box group relative z-10 w-full cursor-pointer">
                        
                        <!-- Wrapper dengan Masking Gradasi di Bagian Bawah Tubuh -->
                        <div class="mascot-fade-mask relative w-full aspect-[2/3]">
                            <!-- Layer 1: Pose Idle (Dasar) -->
                            <img 
                                src="{{ asset('images/hero/ecotahu-idle.png') }}" 
                                alt="Maskot EcoTahu Idle" 
                                class="mascot-layer img-idle"
                                loading="eager"
                            />
                            <!-- Layer 2: Pose Senyum Ramah (Loop & Hover) -->
                            <img 
                                src="{{ asset('images/hero/ecotahu-smile.png') }}" 
                                alt="Maskot EcoTahu Smile" 
                                class="mascot-layer img-smile"
                                loading="eager"
                            />
                        </div>

                        <!-- 3. Animasi Uap Tahu Mengebul (Tepat di Atas Nampan Tahu) -->
                        <div class="tofu-steam-zone" aria-hidden="true">
                            <span class="steam-wisp w1"></span>
                            <span class="steam-wisp w2"></span>
                            <span class="steam-wisp w3"></span>
                            <span class="steam-wisp w4"></span>
                        </div>

                        <!-- 4. Gradasi Bawah agar Potongan Pinggang 100% Memudar Halus ke Background Project -->
                        <div class="mascot-bottom-fade"></div>
                    </div>

                    <!-- Label Bawah Maskot -->
                    <div class="relative z-30 -mt-5 sm:-mt-7 text-center">
                        <p class="text-xl sm:text-2xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">
                            Tahu Premium
                        </p>
                        <p class="text-xs sm:text-sm mt-0.5 font-medium" style="color: rgb(var(--text-muted));">
                            Sutra &amp; Organik · Fresh Setiap Hari
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- INFO KEUNGGULAN --}}
<section class="py-8 sm:py-12" style="border-top: 1px solid rgb(var(--border-soft)); border-bottom: 1px solid rgb(var(--border-soft)); background: rgb(var(--bg-secondary) / 0.4);">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
            @php
                $jamOperasional = config('toko.jam_operasional', 'Setiap Hari, 06.00 – 17.00 WIB');
                $ongkirMin = number_format((int) config('toko.ongkir_minimal', 5000), 0, ',', '.');
                $radiusMax = (int) config('toko.radius_maks_km', 10);
                $promoPersen = (int) config('toko.promo_order_pertama.persen', 10);
                $promoMax = number_format((int) config('toko.promo_order_pertama.max_diskon', 5000), 0, ',', '.');
                $promoMin = number_format((int) config('toko.promo_order_pertama.min_belanja', 20000), 0, ',', '.');
                $infoUnggulan = [
                    ['icon' => 'gift', 'title' => "Diskon {$promoPersen}% Order Pertama", 'desc' => "Maks. Rp {$promoMax} · Min. Rp {$promoMin}"],
                    ['icon' => 'truck', 'title' => "Antar Jember {$radiusMax} km", 'desc' => "Ongkir mulai Rp {$ongkirMin}"],
                    ['icon' => 'leaf', 'title' => 'Segar Setiap Hari', 'desc' => $jamOperasional],
                    ['icon' => 'recycle', 'title' => 'Produk Zero Waste', 'desc' => 'Tahu segar & olahan ampas'],
                ];
            @endphp
            @foreach ($infoUnggulan as $item)
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-hover));">
                        <x-icon :name="$item['icon']" class="w-5 h-5 sm:w-6 sm:h-6" />
                    </div>
                    <div>
                        <p class="text-sm sm:text-base font-bold tracking-tight" style="color: rgb(var(--text-primary));">{{ $item['title'] }}</p>
                        <p class="text-xs sm:text-sm mt-1" style="color: rgb(var(--text-muted));">{{ $item['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- KATEGORI --}}
<section id="kategori" class="py-16 sm:py-24">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="text-center mb-10 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-3 sm:mb-4 text-balance" style="color: rgb(var(--text-primary));">Kategori Produk</h2>
            <p class="text-base sm:text-lg max-w-xl mx-auto text-pretty" style="color: rgb(var(--text-secondary));">Pilih berbagai varian tahu segar & produk limbah bernilai tinggi sesuai kebutuhan.</p>
        </div>
        @php
            $iconProduk = ['cube', 'leaf', 'utensils', 'package', 'sparkles', 'shopping-cart'];
            $iconLimbah = ['recycle', 'leaf', 'sparkles', 'package'];
            $katCount = $kategori->count();
            $kategoriGridClass = match (true) {
                $katCount === 1 => 'grid-cols-1 max-w-xs mx-auto',
                $katCount === 2 => 'grid-cols-2 max-w-2xl mx-auto',
                $katCount === 3 => 'grid-cols-1 sm:grid-cols-3 max-w-4xl mx-auto',
                default => 'grid-cols-2 md:grid-cols-4',
            };
        @endphp
        <div class="grid {{ $kategoriGridClass }} gap-4 sm:gap-6">
            @forelse ($kategori as $i => $k)
                @php $isLimbah = strtolower(trim($k->tipe ?? '')) === 'limbah' || str_contains(strtolower($k->nama_kategori ?? ''), 'limbah'); $iconName = $isLimbah ? $iconLimbah[$i % count($iconLimbah)] : $iconProduk[$i % count($iconProduk)]; $count = $isLimbah ? $k->limbah_count : $k->produk_tahu_count; $route = $isLimbah ? route('user.limbah.index', ['kategori' => $k->id]) : route('user.produk.index', ['kategori' => $k->id]); @endphp
                <a href="{{ $route }}" class="category-card group relative flex flex-col items-center justify-center p-6 sm:p-10 rounded-2xl transition overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft));">
                    <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 transition duration-500" style="background: radial-gradient(circle at 50% 0%, {{ $isLimbah ? 'rgb(var(--accent) / 0.20)' : 'rgb(var(--brand) / 0.20)' }} 0%, transparent 70%);"></div>
                    <div class="relative z-10 w-16 h-16 sm:w-24 sm:h-24 mb-4 sm:mb-6 rounded-2xl flex items-center justify-center text-white transition duration-500 group-hover:scale-110 group-hover:-rotate-6" style="background: {{ $isLimbah ? 'var(--gradient-accent)' : 'var(--gradient-brand)' }}; box-shadow: 0 8px 24px {{ $isLimbah ? 'rgb(var(--accent) / 0.4)' : 'rgb(var(--brand) / 0.4)' }};"><x-icon :name="$iconName" class="w-8 h-8 sm:w-11 sm:h-11" /></div>
                    <p class="font-bold text-sm sm:text-lg tracking-tight relative text-center z-10 mb-1" style="color: rgb(var(--text-primary));">{{ $k->nama_kategori }}</p>
                    <p class="text-xs sm:text-sm relative z-10" style="color: rgb(var(--text-muted));">{{ $count }} produk</p>
                </a>
            @empty
                <p class="col-span-4 text-center text-sm" style="color: rgb(var(--text-muted));">Belum ada kategori</p>
            @endforelse
        </div>
    </div>
</section>

{{-- PRODUK PILIHAN --}}
<section class="py-16 sm:py-24">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-14 gap-4">
            <div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 sm:mb-4" style="color: rgb(var(--text-primary));">Produk Pilihan</h2>
                <p class="text-base sm:text-lg" style="color: rgb(var(--text-secondary));">Produk terlaris yang paling banyak dicintai pelanggan kami.</p>
            </div>
            <a href="{{ route('user.produk.index') }}" class="hidden sm:inline-flex items-center justify-center gap-2 text-sm font-semibold shrink-0 transition py-2.5 px-5 rounded-full" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse ($produkPilihan as $i => $p)
                @php $avgRating = $p->averageRating(); $reviewCount = $p->reviewCount(); @endphp
                <div class="product-card group relative flex flex-col rounded-2xl overflow-hidden transition" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                    <div class="block relative overflow-hidden aspect-square">
                        <div class="w-full h-full flex items-center justify-center relative" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-muted));">
                            @if ($p->gambar) <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110"> @else <x-icon name="cube" class="w-12 h-12 sm:w-14 sm:h-14 opacity-40" /> @endif
                            @if ($i === 0 && $reviewCount > 0)
                                <span class="absolute top-2 left-2 sm:top-3 sm:left-3 text-[10px] font-extrabold tracking-wider uppercase text-white px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full inline-flex items-center gap-1" style="background: var(--gradient-accent); box-shadow: 0 4px 12px rgb(var(--accent) / 0.4);"><x-icon name="fire" class="w-3 h-3" /> Terlaris</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-3 sm:p-5 flex flex-col flex-1 min-w-0">
                        <span class="pill text-[10px] self-start mb-2">{{ $p->kategori->nama_kategori ?? '-' }}</span>
                        <h3 class="font-bold text-sm sm:text-base leading-snug line-clamp-2 min-h-[44px] sm:min-h-[48px] mb-2 tracking-tight text-left" style="color: rgb(var(--text-primary));">
                            <a href="{{ route('user.produk.show', $p->slug) }}" class="after:absolute after:inset-0 after:z-[1] focus:outline-none">{{ $p->nama_produk }}</a>
                        </h3>
                        <div class="flex flex-wrap items-center justify-between gap-1 text-xs mb-2">
                            <div class="flex items-center gap-0.5">
                                @for ($j = 1; $j <= 5; $j++) <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 {{ $j <= round($avgRating) ? 'text-amber-400 fill-current' : 'fill-current' }}" style="{{ $j > round($avgRating) ? 'color: rgb(var(--text-faint))' : '' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg> @endfor
                                <span class="ml-1" style="color: rgb(var(--text-muted));">@if ($reviewCount > 0) {{ number_format($avgRating, 1) }} ({{ $reviewCount }}) @else Baru @endif</span>
                            </div>
                        </div>
                        <div class="mb-2">
                            @if (($p->stok ?? 0) > 0)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md tabular-nums" style="background: rgb(var(--brand-soft) / 0.65); color: rgb(var(--brand-strong));">
                                    Stok: {{ $p->stok }} {{ $p->satuan ?? 'pcs' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md" style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                                    Stok Habis
                                </span>
                            @endif
                        </div>
                        <div class="mb-3 mt-auto">
                            <span class="price text-base sm:text-xl">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                            <span class="text-xs ml-1" style="color: rgb(var(--text-muted));">/ {{ $p->satuan ?? 'pcs' }}</span>
                        </div>
                        
                        {{-- FIX LOGIKA TOMBOL TAMBAH UNTUK GUEST & ADMIN --}}
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <button type="button" onclick="showAdminNotif()" class="btn-primary relative z-10 w-full !text-xs py-2 sm:py-2.5 opacity-80 hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg> Tambah
                            </button>
                        @elseif(auth()->check())
                            <button type="button" onclick="addToCart('produk', {{ $p->id }})" class="btn-primary relative z-10 w-full !text-xs py-2 sm:py-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg> Tambah
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary relative z-10 w-full !text-xs py-2 sm:py-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg> Tambah
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="col-span-2 text-center py-6 text-sm" style="color: rgb(var(--text-muted));">Belum ada produk</p>
            @endforelse
        </div>

        <div class="mt-8 text-center sm:hidden">
            <a href="{{ route('user.produk.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">
                Lihat Semua Produk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- BANNER ZERO WASTE --}}
<section class="py-16 sm:py-24">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="relative rounded-3xl overflow-hidden p-8 sm:p-10 lg:p-16 grid md:grid-cols-2 gap-10 items-center" style="background: var(--gradient-brand); box-shadow: 0 24px 56px -16px rgb(var(--brand) / 0.4);">
            <div class="absolute top-0 right-0 w-64 sm:w-96 h-64 sm:h-96 rounded-full -translate-y-32 sm:-translate-y-48 translate-x-32 sm:translate-x-48 blur-3xl pointer-events-none" style="background: rgb(255 255 255 / 0.15);"></div>
            <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 rounded-full translate-y-24 sm:translate-y-32 -translate-x-24 sm:-translate-x-32 blur-3xl pointer-events-none" style="background: rgb(255 255 255 / 0.15);"></div>
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold uppercase tracking-widest text-white/80 mb-4 sm:mb-6"><x-icon name="recycle" class="w-4 h-4" /> Zero Waste Movement</span>
                <h3 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4 sm:mb-6 leading-tight tracking-tight">Setiap Tahu yang Kami Produksi<br>Juga Bernilai untuk Bumi</h3>
                <p class="text-base sm:text-lg text-white/90 mb-8 sm:mb-10 text-pretty max-w-lg">Ampas tahu yang biasanya terbuang, kami olah menjadi produk bernilai seperti pakan ternak, pupuk organik, dan olahan pangan. Dukung sirkular ekonomi bersama kami.</p>
                <div class="flex flex-wrap gap-3 sm:gap-4">
                    <a href="{{ route('user.limbah.index') }}" class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-4 bg-white font-bold rounded-xl sm:rounded-2xl hover:bg-amber-50 transition whitespace-nowrap shadow-lg text-sm sm:text-base" style="color: rgb(var(--brand-hover));">Lihat Produk Limbah <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>
                    <a href="{{ route('user.edukasi.index') }}" class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-4 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold rounded-xl sm:rounded-2xl transition whitespace-nowrap border border-white/30 text-sm sm:text-base">Baca Edukasi</a>
                </div>
            </div>
            <div class="hidden md:flex justify-end relative z-10">
                <div class="w-56 sm:w-72 h-56 sm:h-72 rounded-full flex items-center justify-center border" style="background: rgb(255 255 255 / 0.15); backdrop-filter: blur(20px); border-color: rgb(255 255 255 / 0.3);">
                    <div class="text-center text-white"><x-icon name="recycle" class="w-20 h-20 sm:w-28 sm:h-28 mx-auto mb-3 text-white" /><p class="text-xs sm:text-sm font-semibold tracking-widest">SDG 12</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- LIMBAH PILIHAN --}}
@if ($limbahPilihan->count() > 0)
<section class="py-16 sm:py-24">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-14 gap-4">
            <div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 sm:mb-4" style="color: rgb(var(--text-primary));">Ampas Tahu Bermanfaat</h2>
                <p class="text-base sm:text-lg" style="color: rgb(var(--text-secondary));">Kami mengubah limbah ampas tahu menjadi produk bernilai.</p>
            </div>
            <a href="{{ route('user.limbah.index') }}" class="hidden sm:inline-flex items-center justify-center gap-2 text-sm font-semibold shrink-0 transition py-2.5 px-5 rounded-full" style="color: rgb(var(--accent-hover)); background: rgb(var(--accent-soft));">Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach ($limbahPilihan as $i => $l)
                @php $avgRating = $l->averageRating(); $reviewCount = $l->reviewCount(); @endphp
                <div class="limbah-card group relative flex flex-col rounded-2xl overflow-hidden transition" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                    <div class="block relative overflow-hidden aspect-square">
                        <div class="w-full h-full flex items-center justify-center relative" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-muted));">
                            @if ($l->gambar) <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_limbah }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110"> @else <x-icon name="recycle" class="w-12 h-12 sm:w-14 sm:h-14 opacity-40" /> @endif
                            @if ($i === 0 && $reviewCount > 0)
                                <span class="absolute top-2 left-2 sm:top-3 sm:left-3 text-[10px] font-extrabold tracking-wider uppercase text-white px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full inline-flex items-center gap-1" style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.4);"><x-icon name="leaf" class="w-3 h-3" /> Populer</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-3 sm:p-5 flex flex-col flex-1 min-w-0">
                        <span class="pill-accent text-[10px] self-start mb-2">{{ $l->kategori->nama_kategori ?? 'Limbah' }}</span>
                        <h3 class="font-bold text-sm sm:text-base leading-snug line-clamp-2 min-h-[44px] sm:min-h-[48px] mb-2 tracking-tight" style="color: rgb(var(--text-primary));">
                            <a href="{{ route('user.limbah.show', $l->slug) }}" class="after:absolute after:inset-0 after:z-[1] focus:outline-none">{{ $l->nama_limbah }}</a>
                        </h3>
                        <div class="flex flex-wrap items-center gap-1 text-xs mb-2">
                            @for ($j = 1; $j <= 5; $j++) <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 {{ $j <= round($avgRating) ? 'text-amber-400 fill-current' : 'fill-current' }}" style="{{ $j > round($avgRating) ? 'color: rgb(var(--text-faint))' : '' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg> @endfor
                            <span class="ml-1" style="color: rgb(var(--text-muted));">@if ($reviewCount > 0) {{ number_format($avgRating, 1) }} ({{ $reviewCount }}) @else Baru @endif</span>
                        </div>
                        <div class="mb-2">
                            @if (($l->stok ?? 0) > 0)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md tabular-nums" style="background: rgb(var(--accent-soft) / 0.75); color: rgb(var(--accent-hover));">
                                    Stok: {{ $l->stok }} {{ $l->satuan ?? 'kg' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md" style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                                    Stok Habis
                                </span>
                            @endif
                        </div>
                        <div class="mb-3 mt-auto">
                            <span class="price text-base sm:text-xl" style="color: rgb(var(--accent-hover));">Rp {{ number_format($l->harga, 0, ',', '.') }}</span>
                            <span class="text-xs sm:text-sm ml-1" style="color: rgb(var(--text-muted));">/ {{ $l->satuan }}</span>
                        </div>

                        {{-- FIX LOGIKA TOMBOL TAMBAH UNTUK GUEST & ADMIN --}}
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <button type="button" onclick="showAdminNotif()" class="glass-btn-amber relative z-10 w-full !text-xs py-2 sm:py-2.5 opacity-80 hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg> Tambah
                            </button>
                        @elseif(auth()->check())
                            <button type="button" onclick="addToCart('limbah', {{ $l->id }})" class="glass-btn-amber relative z-10 w-full !text-xs py-2 sm:py-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg> Tambah
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="glass-btn-amber relative z-10 w-full !text-xs py-2 sm:py-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg> Tambah
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 text-center sm:hidden">
            <a href="{{ route('user.limbah.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition" style="color: rgb(var(--accent-hover)); background: rgb(var(--accent-soft));">
                Lihat Semua Limbah
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- TESTIMONI (Marquee Full-Bleed Tanpa Clipping Glow/Hover) --}}
<section class="py-16 sm:py-24 overflow-x-clip">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="text-center {{ $useRealTestimoni ? 'mb-6 sm:mb-10' : 'mb-10 sm:mb-16' }}">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-3 sm:mb-4 text-balance" style="color: rgb(var(--text-primary));">Apa Kata Mereka?</h2>
            <p class="text-base sm:text-lg max-w-lg mx-auto text-pretty" style="color: rgb(var(--text-secondary));">
                @if ($useRealTestimoni) Ulasan asli dari pelanggan yang sudah merasakan tahu sehat EcoTahu. @else Ribuan pelanggan sudah merasakan manfaat tahu sehat EcoTahu. @endif
            </p>
        </div>
    </div>

    @if ($useRealTestimoni)
        @php
            $halfItems = collect();
            while ($halfItems->count() < 6) {
                foreach ($testimoniReal as $rev) {
                    $halfItems->push($rev);
                }
            }
        @endphp
        <div class="testimoni-marquee w-full" data-no-reveal>
            <div class="testimoni-marquee-track">
                @foreach ([$halfItems, $halfItems] as $loopIdx => $group)
                    @foreach ($group as $i => $r)
                        @php
                            $warna = $warnaAvatar[$i % count($warnaAvatar)];
                            $itemName = $r->reviewable->nama_produk ?? $r->reviewable->nama_limbah ?? null;
                        @endphp
                        <div class="w-[310px] sm:w-[360px] shrink-0 flex flex-col testimoni-card p-6 sm:p-8 rounded-2xl transition relative overflow-hidden" @if($loopIdx === 1) aria-hidden="true" @endif style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                            <div class="absolute top-3 right-4 text-6xl sm:text-7xl leading-none font-serif opacity-[0.06]" style="color: rgb(var(--brand));">"</div>

                            <div class="flex items-center gap-3 sm:gap-4 mb-4 relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center text-white font-bold text-lg sm:text-xl flex-shrink-0" style="background: {{ $warna }}; box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);">{{ strtoupper(substr($r->user->username ?? 'U', 0, 1)) }}</div>
                                <div class="min-w-0">
                                    <p class="font-bold text-base sm:text-lg tracking-tight truncate" style="color: rgb(var(--text-primary));">{{ $r->user->username ?? 'User' }}</p>
                                    <p class="text-sm sm:text-base" style="color: rgb(var(--text-muted));">{{ $r->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-2 mb-3 relative z-10">
                                <div class="flex items-center gap-0.5">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <x-icon name="star-solid" class="w-4 h-4 {{ $s <= (int) $r->rating ? 'text-amber-400' : '' }}" style="{{ $s > (int) $r->rating ? 'color: rgb(var(--text-faint));' : '' }}" />
                                    @endfor
                                </div>
                                @if ($itemName)
                                    <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full truncate max-w-[170px]" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-hover));">{{ $itemName }}</span>
                                @endif
                            </div>

                            <p class="text-base sm:text-lg italic leading-relaxed mb-6 relative z-10 line-clamp-4 flex-1" style="color: rgb(var(--text-secondary));">"{{ $r->komentar }}"</p>

                            <div class="flex items-center gap-2 text-sm sm:text-base font-semibold relative z-10 mt-auto" style="color: rgb(var(--brand));">
                                <x-icon name="check-circle" class="w-4 h-4 sm:w-5 sm:h-5" />
                                Pembeli Terverifikasi
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @else
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php $testimoniDefault = [['nama' => 'Pelanggan', 'teks' => 'Jadilah yang pertama memberi ulasan setelah pesanan pertama Anda selesai!'], ['nama' => 'Pelanggan', 'teks' => 'Bagikan pengalaman Anda tentang tahu premium & ampas tahu bermanfaat dari kami.'], ['nama' => 'Pelanggan', 'teks' => 'Ulasan Anda membantu pelanggan lain memilih produk terbaik.']]; @endphp
                @foreach ($testimoniDefault as $i => $t)
                    @php $warna = $warnaAvatar[$i % count($warnaAvatar)]; @endphp
                    <div class="flex flex-col p-6 sm:p-8 rounded-2xl transition relative overflow-hidden" style="background: rgb(var(--surface)); border: 1px dashed rgb(var(--border)); opacity: 0.75;">
                        <div class="flex items-center gap-3 sm:gap-4 mb-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center text-white flex-shrink-0" style="background: {{ $warna }};"><x-icon name="user" class="w-6 h-6 sm:w-7 sm:h-7" /></div>
                            <div>
                                <p class="font-bold text-base sm:text-lg tracking-tight" style="color: rgb(var(--text-primary));">{{ $t['nama'] }}</p>
                                <p class="text-sm sm:text-base" style="color: rgb(var(--text-muted));">Menunggu ulasan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-0.5 mb-3">
                            @for ($s = 1; $s <= 5; $s++)
                                <x-icon name="star-solid" class="w-4 h-4" style="color: rgb(var(--text-faint));" />
                            @endfor
                        </div>
                        <p class="text-base sm:text-lg italic leading-relaxed flex-1" style="color: rgb(var(--text-muted));">"{{ $t['teks'] }}"</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

{{-- RESEP OLAHAN TAHU --}}
<section class="py-16 sm:py-24">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-14 gap-4">
            <div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 sm:mb-4" style="color: rgb(var(--text-primary));">Resep Olahan Tahu</h2>
                <p class="text-base sm:text-lg" style="color: rgb(var(--text-secondary));">Inspirasi menu sehat &amp; lezat dari bahan tahu organik pilihan.</p>
            </div>
            <a href="{{ route('user.resep.index') }}" class="hidden sm:inline-flex items-center justify-center gap-2 text-sm font-semibold shrink-0 transition py-2.5 px-5 rounded-full" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>
        </div>

        @if (isset($resepTerbaru) && $resepTerbaru->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-8">
                @foreach ($resepTerbaru as $r)
                    @php
                        $rDate = $r->published_at ?? $r->created_at;
                        $diffBadgeStyle = match($r->tingkat_kesulitan) {
                            'sulit'  => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger));',
                            'sedang' => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning));',
                            default  => 'background: rgb(var(--success-soft)); color: rgb(var(--success));',
                        };
                    @endphp
                    <a href="{{ route('user.resep.show', $r->slug) }}" class="edukasi-card group block rounded-2xl overflow-hidden transition flex flex-col" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                        <div class="w-full aspect-[4/3] sm:aspect-video overflow-hidden relative" style="background: rgb(var(--bg-secondary));">
                            @if ($r->display_thumbnail_url)
                                <img src="{{ $r->display_thumbnail_url }}" alt="{{ $r->judul }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center" style="color: rgb(var(--text-muted));"><x-icon name="utensils" class="w-12 h-12 opacity-40" /></div>
                            @endif
                            @if (($r->media_type ?? 'image') === 'youtube')
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-600/90 text-white shadow inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                    YouTube
                                </span>
                            @elseif (($r->media_type ?? 'image') === 'video')
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-600/90 text-white shadow inline-flex items-center gap-1"><x-icon name="film" class="w-3 h-3" /> Video</span>
                            @endif
                        </div>
                        <div class="p-4 sm:p-6 flex flex-col flex-1 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2 sm:mb-3">
                                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold capitalize" style="{{ $diffBadgeStyle }}">{{ $r->tingkat_kesulitan }}</span>
                                <span class="text-[10px] sm:text-xs font-semibold tabular-nums inline-flex items-center gap-1" style="color: rgb(var(--danger));"><x-icon name="heart-solid" class="w-3.5 h-3.5 text-rose-500" /> {{ $r->likes_count ?? 0 }}</span>
                            </div>
                            <h3 class="font-bold text-sm sm:text-lg mb-2 line-clamp-2 leading-snug tracking-tight" style="color: rgb(var(--text-primary));">{{ $r->judul }}</h3>
                            <p class="text-[11px] sm:text-xs tabular-nums mt-auto flex flex-wrap items-center gap-1.5" style="color: rgb(var(--text-muted));">
                                <span class="inline-flex items-center gap-1"><x-icon name="clock" class="w-3.5 h-3.5" /> {{ $r->durasi_menit }} mnt</span>
                                <span>·</span>
                                <span class="inline-flex items-center gap-1"><x-icon name="utensils" class="w-3.5 h-3.5" /> {{ $r->porsi }} porsi</span>
                                @if($rDate) <span>· {{ $rDate->format('d M Y') }}</span> @endif
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('user.resep.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">
                    Lihat Semua Resep
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        @else
            <div class="rounded-2xl p-8 sm:p-12 text-center" style="background: rgb(var(--surface)); border: 1px dashed rgb(var(--border));">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand));">
                    <x-icon name="utensils" class="w-7 h-7" />
                </div>
                <h3 class="text-base sm:text-xl font-bold mb-2" style="color: rgb(var(--text-primary));">Belum Ada Resep Dipublikasikan</h3>
                <p class="text-xs sm:text-sm max-w-md mx-auto mb-5" style="color: rgb(var(--text-secondary));">
                    Kreasi menu olahan tahu organik pilihan akan tampil di sini setelah admin mempublikasikan resep baru.
                </p>
                <a href="{{ route('user.resep.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full text-xs sm:text-sm font-semibold transition" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">
                    Jelajahi Halaman Resep
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- EDUKASI --}}
<section class="py-16 sm:py-24">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-14 gap-4">
            <div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 sm:mb-4" style="color: rgb(var(--text-primary));">Edukasi Lingkungan</h2>
                <p class="text-base sm:text-lg" style="color: rgb(var(--text-secondary));">Belajar bareng tentang pelestarian lingkungan & gaya hidup berkelanjutan.</p>
            </div>
            <a href="{{ route('user.edukasi.index') }}" class="hidden sm:inline-flex items-center justify-center gap-2 text-sm font-semibold shrink-0 transition py-2.5 px-5 rounded-full" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-8">
            @forelse ($edukasi as $e)
                @php $eDate = $e->published_at ?? $e->tanggal_mengunggah ?? $e->created_at; @endphp
                <a href="{{ route('user.edukasi.show', $e->slug) }}" class="edukasi-card group block rounded-2xl overflow-hidden transition flex flex-col" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); box-shadow: var(--shadow-sm);">
                    <div class="w-full aspect-[4/3] sm:aspect-video overflow-hidden relative" style="background: rgb(var(--bg-secondary));">
                        @if ($e->display_thumbnail_url)
                            <img src="{{ $e->display_thumbnail_url }}" alt="{{ $e->judul }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-full flex items-center justify-center" style="color: rgb(var(--text-muted));"><x-icon name="book-open" class="w-12 h-12 opacity-40" /></div>
                        @endif
                    </div>
                    <div class="p-4 sm:p-6 flex flex-col flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2 sm:mb-3">
                            <span class="pill text-[9px] sm:text-[10px] inline-flex items-center gap-1"><x-icon name="book-open" class="w-3 h-3" /> Artikel</span>
                            <span class="text-[10px] sm:text-xs tnum" style="color: rgb(var(--text-muted));">{{ $eDate?->format('d M Y') }}</span>
                        </div>
                        <h3 class="font-bold text-sm sm:text-lg mb-2 sm:mb-3 line-clamp-2 leading-snug tracking-tight" style="color: rgb(var(--text-primary));">{{ $e->judul }}</h3>
                        <p class="text-xs sm:text-base line-clamp-2 sm:line-clamp-3 text-pretty leading-relaxed" style="color: rgb(var(--text-secondary));">{{ Str::limit(strip_tags($e->konten), 100) }}</p>
                    </div>
                </a>
            @empty
                <p class="col-span-2 md:col-span-3 text-center py-6 text-sm" style="color: rgb(var(--text-muted));">Belum ada artikel</p>
            @endforelse
        </div>

        <div class="mt-8 text-center sm:hidden">
            <a href="{{ route('user.edukasi.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">
                Lihat Semua Edukasi
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .category-card { box-shadow: 0 1px 3px rgb(15 23 42 / 0.05); }
    .arrow-link { gap: 0.375rem; transition: gap 0.25s ease, color 0.25s ease; }
    .arrow-link:hover { gap: 0.625rem; }
    html { scroll-behavior: smooth; }
    .hero-gradient-text { display: inline-block; padding-bottom: 0.2em; margin-bottom: -0.2em; padding-right: 0.1em; margin-right: -0.1em; -webkit-box-decoration-break: clone; box-decoration-break: clone; }
    @keyframes cart-badge-pop { 0% { transform: scale(1); } 50% { transform: scale(1.5); } 100% { transform: scale(1); } }
    .cart-badge-pop { animation: cart-badge-pop 0.4s cubic-bezier(0.22, 1, 0.36, 1); }

    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* =========================================
       1. BACKDROP ECO PORTAL (Aura & Rings)
    ========================================= */
    .eco-portal-stage {
        perspective: 1000px;
    }

    .eco-portal-aura {
        position: absolute;
        top: 48%;
        left: 50%;
        width: 94%;
        aspect-ratio: 1 / 1;
        transform: translate(-50%, -50%);
        border-radius: 9999px;
        background: radial-gradient(
            circle,
            rgb(var(--brand) / 0.22) 0%,
            rgb(var(--brand) / 0.12) 35%,
            rgb(var(--brand) / 0.04) 55%,
            transparent 75%
        );
        filter: blur(48px);
        -webkit-filter: blur(48px);
        z-index: 0;
        pointer-events: none;
    }

    .eco-portal-ring-outer {
        position: absolute;
        top: 48%;
        left: 50%;
        width: 88%;
        aspect-ratio: 1 / 1;
        border-radius: 9999px;
        border: 1.5px dashed rgb(var(--brand) / 0.35);
        box-shadow: 0 0 35px rgb(var(--brand) / 0.12), inset 0 0 25px rgb(var(--brand) / 0.08);
        animation: portalSpinSlow 28s linear infinite;
        z-index: 1;
        pointer-events: none;
    }

    .eco-portal-ring-inner {
        position: absolute;
        top: 48%;
        left: 50%;
        width: 72%;
        aspect-ratio: 1 / 1;
        border-radius: 9999px;
        border: 1px solid rgb(var(--brand) / 0.25);
        border-top-color: rgb(var(--brand) / 0.7);
        border-bottom-color: rgb(var(--accent) / 0.5);
        animation: portalSpinReverse 18s linear infinite;
        z-index: 1;
        pointer-events: none;
    }

    @keyframes portalSpinSlow {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to   { transform: translate(-50%, -50%) rotate(360deg); }
    }

    @keyframes portalSpinReverse {
        from { transform: translate(-50%, -50%) rotate(360deg); }
        to   { transform: translate(-50%, -50%) rotate(0deg); }
    }

    /* =========================================
       2. ANIMASI MASKOT, HOVER & GRADASI POTONGAN
    ========================================= */
    .mascot-interactive-box {
        animation: trayFloat 4.2s ease-in-out infinite;
        transform-origin: bottom center;
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), filter 0.35s ease;
        will-change: transform;
    }

    /* Efek Hover: Karakter sedikit terangkat, menyala, dan langsung tersenyum */
    .mascot-interactive-box:hover {
        transform: translateY(-8px) scale(1.025);
        filter: drop-shadow(0 16px 32px rgb(var(--brand) / 0.3));
    }

    .mascot-interactive-box:hover .img-smile {
        opacity: 1 !important;
    }

    /* Masking Gradasi Halus di Bagian Bawah Tubuh Maskot */
    .mascot-fade-mask {
        -webkit-mask-image: linear-gradient(to bottom, #000 62%, rgba(0, 0, 0, 0.85) 78%, transparent 97%);
        mask-image: linear-gradient(to bottom, #000 62%, rgba(0, 0, 0, 0.85) 78%, transparent 97%);
    }

    /* Tumpukan 2 Layer Gambar (Idle & Smile Saja) */
    .mascot-layer {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        pointer-events: none;
    }

    .img-idle {
        opacity: 1;
        z-index: 1;
    }

    .img-smile {
        opacity: 0;
        z-index: 2;
        transition: opacity 0.35s ease;
        animation: mascotSmileLoop 6s ease-in-out infinite;
    }

    /* Gradasi Bawah Otomatis Adaptif Mengikuti Background Normal Project */
    .mascot-bottom-fade {
        position: absolute;
        inset-x: 0;
        bottom: -4px;
        height: 5.5rem;
        background: linear-gradient(to top, rgb(var(--bg-primary)) 15%, rgb(var(--bg-primary) / 0.85) 55%, transparent 100%);
        pointer-events: none;
        z-index: 20;
    }

    /* Gerakan Mengayun Halus Membawa Nampan Tahu */
    @keyframes trayFloat {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-9px) rotate(0.8deg);
        }
    }

    /* Siklus Ganti Ekspresi: Senyum Ramah */
    @keyframes mascotSmileLoop {
        0%, 52% { opacity: 0; }
        58%, 90% { opacity: 1; }
        96%, 100% { opacity: 0; }
    }

    /* =========================================
       3. ANIMASI UAP TAHU MENGEBUL TEPAT DI ATAS TAHU
    ========================================= */
    .tofu-steam-zone {
        position: absolute;
        bottom: 22%;
        left: 10%;
        width: 52%;
        height: 26%;
        pointer-events: none;
        z-index: 15;
    }

    .steam-wisp {
        position: absolute;
        bottom: 0;
        width: 24px;
        height: 56px;
        border-radius: 50%;
        background: radial-gradient(
            circle,
            rgba(255, 255, 255, 0.88) 0%,
            rgba(236, 253, 245, 0.48) 45%,
            transparent 75%
        );
        filter: blur(6px);
        opacity: 0;
        animation: tofuSteamRise 3.6s ease-out infinite;
    }

    .steam-wisp.w1 { left: 12%; animation-delay: 0s; }
    .steam-wisp.w2 { left: 32%; animation-delay: 0.9s; width: 30px; height: 64px; }
    .steam-wisp.w3 { left: 54%; animation-delay: 1.8s; }
    .steam-wisp.w4 { left: 72%; animation-delay: 2.7s; width: 26px; height: 58px; }

    @keyframes tofuSteamRise {
        0% {
            transform: translateY(0) translateX(0) scaleX(0.85) scaleY(0.75);
            opacity: 0;
        }
        20% {
            opacity: 0.75;
        }
        60% {
            transform: translateY(-44px) translateX(6px) scaleX(1.3) scaleY(1.15);
            opacity: 0.38;
        }
        100% {
            transform: translateY(-88px) translateX(-4px) scaleX(1.7) scaleY(1.45);
            opacity: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .eco-portal-ring-outer,
        .eco-portal-ring-inner,
        .mascot-interactive-box,
        .steam-wisp,
        .img-smile {
            animation: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function showAdminNotif() { showToast('Admin tidak dapat melakukan pemesanan.', 'error'); }
    function addToCart(type, id) {
        fetch('{{ route('user.cart.add') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ type: type, id: id, qty: 1 })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                showToast(data.message || 'Ditambahkan ke keranjang!');
                if (data.cart_count !== undefined) setCartBadge(data.cart_count);
                else if (data.count !== undefined) setCartBadge(data.count);
                else incrementCartBadge();
            } else showToast(data.message || 'Gagal menambahkan', 'error');
        }).catch(err => { console.error(err); showToast('Terjadi kesalahan', 'error'); });
    }
    function setCartBadge(count) {
        const cartLink = document.querySelector('a[title="Keranjang"]');
        if (!cartLink) return;
        let badge = cartLink.querySelector('[data-cart-badge]');
        if (count <= 0) { if (badge) badge.remove(); return; }
        if (!badge) {
            badge = document.createElement('span');
            badge.setAttribute('data-cart-badge', '');
            badge.className = 'absolute top-0.5 right-0.5 min-w-[16px] h-[16px] px-1 text-white text-[9px] font-bold rounded-full flex items-center justify-center tnum';
            badge.style.cssText = 'background: var(--gradient-brand); box-shadow: 0 0 0 2px rgb(var(--surface)), 0 2px 6px rgb(var(--brand) / 0.5);';
            cartLink.appendChild(badge);
        }
        badge.textContent = count > 9 ? '9+' : count;
        badge.classList.remove('cart-badge-pop');
        void badge.offsetWidth;
        badge.classList.add('cart-badge-pop');
    }
    function incrementCartBadge() {
        const cartLink = document.querySelector('a[title="Keranjang"]');
        if (!cartLink) return;
        let badge = cartLink.querySelector('[data-cart-badge]');
        const current = badge ? parseInt(badge.textContent.replace('+', '')) || 0 : 0;
        setCartBadge(current + 1);
    }
    function showToast(message, type = 'success') {
        const isSuccess = type === 'success';
        const toast = document.createElement('div');
        toast.className = 'fixed top-24 right-4 z-[999] text-white px-5 py-3 rounded-xl shadow-2xl text-xs sm:text-sm font-semibold flex items-center gap-2';
        toast.style.background = isSuccess ? 'linear-gradient(135deg, #10b981 0%, #047857 100%)' : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        toast.style.transform = 'translateX(400px)'; toast.style.transition = 'transform 0.35s cubic-bezier(0.4, 0, 0.2, 1)'; toast.style.boxShadow = isSuccess ? '0 12px 32px rgb(16 185 129 / 0.4)' : '0 12px 32px rgb(239 68 68 / 0.4)';
        toast.innerHTML = (isSuccess ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>') + '<span>' + message + '</span>';
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 50);
        setTimeout(() => { toast.style.transform = 'translateX(400px)'; setTimeout(() => toast.remove(), 350); }, 2500);
    }
</script>
@endpush