@extends('layouts.user')
@section('title', 'Edukasi Lingkungan')

@section('content')

<section class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card text-center max-w-3xl mx-auto p-5 sm:p-10">
            <span class="pill text-[10px] sm:text-xs">📖 EcoTahu Journal</span>
            <h1 class="text-xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight mb-2 sm:mb-3 mt-3 sm:mt-4" style="color: rgb(var(--text-primary));">
                Edukasi Lingkungan
            </h1>
            <p class="text-xs sm:text-base leading-relaxed text-pretty" style="color: rgb(var(--text-secondary));">
                Belajar bareng tentang pelestarian lingkungan, gaya hidup berkelanjutan, dan kisah di balik secuil tahu yang ramah bumi.
            </p>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8">

        <div class="lg:col-span-3">
            <div class="glass-card p-3 sm:p-4 mb-4 sm:mb-6">
                <form method="GET" class="flex flex-wrap gap-2">
                    <div class="relative flex-1 min-w-[150px]">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..."
                               class="glass-input w-full px-3 py-1.5 sm:py-2 text-xs sm:text-sm" style="color: rgb(var(--text-primary));">
                    </div>
                    <button type="submit" class="glass-btn-primary text-xs sm:text-sm whitespace-nowrap px-4 sm:px-6 py-1.5 sm:py-2">Cari</button>
                    @if (request('q'))
                        <a href="{{ route('user.edukasi.index') }}" class="glass-btn text-xs sm:text-sm whitespace-nowrap px-3 sm:px-5 py-1.5 sm:py-2">Reset</a>
                    @endif
                </form>
            </div>

            @if (request('q'))
                <div class="glass-card inline-flex items-center px-3 py-1.5 mb-4">
                    <p class="text-xs sm:text-sm" style="color: rgb(var(--text-secondary));">
                        Menampilkan <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">{{ $edukasi->total() }}</span> hasil untuk "<span class="font-semibold" style="color: rgb(var(--brand));">{{ request('q') }}</span>"
                    </p>
                </div>
            @endif

            @if ($edukasi->count() > 0)
                {{-- FIX: Grid 2 Kolom Rapih --}}
                <div class="grid grid-cols-2 md:grid-cols-2 gap-3 sm:gap-6">
                    @foreach ($edukasi as $e)
                        <a href="{{ route('user.edukasi.show', $e->slug) }}" class="surface edukasi-card overflow-hidden flex flex-col rounded-xl sm:rounded-2xl">
                            <div class="w-full aspect-[4/3] sm:aspect-video overflow-hidden" style="background: rgb(var(--bg-secondary));">
                                @if ($e->thumbnail)
                                    <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-3xl sm:text-6xl">📖</div>
                                @endif
                            </div>
                            <div class="p-2.5 sm:p-5 flex flex-col flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1.5 sm:mb-3">
                                    <span class="pill text-[8px] sm:text-[10px]">📚 Artikel</span>
                                    <span class="text-[9px] sm:text-xs tabular-nums" style="color: rgb(var(--text-muted));">{{ $e->tanggal_mengunggah->format('d M Y') }}</span>
                                </div>
                                <h3 class="font-bold text-[13px] sm:text-base mb-1.5 sm:mb-2 line-clamp-2 leading-snug" style="color: rgb(var(--text-primary));">{{ $e->judul }}</h3>
                                <p class="text-[11px] sm:text-sm line-clamp-2 sm:line-clamp-3 leading-relaxed text-pretty" style="color: rgb(var(--text-secondary));">
                                    {{ Str::limit(strip_tags($e->konten), 120) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 sm:mt-8">{{ $edukasi->links() }}</div>
            @else
                <div class="glass-card p-10 sm:p-16 text-center">
                    <div class="text-4xl sm:text-6xl mb-4">🔍</div>
                    <h3 class="text-base sm:text-xl font-bold mb-2" style="color: rgb(var(--text-primary));">Artikel tidak ditemukan</h3>
                    <p class="text-xs sm:text-sm mb-4 sm:mb-6" style="color: rgb(var(--text-secondary));">Coba kata kunci lain, atau jelajahi semua artikel.</p>
                    <a href="{{ route('user.edukasi.index') }}" class="btn-primary inline-block text-xs sm:text-sm px-4 sm:px-5 py-2">Lihat Semua Artikel</a>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-24 space-y-4 sm:space-y-5">
                <div class="glass-card p-4 sm:p-5">
                    <h3 class="font-bold mb-3 sm:mb-4 text-sm sm:text-base" style="color: rgb(var(--text-primary));">📌 Artikel Terbaru</h3>
                    <div class="space-y-3 sm:space-y-4">
                        @forelse ($terbaru as $t)
                            <a href="{{ route('user.edukasi.show', $t->slug) }}" class="flex gap-2 sm:gap-3 group">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex-shrink-0 overflow-hidden" style="background: rgb(var(--bg-secondary));">
                                    @if ($t->thumbnail)
                                        <img src="{{ asset('storage/' . $t->thumbnail) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-lg sm:text-xl">📖</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-medium line-clamp-2 group-hover:text-emerald-500 transition leading-snug" style="color: rgb(var(--text-primary));">
                                        {{ $t->judul }}
                                    </p>
                                    <p class="text-[9px] sm:text-xs mt-1 tabular-nums" style="color: rgb(var(--text-muted));">{{ $t->tanggal_mengunggah->format('d M Y') }}</p>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs sm:text-sm text-center py-2" style="color: rgb(var(--text-muted));">Belum ada artikel</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl sm:rounded-2xl p-4 sm:p-5 text-white" style="background: var(--gradient-brand); box-shadow: 0 12px 32px -8px rgb(var(--brand) / 0.4);">
                    <p class="text-[10px] sm:text-xs font-semibold opacity-90 mb-1">SDG 12</p>
                    <h4 class="font-bold text-sm sm:text-lg mb-1 sm:mb-2 leading-tight">Konsumsi & Produksi yang Bertanggung Jawab</h4>
                    <p class="text-[10px] sm:text-xs opacity-90 leading-relaxed">Setiap pembelian EcoTahu membantu terwujudnya pola konsumsi berkelanjutan.</p>
                </div>
            </div>
        </aside>
    </div>
    
    {{-- Tombol Kembali Mobile --}}
    <div class="mt-8 text-center sm:hidden">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Beranda
        </a>
    </div>
</div>

@endsection