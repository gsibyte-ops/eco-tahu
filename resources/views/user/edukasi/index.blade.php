@extends('layouts.user')
@section('title', 'Edukasi Lingkungan')

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-50 via-white to-amber-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold mb-3">
                📖 EcoTahu Journal
            </span>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 mb-3">
                Edukasi Lingkungan
            </h1>
            <p class="text-gray-600 leading-relaxed">
                Belajar bareng tentang pelestarian lingkungan, gaya hidup berkelanjutan, dan kisah di balik secuil tahu yang ramah bumi.
            </p>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- MAIN CONTENT --}}
        <div class="lg:col-span-3">

            {{-- Search --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
                <form method="GET" class="flex gap-3">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel edukasi..."
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
                        Cari
                    </button>
                    @if (request('q'))
                        <a href="{{ route('user.edukasi.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Result Info --}}
            @if (request('q'))
                <p class="text-sm text-gray-500 mb-4">
                    Menampilkan <span class="font-semibold text-gray-800">{{ $edukasi->total() }}</span> hasil untuk "<span class="font-semibold text-emerald-600">{{ request('q') }}</span>"
                </p>
            @endif

            {{-- GRID ARTIKEL --}}
            @if ($edukasi->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($edukasi as $e)
                        <a href="{{ route('user.edukasi.show', $e->slug) }}"
                           class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                            <div class="aspect-video bg-gradient-to-br from-emerald-100 to-amber-50 overflow-hidden">
                                @if ($e->thumbnail)
                                    <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-6xl">📖</div>
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                        Edukasi
                                    </span>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500">{{ $e->tanggal_mengunggah->format('d M Y') }}</span>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-emerald-600 transition">
                                    {{ $e->judul }}
                                </h3>
                                <p class="text-sm text-gray-500 line-clamp-3 leading-relaxed">
                                    {{ Str::limit(strip_tags($e->konten), 120) }}
                                </p>
                                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-50">
                                    <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">
                                        {{ strtoupper(substr($e->user->username ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $e->user->username ?? 'Admin' }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">{{ $edukasi->links() }}</div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Artikel tidak ditemukan</h3>
                    <p class="text-sm text-gray-500 mb-6">Coba kata kunci lain, atau jelajahi semua artikel.</p>
                    <a href="{{ route('user.edukasi.index') }}"
                       class="inline-block px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition">
                        Lihat Semua Artikel
                    </a>
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4">📌 Artikel Terbaru</h3>

                <div class="space-y-4">
                    @forelse ($terbaru as $t)
                        <a href="{{ route('user.edukasi.show', $t->slug) }}" class="flex gap-3 group">
                            <div class="w-14 h-14 rounded-lg bg-emerald-50 flex-shrink-0 overflow-hidden">
                                @if ($t->thumbnail)
                                    <img src="{{ asset('storage/' . $t->thumbnail) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xl">📖</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 line-clamp-2 group-hover:text-emerald-600 transition">
                                    {{ $t->judul }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $t->tanggal_mengunggah->format('d M Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada artikel</p>
                    @endforelse
                </div>
            </div>

            {{-- Info SDG Card --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 mt-5 text-white">
                <p class="text-xs font-semibold opacity-90 mb-1">SDG 12</p>
                <h4 class="font-bold text-lg mb-2 leading-tight">Konsumsi & Produksi yang Bertanggung Jawab</h4>
                <p class="text-xs opacity-90 leading-relaxed">
                    Setiap pembelian EcoTahu membantu terwujudnya pola konsumsi berkelanjutan.
                </p>
            </div>
        </aside>
    </div>
</div>

@endsection