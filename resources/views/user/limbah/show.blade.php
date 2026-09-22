@extends('layouts.user')
@section('title', $limbah->nama_limbah)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('user.limbah.index') }}" class="hover:text-emerald-600">Limbah</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">{{ $limbah->nama_limbah }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">
        <div>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden aspect-square">
                @if ($limbah->gambar)
                    <img src="{{ asset('storage/' . $limbah->gambar) }}" alt="{{ $limbah->nama_limbah }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-9xl">🌾</div>
                @endif
            </div>
        </div>

        <div>
            <span class="inline-block text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1 rounded-full mb-3">
                {{ $limbah->kategori->nama_kategori ?? 'Limbah' }}
            </span>

            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-3">{{ $limbah->nama_limbah }}</h1>

            <p class="text-sm text-gray-500 mb-5">
                Stok: <span class="font-semibold text-gray-800">{{ $limbah->stok }} {{ $limbah->satuan }}</span>
            </p>

            <p class="text-4xl font-bold text-amber-600 mb-6">
                Rp {{ number_format($limbah->harga, 0, ',', '.') }}
                <span class="text-base font-normal text-gray-500">/ {{ $limbah->satuan }}</span>
            </p>

            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Deskripsi</h3>
                <p class="text-gray-600 leading-relaxed">{{ $limbah->deskripsi ?? 'Ampas tahu berkualitas untuk berbagai keperluan.' }}</p>
            </div>

            <div class="flex flex-wrap gap-3 mb-6" x-data="{ qty: 1 }">
                <div class="flex items-center bg-gray-100 rounded-xl">
                    <button @click="if(qty > 1) qty--" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-amber-600">−</button>
                    <span class="w-12 text-center font-semibold" x-text="qty"></span>
                    <button @click="if(qty < {{ $limbah->stok }}) qty++" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-amber-600">+</button>
                </div>

                <button type="button"
                        @click="fetch('{{ route('user.cart.add') }}', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                            body: JSON.stringify({type: 'limbah', id: {{ $limbah->id }}, qty: qty})
                        }).then(r => r.json()).then(d => { if(d.success) location.href = '{{ route('user.cart.index') }}'; else alert(d.message); })"
                        class="flex-1 min-w-[200px] flex items-center justify-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl shadow-lg shadow-amber-200 transition">
                    Tambah ke Keranjang
                </button>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                ♻️ Setiap pembelian ampas tahu membantu mengurangi limbah industri tahu.
            </div>
        </div>
    </div>

    @if ($related->count() > 0)
    <section>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Produk Serupa</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach ($related as $r)
                <a href="{{ route('user.limbah.show', $r->slug) }}"
                   class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                    <div class="aspect-square bg-amber-50 overflow-hidden">
                        @if ($r->gambar)
                            <img src="{{ asset('storage/' . $r->gambar) }}" alt="{{ $r->nama_limbah }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">🌾</div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 min-h-[40px] mb-1">{{ $r->nama_limbah }}</h3>
                        <p class="font-bold text-amber-600 text-sm">Rp {{ number_format($r->harga, 0, ',', '.') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection