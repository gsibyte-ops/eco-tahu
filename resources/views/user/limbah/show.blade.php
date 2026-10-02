@extends('layouts.user')
@section('title', $limbah->nama_limbah)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition">Beranda</a>
        <span class="mx-2 text-amber-400">/</span>
        <a href="{{ route('user.limbah.index') }}" class="hover:text-amber-600 transition">Limbah</a>
        <span class="mx-2 text-amber-400">/</span>
        <span class="text-gray-800 font-medium">{{ Str::limit($limbah->nama_limbah, 40) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">
        <div>
            <div class="glass-amber rounded-3xl overflow-hidden aspect-square p-3">
                @if ($limbah->gambar)
                    <img src="{{ asset('storage/' . $limbah->gambar) }}" alt="{{ $limbah->nama_limbah }}" class="w-full h-full object-cover rounded-2xl">
                @else
                    <div class="w-full h-full flex items-center justify-center text-9xl">🌾</div>
                @endif
            </div>
        </div>

        <div class="glass-card p-8">
            <span class="inline-block text-xs font-semibold text-amber-700 bg-amber-500/15 border border-amber-300/40 px-3 py-1 rounded-full mb-3">
                {{ $limbah->kategori->nama_kategori ?? 'Limbah' }}
            </span>

            <h1 class="text-3xl lg:text-4xl font-display text-gray-800 mb-3">{{ $limbah->nama_limbah }}</h1>

            <p class="text-sm text-gray-500 mb-5">
                Stok: <span class="font-semibold text-gray-800 tabular-nums">{{ $limbah->stok }} {{ $limbah->satuan }}</span>
            </p>

            <p class="text-4xl price text-amber-600 mb-6">
                Rp {{ number_format($limbah->harga, 0, ',', '.') }}
                <span class="text-base font-normal text-gray-500">/ {{ $limbah->satuan }}</span>
            </p>

            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Deskripsi</h3>
                <p class="text-gray-600 leading-relaxed">{{ $limbah->deskripsi ?? 'Ampas tahu berkualitas untuk berbagai keperluan.' }}</p>
            </div>

            <div class="flex flex-wrap gap-3 mb-6" x-data="{ qty: 1 }">
                <div class="glass-input flex items-center">
                    <button @click="if(qty > 1) qty--" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-amber-600 text-lg font-bold">−</button>
                    <span class="w-12 text-center font-semibold tabular-nums text-gray-800" x-text="qty"></span>
                    <button @click="if(qty < {{ $limbah->stok }}) qty++" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-amber-600 text-lg font-bold">+</button>
                </div>

                <button type="button"
                        @click="fetch('{{ route('user.cart.add') }}', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                            body: JSON.stringify({type: 'limbah', id: {{ $limbah->id }}, qty: qty})
                        }).then(r => r.json()).then(d => { if(d.success) location.href = '{{ route('user.cart.index') }}'; else showToast(d.message, 'error'); })"
                        class="glass-btn-amber flex-1 min-w-[200px] py-3 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Tambah ke Keranjang
                </button>
            </div>

            <div class="glass-amber rounded-xl p-4 text-sm text-amber-800">
                ♻️ Setiap pembelian ampas tahu membantu mengurangi limbah industri tahu.
            </div>
        </div>
    </div>

    @if ($related->count() > 0)
    <section>
        <h2 class="text-2xl font-display text-gray-800 mb-6">Produk Serupa</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach ($related as $r)
                <a href="{{ route('user.limbah.show', $r->slug) }}"
                   class="glass-amber rounded-2xl overflow-hidden transition hover:-translate-y-1.5 hover:shadow-xl hover:shadow-amber-500/20 block">
                    <div class="aspect-square bg-amber-50/50 overflow-hidden">
                        @if ($r->gambar)
                            <img src="{{ asset('storage/' . $r->gambar) }}" alt="{{ $r->nama_limbah }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">🌾</div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 min-h-[40px] mb-1">{{ $r->nama_limbah }}</h3>
                        <p class="price text-amber-600 text-sm">Rp {{ number_format($r->harga, 0, ',', '.') }}</p>
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
    function showToast(msg, type = 'success') {
        const bg = type === 'success'
            ? 'linear-gradient(135deg, rgba(245,158,11,0.95), rgba(217,119,6,0.95))'
            : 'linear-gradient(135deg, rgba(239,68,68,0.95), rgba(220,38,38,0.95))';
        const t = document.createElement('div');
        t.className = 'fixed top-20 right-4 z-50 text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium';
        t.style.background = bg;
        t.style.backdropFilter = 'blur(12px)';
        t.style.border = '1px solid rgba(255,255,255,0.25)';
        t.style.transform = 'translateX(400px)';
        t.style.transition = 'all 0.3s cubic-bezier(0.4,0,0.2,1)';
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.style.transform = 'translateX(0)', 50);
        setTimeout(() => {
            t.style.transform = 'translateX(400px)';
            setTimeout(() => t.remove(), 300);
        }, 2500);
    }
</script>
@endpush