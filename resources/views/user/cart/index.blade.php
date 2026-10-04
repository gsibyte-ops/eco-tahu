@extends('layouts.user')
@section('title', 'Keranjang Belanja')

@section('content')
<div id="cartData"
     data-route-update="{{ route('user.cart.update') }}"
     data-route-remove="{{ route('user.cart.remove') }}"
     data-csrf="{{ csrf_token() }}"
     class="hidden"></div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Beranda</a>
        <span class="mx-2 text-emerald-400">/</span>
        <span class="text-gray-800 font-medium">Keranjang</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-display text-gray-800">Keranjang Belanja</h1>
        <p class="text-gray-500 mt-2">Cek kembali produk pilihanmu sebelum checkout.</p>
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-emerald-700 border-emerald-300/50">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-red-700 border-red-300/50">
            {{ session('error') }}
        </div>
    @endif

    @if (count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ITEM LIST --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach ($cart as $key => $item)
                    <div class="glass-card p-4 flex gap-4" id="item-{{ $key }}">
                        <div class="w-20 h-20 rounded-2xl bg-white/40 flex-shrink-0 overflow-hidden border border-white/60">
                            @if ($item['gambar'])
                                <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl">{{ $item['type'] === 'produk' ? '🥛' : '🌾' }}</div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2">
                                <div class="min-w-0">
                                    <span class="inline-block text-[10px] font-semibold uppercase tracking-wide
                                        {{ $item['type'] === 'produk'
                                            ? 'text-emerald-700 bg-emerald-500/15 border border-emerald-300/40'
                                            : 'text-amber-700 bg-amber-500/15 border border-amber-300/40' }}
                                        px-2 py-1 rounded-full">
                                        {{ $item['type'] === 'produk' ? 'Produk Tahu' : 'Limbah' }}
                                    </span>
                                    <h3 class="font-semibold text-gray-800 mt-2 truncate">{{ $item['nama'] }}</h3>
                                    <p class="text-sm text-gray-500 mt-1 tabular-nums">
                                        Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                        @if ($item['satuan']) / {{ $item['satuan'] }} @endif
                                    </p>
                                </div>

                                <button type="button" onclick="removeItem('{{ $key }}')"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-red-50/70 text-gray-400 hover:text-red-500 transition flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <div class="glass-input flex items-center overflow-hidden">
                                    <button type="button" onclick="updateQty('{{ $key }}', -1)" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:text-emerald-600 font-bold">−</button>
                                    <span class="w-10 text-center font-semibold text-sm tabular-nums text-gray-800" id="qty-{{ $key }}">{{ $item['qty'] }}</span>
                                    <button type="button" onclick="updateQty('{{ $key }}', 1)" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:text-emerald-600 font-bold">+</button>
                                </div>

                                <p class="price text-gray-800" id="subtotal-{{ $key }}">Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between items-center pt-2 flex-wrap gap-3">
                    <a href="{{ route('user.produk.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Lanjut Belanja
                    </a>
                    <form method="POST" action="{{ route('user.cart.clear') }}" onsubmit="return confirm('Kosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700">Kosongkan Keranjang</button>
                    </form>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="lg:col-span-1">
                <div class="glass-card p-5 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Belanja</h3>

                    <div class="space-y-2 mb-4 pb-4 border-b border-white/50">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Item</span>
                            <span class="font-semibold text-gray-800 tabular-nums" id="totalItems">{{ collect($cart)->sum('qty') }} item</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800 tabular-nums" id="subtotalDisplay">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('user.checkout.index') }}"
                       class="block w-full text-center py-3 glass-btn-primary font-semibold">
                        Lanjut ke Checkout
                    </a>

                    <p class="text-xs text-gray-400 text-center mt-3">
                        Ongkir & minimum pembelian dicek saat checkout.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="glass-card p-16 text-center max-w-md mx-auto">
            <div class="text-7xl mb-4">🛒</div>
            <h2 class="text-xl font-display text-gray-800 mb-2">Keranjang Kosong</h2>
            <p class="text-sm text-gray-500 mb-6">Yuk mulai belanja tahu segar dan ampas tahu bermanfaat!</p>
            <div class="flex gap-3 justify-center flex-wrap">
                <a href="{{ route('user.produk.index') }}" class="glass-btn-primary text-sm">Belanja Tahu</a>
                <a href="{{ route('user.limbah.index') }}" class="glass-btn-amber text-sm px-5 py-2.5">Belanja Limbah</a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const cartData = document.getElementById('cartData');
    const ROUTE_UPDATE = cartData.dataset.routeUpdate;
    const ROUTE_REMOVE = cartData.dataset.routeRemove;
    const CSRF_TOKEN = cartData.dataset.csrf;

    function updateQty(key, delta) {
        const el = document.getElementById('qty-' + key);
        const newQty = parseInt(el.textContent) + delta;
        if (newQty < 1) return;

        fetch(ROUTE_UPDATE, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ key: key, qty: newQty })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                location.reload();
            } else {
                showToast(d.message || 'Gagal', 'error');
            }
        });
    }

    function removeItem(key) {
        if (!confirm('Hapus item ini dari keranjang?')) return;

        fetch(ROUTE_REMOVE, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ key: key })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                location.reload();
            }
        });
    }

    function showToast(msg, type = 'success') {
        const bg = type === 'success'
            ? 'linear-gradient(135deg, rgba(16,185,129,0.95), rgba(5,150,105,0.95))'
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