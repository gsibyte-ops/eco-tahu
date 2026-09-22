@extends('layouts.user')
@section('title', $artikel->judul)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('user.edukasi.index') }}" class="hover:text-emerald-600">Edukasi</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium truncate">{{ Str::limit($artikel->judul, 30) }}</span>
    </nav>

    {{-- ARTICLE HEADER --}}
    <header class="mb-8">
        <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-semibold mb-3">
            🌿 Edukasi Lingkungan
        </span>

        <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 leading-tight mb-5">
            {{ $artikel->judul }}
        </h1>

        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                    {{ strtoupper(substr($artikel->user->username ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $artikel->user->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">Penulis</p>
                </div>
            </div>
            <span class="text-gray-300">|</span>
            <div class="text-sm text-gray-500">
                📅 {{ $artikel->tanggal_mengunggah->format('d M Y') }}
            </div>
        </div>
    </header>

    {{-- THUMBNAIL --}}
    @if ($artikel->thumbnail)
        <div class="rounded-2xl overflow-hidden mb-8 shadow-md">
            <img src="{{ asset('storage/' . $artikel->thumbnail) }}"
                 alt="{{ $artikel->judul }}"
                 class="w-full aspect-video object-cover">
        </div>
    @endif

    {{-- CONTENT --}}
    <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 mb-8">
        <div class="prose prose-emerald max-w-none text-gray-700 leading-relaxed text-base whitespace-pre-line">
            {!! nl2br(e($artikel->konten)) !!}
        </div>
    </article>

    {{-- SHARE --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="font-semibold text-gray-800 text-sm">Bagikan artikel ini</p>
                <p class="text-xs text-gray-500">Sebarkan kesadaran lingkungan ke orang lain</p>
            </div>
            <div class="flex gap-2">
                <a href="https://wa.me/?text={{ urlencode($artikel->judul . ' - ' . url()->current()) }}" target="_blank"
                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <button onclick="navigator.clipboard.writeText('{{ url()->current() }}'); alert('Link disalin!')"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ARTIKEL TERKAIT --}}
    @if ($terkait->count() > 0)
        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-5">Artikel Terkait</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($terkait as $t)
                    <a href="{{ route('user.edukasi.show', $t->slug) }}"
                       class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                        <div class="aspect-video bg-emerald-50 overflow-hidden">
                            @if ($t->thumbnail)
                                <img src="{{ asset('storage/' . $t->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-4xl">📖</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-gray-400 mb-1">{{ $t->tanggal_mengunggah->format('d M Y') }}</p>
                            <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 group-hover:text-emerald-600 transition">
                                {{ $t->judul }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- BACK --}}
    <div class="mt-10 text-center">
        <a href="{{ route('user.edukasi.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Edukasi
        </a>
    </div>
</div>

@endsection