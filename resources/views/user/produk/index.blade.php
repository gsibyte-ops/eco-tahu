@extends('layouts.user')
@section('title', 'Katalog Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-emerald-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <span class="font-medium" style="color: rgb(var(--text-primary));">Produk Tahu</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Katalog Produk Tahu</h1>
        <p class="mt-2" style="color: rgb(var(--text-secondary));">Temukan berbagai varian tahu segar berkualitas tinggi untuk keluarga Anda.</p>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[270px_minmax(0,1fr)]">

        <aside class="glass-card p-5 h-fit xl:sticky xl:top-24">
            <div class="mb-5">
                <h3 class="text-lg font-bold" style="color: rgb(var(--text-primary));">Filter</h3>
            </div>

            <form method="GET" class="space-y-5">
                <div>
                    <label class="mb-2 block text-[11px] font-bold uppercase tracking-wide" style="color: rgb(var(--text-muted));">Cari Produk</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tahu..."
                               class="glass-input w-full py-2.5 pl-9 pr-3 text-sm" style="color: rgb(var(--text-primary));">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-[11px] font-bold uppercase tracking-wide" style="color: rgb(var(--text-muted));">Kategori</label>
                    <div class="space-y-1">
                        <a href="{{ route('user.produk.index', array_merge(request()->except('kategori', 'page'), [])) }}"
                           class="block rounded-lg px-3 py-2 text-sm transition {{ !request('kategori') ? 'bg-emerald-500/15 font-semibold border' : 'hover:bg-emerald-500/5' }}"
                           style="{{ !request('kategori') ? 'color: rgb(var(--brand-hover)); border-color: rgb(var(--brand) / 0.3);' : 'color: rgb(var(--text-secondary));' }}">
                            Semua Kategori
                        </a>
                        @foreach ($kategori as $k)
                            <a href="{{ route('user.produk.index', array_merge(request()->except('page'), ['kategori' => $k->id])) }}"
                               class="block rounded-lg px-3 py-2 text-sm transition {{ request('kategori') == $k->id ? 'bg-emerald-500/15 font-semibold border' : 'hover:bg-emerald-500/5' }}"
                               style="{{ request('kategori') == $k->id ? 'color: rgb(var(--brand-hover)); border-color: rgb(var(--brand) / 0.3);' : 'color: rgb(var(--text-secondary));' }}">
                                {{ $k->nama_kategori }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-[11px] font-bold uppercase tracking-wide" style="color: rgb(var(--text-muted));">Urutkan</label>
                    <select name="sort" onchange="this.form.submit()"
                            class="glass-input w-full px-3 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                        <option value="">Terbaru</option>
                        <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                        <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
                    </select>
                </div>

                <button type="submit" class="glass-btn-primary w-full py-3">
                    Terapkan Filter
                </button>
            </form>
        </aside>

        <div class="space-y-5">
            <div class="glass-card inline-flex items-center px-4 py-2">
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Menampilkan <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">{{ $produk->total() }}</span> produk</p>
            </div>

            @if ($produk->count() > 0)
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($produk as $p)
                        <div class="surface product-card flex flex-col rounded-2xl overflow-hidden">
                            <a href="{{ route('user.produk.show', $p->slug) }}" class="block">
                                <div class="flex aspect-square items-center justify-center overflow-hidden" style="background: rgb(var(--bg-secondary));">
                                    @if ($p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="text-5xl">🥛</div>
                                    @endif
                                </div>
                            </a>

                            <div class="p-4 flex flex-col flex-1">
                                <p class="font-bold text-sm line-clamp-2 min-h-[40px] mb-1 text-center" style="color: rgb(var(--text-primary));">{{ $p->nama_produk }}</p>
                                <p class="text-xs text-center mb-3" style="color: rgb(var(--text-muted));">{{ $p->kategori->nama_kategori ?? '-' }}</p>

                                @php
                                    $avg = $p->averageRating();
                                    $cnt = $p->reviewCount();
                                @endphp
                                <div class="flex items-center justify-center gap-1 text-xs mb-3">
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

                                <p class="text-center text-lg price mb-3 mt-auto">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>

                                <button type="button" onclick="addToCart('produk', {{ $p->id }})"
                                        class="btn-primary w-full !text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">{{ $produk->links() }}</div>
            @else
                <div class="glass-card p-12 text-center">
                    <div class="mb-4 text-5xl">🔍</div>
                    <h3 class="text-xl font-bold" style="color: rgb(var(--text-primary));">Produk tidak ditemukan</h3>
                    <p class="mt-2 text-sm" style="color: rgb(var(--text-secondary));">Coba ubah filter atau kata kunci pencarian.</p>
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
            ? 'linear-gradient(135deg, rgba(16,185,129,0.95), rgba(5,150,105,0.95))'
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