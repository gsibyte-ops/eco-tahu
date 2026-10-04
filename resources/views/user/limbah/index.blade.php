@extends('layouts.user')
@section('title', 'Limbah Ampas Tahu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-amber-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--accent));">/</span>
        <span class="font-medium" style="color: rgb(var(--text-primary));">Limbah Ampas Tahu</span>
    </nav>

    <div class="glass-amber rounded-2xl p-5 mb-8 flex items-start gap-4">
        <div class="text-3xl">♻️</div>
        <div>
            <h2 class="font-bold mb-1" style="color: rgb(var(--accent));">Zero Waste Movement</h2>
            <p class="text-sm" style="color: rgb(var(--text-secondary));">Membeli ampas tahu = ikut mengurangi limbah industri & mendukung ekonomi sirkular. Cocok untuk pakan ternak, pupuk, dan olahan pangan.</p>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-4xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Katalog Ampas Tahu</h1>
        <p class="mt-2" style="color: rgb(var(--text-secondary));">Produk limbah bernilai tinggi, ramah lingkungan, dan ekonomis.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <aside class="lg:col-span-1">
            <div class="glass-card p-5 sticky top-24">
                <h3 class="font-bold mb-4" style="color: rgb(var(--text-primary));">Filter</h3>
                <form method="GET">
                    <div class="mb-5">
                        <label class="block text-xs font-semibold uppercase mb-2 tracking-wide" style="color: rgb(var(--text-muted));">Cari</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari limbah..."
                               class="glass-input w-full px-3 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold uppercase mb-2 tracking-wide" style="color: rgb(var(--text-muted));">Kategori</label>
                        <div class="space-y-1">
                            <a href="{{ route('user.limbah.index', request()->except('kategori', 'page')) }}"
                               class="block px-3 py-2 rounded-lg text-sm transition {{ !request('kategori') ? 'bg-amber-500/15 font-semibold border' : 'hover:bg-amber-500/5' }}"
                               style="{{ !request('kategori') ? 'color: rgb(var(--accent-hover)); border-color: rgb(var(--accent) / 0.3);' : 'color: rgb(var(--text-secondary));' }}">
                                Semua
                            </a>
                            @foreach ($kategori as $k)
                                <a href="{{ route('user.limbah.index', array_merge(request()->except('page'), ['kategori' => $k->id])) }}"
                                   class="block px-3 py-2 rounded-lg text-sm transition {{ request('kategori') == $k->id ? 'bg-amber-500/15 font-semibold border' : 'hover:bg-amber-500/5' }}"
                                   style="{{ request('kategori') == $k->id ? 'color: rgb(var(--accent-hover)); border-color: rgb(var(--accent) / 0.3);' : 'color: rgb(var(--text-secondary));' }}">
                                    {{ $k->nama_kategori }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold uppercase mb-2 tracking-wide" style="color: rgb(var(--text-muted));">Urutkan</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="glass-input w-full px-3 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                            <option value="">Terbaru</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Termurah</option>
                            <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Termahal</option>
                        </select>
                    </div>

                    <button type="submit" class="glass-btn-amber w-full py-3 text-sm">Terapkan Filter</button>
                </form>
            </div>
        </aside>

        <div class="lg:col-span-3">
            <div class="glass-card inline-flex items-center px-4 py-2 mb-5">
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Menampilkan <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">{{ $limbah->total() }}</span> produk</p>
            </div>

            @if ($limbah->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($limbah as $l)
                        <div class="surface limbah-card flex flex-col rounded-2xl overflow-hidden">
                            <a href="{{ route('user.limbah.show', $l->slug) }}" class="block">
                                <div class="aspect-square overflow-hidden" style="background: rgb(var(--bg-secondary));">
                                    @if ($l->gambar)
                                        <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_limbah }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-6xl">🌾</div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4 flex flex-col flex-1">
                                <span class="pill-accent text-[10px] self-start mb-2">{{ $l->kategori->nama_kategori ?? 'Limbah' }}</span>
                                <h3 class="font-bold text-sm line-clamp-2 min-h-[44px] mb-2" style="color: rgb(var(--text-primary));">{{ $l->nama_limbah }}</h3>

                                @php
                                    $avg = $l->averageRating();
                                    $cnt = $l->reviewCount();
                                @endphp
                                <div class="flex items-center gap-1 text-xs mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= round($avg) ? 'text-amber-400 fill-current' : 'fill-current' }}"
                                             style="{{ $i > round($avg) ? 'color: rgb(var(--text-faint));' : '' }}"
                                             viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @endfor
                                    <span class="ml-1" style="color: rgb(var(--text-muted));">
                                        @if ($cnt > 0) {{ number_format($avg, 1) }} ({{ $cnt }})
                                        @else Baru @endif
                                    </span>
                                </div>

                                <p class="text-lg price mb-3 mt-auto" style="color: rgb(var(--accent-hover));">
                                    Rp {{ number_format($l->harga, 0, ',', '.') }}
                                    <span class="text-xs font-normal" style="color: rgb(var(--text-muted));">/ {{ $l->satuan }}</span>
                                </p>
                                <button type="button" onclick="addToCart('limbah', {{ $l->id }})"
                                        class="glass-btn-amber w-full !text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $limbah->links() }}</div>
            @else
                <div class="glass-card p-12 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="font-bold mb-2" style="color: rgb(var(--text-primary));">Belum ada limbah</h3>
                </div>
            @endif
        </div>
    </div>
</div>
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
            body: JSON.stringify({ type, id, qty: 1 })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast(d.message || 'Ditambahkan!');
                if (d.cart_count !== undefined && typeof window.setCartBadge === 'function') {
                    window.setCartBadge(d.cart_count);
                }
            } else {
                showToast(d.message || 'Gagal', 'error');
            }
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