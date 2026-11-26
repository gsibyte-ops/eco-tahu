@extends('layouts.user')
@section('title', 'Edukasi Lingkungan')

@section('content')

<section class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card text-center max-w-3xl mx-auto p-5 sm:p-10">
            <span class="pill text-[10px] sm:text-xs inline-flex items-center gap-1"><x-icon name="book-open" class="w-3.5 h-3.5" /> EcoTahu Journal</span>
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
            <div class="glass-card !overflow-visible relative z-20 p-3 sm:p-4 mb-4 sm:mb-6">
                <form method="GET" class="flex flex-wrap gap-2">
                    <div class="relative flex-1 min-w-[150px]">
                        <x-live-search-input
                            name="q"
                            :value="request('q', '')"
                            scope="edukasi"
                            placeholder="Cari judul artikel edukasi..."
                            accent="emerald" />
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
                        @php $eDate = $e->published_at ?? $e->tanggal_mengunggah ?? $e->created_at; @endphp
                        <a href="{{ route('user.edukasi.show', $e->slug) }}" class="surface edukasi-card overflow-hidden flex flex-col rounded-xl sm:rounded-2xl">
                            <div class="w-full aspect-[4/3] sm:aspect-video overflow-hidden relative" style="background: rgb(var(--bg-secondary));">
                                @if ($e->display_thumbnail_url)
                                    <img src="{{ $e->display_thumbnail_url }}" alt="{{ $e->judul }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="color: rgb(var(--text-muted));">
                                        <x-icon :name="($e->media_type ?? 'image') === 'video' ? 'film' : 'book-open'" class="w-10 h-10 sm:w-12 sm:h-12 opacity-40" />
                                    </div>
                                @endif
                                @if (($e->media_type ?? 'image') === 'youtube')
                                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-red-600/90 text-white shadow">▶ YouTube</span>
                                @elseif (($e->media_type ?? 'image') === 'video')
                                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-blue-600/90 text-white shadow inline-flex items-center gap-1"><x-icon name="film" class="w-3 h-3" /> Video</span>
                                @endif
                            </div>
                            <div class="p-2.5 sm:p-5 flex flex-col flex-1 min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-1.5 sm:gap-2 mb-1.5 sm:mb-3">
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <span class="pill text-[8px] sm:text-[10px] inline-flex items-center gap-1"><x-icon name="book-open" class="w-3 h-3" /> Artikel</span>
                                        <span class="text-[9px] sm:text-xs tabular-nums" style="color: rgb(var(--text-muted));">{{ $eDate?->format('d M Y') }}</span>
                                    </div>
                                    <span class="text-[10px] sm:text-xs font-semibold tabular-nums inline-flex items-center gap-1" style="color: rgb(var(--danger));"><x-icon name="heart-solid" class="w-3.5 h-3.5 text-rose-500" /> {{ $e->likes_count ?? 0 }}</span>
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
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand));"><x-icon name="search" class="w-8 h-8" /></div>
                    <h3 class="text-base sm:text-xl font-bold mb-2" style="color: rgb(var(--text-primary));">Artikel tidak ditemukan</h3>
                    <p class="text-xs sm:text-sm mb-4 sm:mb-6" style="color: rgb(var(--text-secondary));">Coba kata kunci lain, atau jelajahi semua artikel.</p>
                    <a href="{{ route('user.edukasi.index') }}" class="btn-primary inline-block text-xs sm:text-sm px-4 sm:px-5 py-2">Lihat Semua Artikel</a>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-24 space-y-4 sm:space-y-5">
                <div class="glass-card p-4 sm:p-5">
                    <h3 class="font-bold mb-3 sm:mb-4 text-sm sm:text-base flex items-center gap-1.5" style="color: rgb(var(--text-primary));"><x-icon name="bookmark" class="w-4 h-4" style="color: rgb(var(--brand));" /> Artikel Terbaru</h3>
                    <div class="space-y-3 sm:space-y-4">
                        @forelse ($terbaru as $t)
                            @php $tDate = $t->published_at ?? $t->tanggal_mengunggah ?? $t->created_at; @endphp
                            <a href="{{ route('user.edukasi.show', $t->slug) }}" class="flex gap-2 sm:gap-3 group">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex-shrink-0 overflow-hidden" style="background: rgb(var(--bg-secondary));">
                                    @if ($t->display_thumbnail_url)
                                        <img src="{{ $t->display_thumbnail_url }}" alt="{{ $t->judul }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center" style="color: rgb(var(--text-muted));"><x-icon name="book-open" class="w-5 h-5 opacity-50" /></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-medium line-clamp-2 group-hover:text-emerald-500 transition leading-snug" style="color: rgb(var(--text-primary));">
                                        {{ $t->judul }}
                                    </p>
                                    <p class="text-[9px] sm:text-xs mt-1 tabular-nums flex items-center gap-1" style="color: rgb(var(--text-muted));">{{ $tDate?->format('d M Y') }} · <x-icon name="heart-solid" class="w-3 h-3 text-rose-500" /> {{ $t->likes_count ?? 0 }}</p>
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