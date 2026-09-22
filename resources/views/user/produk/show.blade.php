@extends('layouts.user')
@section('title', $produk->nama_produk)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('user.produk.index') }}" class="hover:text-emerald-600">Produk</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">{{ $produk->nama_produk }}</span>
    </nav>

    {{-- DETAIL --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">

        {{-- Image --}}
        <div>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden aspect-square">
                @if ($produk->gambar)
                    <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-9xl">🥛</div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div>
            <span class="inline-block text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full mb-3">
                {{ $produk->kategori->nama_kategori ?? '-' }}
            </span>

            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-3">{{ $produk->nama_produk }}</h1>

            <div class="flex items-center gap-4 mb-5">
                <div class="flex items-center gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    @endfor
                    <span class="text-sm text-gray-500 ml-1">(4.9)</span>
                </div>
                <span class="text-sm text-gray-300">|</span>
                <span class="text-sm text-gray-500">Stok: <span class="font-semibold text-gray-800">{{ $produk->stok }}</span></span>
            </div>

            <p class="text-4xl font-bold text-emerald-600 mb-6">
                Rp {{ number_format($produk->harga, 0, ',', '.') }}
            </p>

            {{-- Description --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Deskripsi Produk</h3>
                <p class="text-gray-600 leading-relaxed">{{ $produk->deskripsi ?? 'Tahu premium berkualitas tinggi, dibuat dari kedelai pilihan.' }}</p>
            </div>

            {{-- Qty + Add --}}
            <div class="flex flex-wrap gap-3 mb-6">
                <div class="flex items-center bg-gray-100 rounded-xl" x-data="{ qty: 1 }">
                    <button @click="if(qty > 1) qty--" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-emerald-600">−</button>
                    <span class="w-12 text-center font-semibold" x-text="qty"></span>
                    <button @click="if(qty < {{ $produk->stok }}) qty++" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-emerald-600">+</button>
                </div>

                <button type="button"
                        onclick="addToCartQty('produk', {{ $produk->id }}, document.querySelector('[x-text=qty]').textContent)"
                        class="flex-1 min-w-[200px] flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Tambah ke Keranjang
                </button>
            </div>

            {{-- Trust Badges --}}
            <div class="grid grid-cols-3 gap-3 pt-6 border-t border-gray-100">
                <div class="text-center">
                    <div class="text-2xl mb-1">🚚</div>
                    <p class="text-xs text-gray-500">Ongkir per km</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-1">🌿</div>
                    <p class="text-xs text-gray-500">100% Organik</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-1">✅</div>
                    <p class="text-xs text-gray-500">Halal & BPOM</p>
                </div>
            </div>
        </div>
    </div>

    {{-- RELATED --}}
    @if ($related->count() > 0)
    <section>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Produk Serupa</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach ($related as $r)
                <a href="{{ route('user.produk.show', $r->slug) }}"
                   class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                    <div class="aspect-square bg-gray-50 overflow-hidden">
                        @if ($r->gambar)
                            <img src="{{ asset('storage/' . $r->gambar) }}" alt="{{ $r->nama_produk }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">🥛</div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 min-h-[40px] mb-1">{{ $r->nama_produk }}</h3>
                        <p class="font-bold text-emerald-600 text-sm">Rp {{ number_format($r->harga, 0, ',', '.') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function addToCartQty(type, id, qty) {
        fetch('{{ route('user.cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ type, id, qty: parseInt(qty) })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast(d.message || 'Ditambahkan!');
                setTimeout(() => location.href = '{{ route('user.cart.index') }}', 600);
            } else showToast(d.message || 'Gagal', 'error');
        });
    }

    function showToast(msg, type = 'success') {
        const bg = type === 'success' ? 'bg-emerald-600' : 'bg-red-500';
        const t = document.createElement('div');
        t.className = `fixed top-20 right-4 z-50 ${bg} text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium`;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 2500);
    }
</script>
@endpush