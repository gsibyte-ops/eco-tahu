@extends('layouts.user')
@section('title', 'Edukasi Lingkungan')

@section('content')

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card text-center max-w-3xl mx-auto p-10">
            <span class="pill">📖 EcoTahu Journal</span>
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-3 mt-4" style="color: rgb(var(--text-primary));">
                Edukasi Lingkungan
            </h1>
            <p class="leading-relaxed text-pretty" style="color: rgb(var(--text-secondary));">
                Belajar bareng tentang pelestarian lingkungan, gaya hidup berkelanjutan, dan kisah di balik secuil tahu yang ramah bumi.
            </p>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <div class="lg:col-span-3">
            <div class="glass-card p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-3">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color: rgb(var(--text-muted));"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel edukasi..."
                               class="glass-input w-full pl-10 pr-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                    </div>
                    <button type="submit" class="glass-btn-primary text-sm whitespace-nowrap">Cari</button>
                    @if (request('q'))
                        <a href="{{ route('user.edukasi.index') }}" class="glass-btn text-sm whitespace-nowrap px-5 py-2.5">Reset</a>
                    @endif
                </form>
            </div>

            @if (request('q'))
                <div class="glass-card inline-flex items-center px-4 py-2 mb-4">
                    <p class="text-sm" style="color: rgb(var(--text-secondary));">
                        Menampilkan <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">{{ $edukasi->total() }}</span> hasil untuk "<span class="font-semibold" style="color: rgb(var(--brand));">{{ request('q') }}</span>"
                    </p>
                </div>
            @endif

            @if ($edukasi->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($edukasi as $e)
                        <a href="{{ route('user.edukasi.show', $e->slug) }}" class="surface edukasi-card overflow-hidden block">
                            <div class="aspect-video overflow-hidden" style="background: rgb(var(--bg-secondary));">
                                @if ($e->thumbnail)
                                    <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-6xl">📖</div>
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="pill text-[10px]">📚 Edukasi</span>
                                    <span class="text-xs tabular-nums" style="color: rgb(var(--text-muted));">{{ $e->tanggal_mengunggah->format('d M Y') }}</span>
                                </div>
                                <h3 class="font-bold mb-2 line-clamp-2 leading-snug" style="color: rgb(var(--text-primary));">{{ $e->judul }}</h3>
                                <p class="text-sm line-clamp-3 leading-relaxed text-pretty" style="color: rgb(var(--text-secondary));">
                                    {{ Str::limit(strip_tags($e->konten), 120) }}
                                </p>
                                <div class="flex items-center gap-2 mt-4 pt-4" style="border-top: 1px solid rgb(var(--border-soft));">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm"
                                         style="background: var(--gradient-brand);">
                                        {{ strtoupper(substr($e->user->username ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="text-xs" style="color: rgb(var(--text-secondary));">{{ $e->user->username ?? 'Admin' }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">{{ $edukasi->links() }}</div>
            @else
                <div class="glass-card p-16 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold mb-2" style="color: rgb(var(--text-primary));">Artikel tidak ditemukan</h3>
                    <p class="text-sm mb-6" style="color: rgb(var(--text-secondary));">Coba kata kunci lain, atau jelajahi semua artikel.</p>
                    <a href="{{ route('user.edukasi.index') }}" class="btn-primary inline-block">Lihat Semua Artikel</a>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-1">
            <div class="glass-card p-5 sticky top-24">
                <h3 class="font-bold mb-4" style="color: rgb(var(--text-primary));">📌 Artikel Terbaru</h3>

                <div class="space-y-4">
                    @forelse ($terbaru as $t)
                        <a href="{{ route('user.edukasi.show', $t->slug) }}" class="flex gap-3 group">
                            <div class="w-14 h-14 rounded-lg flex-shrink-0 overflow-hidden" style="background: rgb(var(--bg-secondary));">
                                @if ($t->thumbnail)
                                    <img src="{{ asset('storage/' . $t->thumbnail) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xl">📖</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium line-clamp-2 group-hover:text-emerald-500 transition leading-snug" style="color: rgb(var(--text-primary));">
                                    {{ $t->judul }}
                                </p>
                                <p class="text-xs mt-1 tabular-nums" style="color: rgb(var(--text-muted));">{{ $t->tanggal_mengunggah->format('d M Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-center py-4" style="color: rgb(var(--text-muted));">Belum ada artikel</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl p-5 mt-5 text-white" style="background: var(--gradient-brand); box-shadow: 0 12px 32px -8px rgb(var(--brand) / 0.4);">
                <p class="text-xs font-semibold opacity-90 mb-1">SDG 12</p>
                <h4 class="font-bold text-lg mb-2 leading-tight">Konsumsi & Produksi yang Bertanggung Jawab</h4>
                <p class="text-xs opacity-90 leading-relaxed">Setiap pembelian EcoTahu membantu terwujudnya pola konsumsi berkelanjutan.</p>
            </div>
        </aside>
    </div>
</div>

@endsection