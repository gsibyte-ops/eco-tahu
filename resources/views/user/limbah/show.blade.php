@extends('layouts.user')
@section('title', $limbah->nama_limbah)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-amber-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--accent));">/</span>
        <a href="{{ route('user.limbah.index') }}" class="hover:text-amber-500 transition">Limbah</a>
        <span class="mx-2" style="color: rgb(var(--accent));">/</span>
        <span class="font-medium" style="color: rgb(var(--text-primary));">{{ Str::limit($limbah->nama_limbah, 40) }}</span>
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
            <span class="pill-accent">{{ $limbah->kategori->nama_kategori ?? 'Limbah' }}</span>

            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-3 mt-4" style="color: rgb(var(--text-primary));">{{ $limbah->nama_limbah }}</h1>

            @php
                $avgRating = $limbah->averageRating();
                $totalReview = $limbah->reviewCount();
            @endphp

            <div class="flex items-center gap-4 mb-5">
                <div class="flex items-center gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-amber-400 fill-current' : 'fill-current' }}"
                             style="{{ $i > round($avgRating) ? 'color: rgb(var(--text-faint));' : '' }}"
                             viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    @endfor
                    <span class="text-sm ml-1 tabular-nums" style="color: rgb(var(--text-secondary));">
                        @if ($totalReview > 0) ({{ number_format($avgRating, 1) }} · {{ $totalReview }} ulasan)
                        @else (Belum ada ulasan) @endif
                    </span>
                </div>
                <span style="color: rgb(var(--text-faint));">|</span>
                <span class="text-sm" style="color: rgb(var(--text-secondary));">Stok: <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">{{ $limbah->stok }} {{ $limbah->satuan }}</span></span>
            </div>

            <p class="text-4xl price mb-6" style="color: rgb(var(--accent-hover));">
                Rp {{ number_format($limbah->harga, 0, ',', '.') }}
                <span class="text-base font-normal" style="color: rgb(var(--text-muted));">/ {{ $limbah->satuan }}</span>
            </p>

            <div class="mb-6">
                <h3 class="font-semibold mb-2" style="color: rgb(var(--text-primary));">Deskripsi</h3>
                <p class="leading-relaxed" style="color: rgb(var(--text-secondary));">{{ $limbah->deskripsi ?? 'Ampas tahu berkualitas untuk berbagai keperluan.' }}</p>
            </div>

            <div class="flex flex-wrap gap-3 mb-6" x-data="{ qty: 1 }">
                <div class="glass-input flex items-center">
                    <button @click="if(qty > 1) qty--" class="w-10 h-10 flex items-center justify-center text-lg font-bold" style="color: rgb(var(--text-secondary));">−</button>
                    <span class="w-12 text-center font-semibold tabular-nums" style="color: rgb(var(--text-primary));" x-text="qty"></span>
                    <button @click="if(qty < {{ $limbah->stok }}) qty++" class="w-10 h-10 flex items-center justify-center text-lg font-bold" style="color: rgb(var(--text-secondary));">+</button>
                </div>

                <button type="button"
                        @click="addToCartQty('limbah', {{ $limbah->id }}, qty)"
                        class="glass-btn-amber flex-1 min-w-[200px] py-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Tambah ke Keranjang
                </button>
            </div>

            <div class="glass-amber rounded-xl p-4 text-sm" style="color: rgb(var(--accent));">
                ♻️ Setiap pembelian ampas tahu membantu mengurangi limbah industri tahu.
            </div>
        </div>
    </div>

    @include('user.partials.review-section', ['item' => $limbah, 'itemType' => 'limbah'])

    @if ($related->count() > 0)
    <section class="mt-16">
        <h2 class="text-2xl font-extrabold tracking-tight mb-6" style="color: rgb(var(--text-primary));">Produk Serupa</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach ($related as $r)
                <a href="{{ route('user.limbah.show', $r->slug) }}" class="surface limbah-card overflow-hidden block">
                    <div class="aspect-square overflow-hidden" style="background: rgb(var(--bg-secondary));">
                        @if ($r->gambar)
                            <img src="{{ asset('storage/' . $r->gambar) }}" alt="{{ $r->nama_limbah }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">🌾</div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-sm line-clamp-2 min-h-[40px] mb-1" style="color: rgb(var(--text-primary));">{{ $r->nama_limbah }}</h3>
                        <p class="price text-sm" style="color: rgb(var(--accent-hover));">Rp {{ number_format($r->harga, 0, ',', '.') }}</p>
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
                if (d.cart_count !== undefined && typeof window.setCartBadge === 'function') {
                    window.setCartBadge(d.cart_count);
                }
                setTimeout(() => location.href = '{{ route('user.cart.index') }}', 700);
            } else showToast(d.message || 'Gagal', 'error');
        })
        .catch(() => showToast('Error', 'error'));
    }

    function showToast(msg, type = 'success') {
        const bg = type === 'success'
            ? 'linear-gradient(135deg, rgba(245,158,11,0.95), rgba(217,119,6,0.95))'
            : 'linear-gradient(135deg, rgba(239,68,68,0.95), rgba(220,38,38,0.95))';
        const t = document.createElement('div');
        t.className = 'fixed top-20 right-4 z-50 text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold';
        t.style.background = bg;
        t.style.backdropFilter = 'blur(12px)';
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