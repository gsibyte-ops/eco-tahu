@extends('layouts.user')

@section('title', 'Beranda')
@section('content')

{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-emerald-50 via-white to-amber-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid md:grid-cols-2 gap-12 items-center">

            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold mb-4">
                    🌿 100% Organik & Ramah Lingkungan
                </span>

                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-800 leading-tight mb-4">
                    Tahu Sehat,
                    <span class="text-emerald-600">Lingkungan Kuat</span>
                </h1>

                <p class="text-gray-600 leading-relaxed mb-8">
                    Nikmati kelezatan tahu premium hasil pengolahan modern yang minim limbah.
                    Sehat untuk keluarga, aman bagi bumi.
                </p>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user.produk.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
                        Mulai Belanja
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="#kategori"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 hover:border-emerald-300 text-gray-700 font-semibold rounded-xl transition">
                        Pelajari Dampak Lingkungan
                    </a>
                </div>

                {{-- Stats --}}
                <div class="flex gap-8 mt-10">
                    <div>
                        <p class="text-2xl font-bold text-emerald-600">12k+</p>
                        <p class="text-xs text-gray-500">Pelanggan Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-emerald-600">4.9/5</p>
                        <p class="text-xs text-gray-500">Rating Kepuasan</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-emerald-600">100%</p>
                        <p class="text-xs text-gray-500">Halal & BPOM</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-emerald-200 to-amber-100 rounded-full blur-3xl opacity-40"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden">
                    <div class="aspect-square bg-gradient-to-br from-emerald-100 to-amber-50 flex items-center justify-center p-8">
                        <div class="text-center">
                            <div class="text-8xl mb-4">🥛</div>
                            <p class="text-2xl font-bold text-gray-800">Tahu Premium</p>
                            <p class="text-sm text-gray-500 mt-1">Sutra & Organik</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- KATEGORI --}}
<section id="kategori" class="py-16 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Kategori Produk</h2>
            <p class="text-gray-500">Pilih berbagai varian tahu segar berkualitas tinggi sesuai kebutuhan.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $emojiMap = ['🍚', '🍳', '🥛', '🥟'];
                $bgMap = ['bg-emerald-50', 'bg-amber-50', 'bg-sky-50', 'bg-rose-50'];
            @endphp
            @forelse ($kategori as $i => $k)
                <a href="{{ route('user.produk.index', ['kategori' => $k->id]) }}"
                   class="group {{ $bgMap[$i % 4] }} rounded-2xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition">
                    <div class="text-5xl mb-3">{{ $emojiMap[$i % 4] }}</div>
                    <p class="font-semibold text-gray-800 group-hover:text-emerald-600">{{ $k->nama_kategori }}</p>
                </a>
            @empty
                <p class="col-span-4 text-center text-gray-400">Belum ada kategori</p>
            @endforelse
        </div>
    </div>
</section>

{{-- PRODUK PILIHAN --}}
<section class="py-16 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Produk Pilihan</h2>
                <p class="text-gray-500">Produk terlaris yang paling banyak dicintai pelanggan kami.</p>
            </div>
            <a href="{{ route('user.produk.index') }}" class="hidden md:inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($produkPilihan as $p)
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                    <a href="{{ route('user.produk.show', $p->slug) }}" class="block">
                        <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                            @if ($p->gambar)
                                <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="text-6xl">🥛</div>
                            @endif
                        </div>
                    </a>
                    <div class="p-4">
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                            {{ $p->kategori->nama_kategori ?? '-' }}
                        </span>
                        <h3 class="font-semibold text-gray-800 mt-2 mb-1 line-clamp-2 min-h-[44px]">
                            {{ $p->nama_produk }}
                        </h3>
                        <p class="text-lg font-bold text-emerald-600 mb-3">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>

                        <button type="button"
                                onclick="addToCart('produk', {{ $p->id }})"
                                class="w-full flex items-center justify-center gap-2 py-2 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-xl text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            @empty
                <p class="col-span-4 text-center text-gray-400 py-8">Belum ada produk</p>
            @endforelse
        </div>
    </div>
</section>

{{-- PROMO BANNER --}}
<section class="py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="relative bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-3xl overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-24 -translate-x-24"></div>

            <div class="relative p-8 lg:p-12 grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-3xl lg:text-4xl font-bold text-white mb-4 leading-tight">
                        Dapatkan Promo Menarik
                        <br>Untuk Pembelian Pertama
                    </h3>
                    <p class="text-emerald-100 mb-6">
                        Daftarkan email Anda dan dapatkan diskon 20% serta informasi seputar pola makan sehat berkelanjutan.
                    </p>

                    <form class="flex flex-wrap gap-3">
                        <input type="email" placeholder="Alamat Email Anda"
                               class="flex-1 min-w-[200px] px-4 py-3 rounded-xl bg-white/95 border-0 focus:outline-none focus:ring-2 focus:ring-white text-sm">
                        <button type="submit"
                                class="px-6 py-3 bg-white text-emerald-700 font-semibold rounded-xl hover:bg-amber-50 transition whitespace-nowrap">
                            Langganan Sekarang
                        </button>
                    </form>
                </div>

                <div class="hidden md:flex justify-end">
                    <div class="w-56 h-56 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <div class="text-center text-white">
                            <p class="text-6xl font-black">20%</p>
                            <p class="text-sm font-semibold tracking-wider">OFF TODAY</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- LIMBAH / AMPAS TAHU --}}
@if ($limbahPilihan->count() > 0)
<section class="py-16 px-4 sm:px-6 lg:px-8 bg-amber-50/50">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
            <span class="inline-block px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold mb-3">
                ♻️ Zero Waste Movement
            </span>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Ampas Tahu Bermanfaat</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Kami mengubah limbah ampas tahu menjadi produk bernilai untuk pakan ternak, pupuk, dan olahan pangan.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($limbahPilihan as $l)
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                    <a href="{{ route('user.limbah.show', $l->slug) }}" class="block">
                        <div class="aspect-square bg-amber-50 flex items-center justify-center overflow-hidden">
                            @if ($l->gambar)
                                <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_limbah }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="text-6xl">🌾</div>
                            @endif
                        </div>
                    </a>
                    <div class="p-4">
                        <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded">
                            {{ $l->kategori->nama_kategori ?? 'Limbah' }}
                        </span>
                        <h3 class="font-semibold text-gray-800 mt-2 mb-1 line-clamp-2 min-h-[44px]">
                            {{ $l->nama_limbah }}
                        </h3>
                        <p class="text-lg font-bold text-amber-600 mb-3">
                            Rp {{ number_format($l->harga, 0, ',', '.') }}
                            <span class="text-xs font-normal text-gray-500">/ {{ $l->satuan }}</span>
                        </p>

                        <button type="button"
                                onclick="addToCart('limbah', {{ $l->id }})"
                                class="w-full flex items-center justify-center gap-2 py-2 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 rounded-xl text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- EDUKASI --}}
<section class="py-16 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Edukasi Lingkungan</h2>
                <p class="text-gray-500">Belajar bareng tentang pelestarian lingkungan & gaya hidup berkelanjutan.</p>
            </div>
            <a href="{{ route('user.edukasi.index') }}" class="hidden md:inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse ($edukasi as $e)
                <a href="{{ route('user.edukasi.show', $e->slug) }}"
                   class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                    <div class="aspect-video bg-emerald-50 flex items-center justify-center overflow-hidden">
                        @if ($e->thumbnail)
                            <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="text-6xl">📖</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="text-xs text-gray-400 mb-2">{{ $e->tanggal_mengunggah->format('d M Y') }}</p>
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-emerald-600 transition">
                            {{ $e->judul }}
                        </h3>
                        <p class="text-sm text-gray-500 line-clamp-2">
                            {{ Str::limit(strip_tags($e->konten), 100) }}
                        </p>
                    </div>
                </a>
            @empty
                <p class="col-span-3 text-center text-gray-400 py-8">Belum ada artikel</p>
            @endforelse
        </div>
    </div>
</section>

@endsection

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
        const bg = type === 'success' ? 'bg-emerald-600' : 'bg-red-500';
        const toast = document.createElement('div');
        toast.className = `fixed top-20 right-4 z-50 ${bg} text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium transition-all duration-300`;
        toast.style.transform = 'translateX(400px)';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 50);
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }
</script>
@endpush