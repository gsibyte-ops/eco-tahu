@extends('layouts.user')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Keranjang</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Keranjang Belanja</h1>
        <p class="text-gray-500">Cek kembali produk pilihanmu sebelum checkout.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LIST ITEMS --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach ($cart as $key => $item)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex gap-4" id="item-{{ $key }}">
                        <div class="w-20 h-20 rounded-xl bg-gray-50 flex-shrink-0 overflow-hidden">
                            @if ($item['gambar'])
                                <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama'] }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl">
                                    {{ $item['type'] === 'produk' ? '🥛' : '🌾' }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2">
                                <div class="min-w-0">
                                    <span class="text-xs font-medium {{ $item['type'] === 'produk' ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50' }} px-2 py-0.5 rounded">
                                        {{ $item['type'] === 'produk' ? 'Produk Tahu' : 'Limbah' }}
                                    </span>
                                    <h3 class="font-semibold text-gray-800 mt-1 truncate">{{ $item['nama'] }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                        @if ($item['satuan']) / {{ $item['satuan'] }} @endif
                                    </p>
                                </div>

                                <button type="button" onclick="removeItem('{{ $key }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center bg-gray-100 rounded-lg">
                                    <button type="button" onclick="updateQty('{{ $key }}', -1)"
                                            class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-emerald-600">−</button>
                                    <span class="w-10 text-center font-semibold text-sm" id="qty-{{ $key }}">{{ $item['qty'] }}</span>
                                    <button type="button" onclick="updateQty('{{ $key }}', 1)"
                                            class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-emerald-600">+</button>
                                </div>

                                <p class="font-bold text-gray-800" id="subtotal-{{ $key }}">
                                    Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between items-center pt-3">
                    <a href="{{ route('user.produk.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Lanjut Belanja
                    </a>
                    <form method="POST" action="{{ route('user.cart.clear') }}" onsubmit="return confirm('Kosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700">
                            Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Belanja</h3>

                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Item</span>
                            <span class="font-semibold text-gray-800" id="totalItems">
                                {{ collect($cart)->sum('qty') }} item
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800" id="subtotalDisplay">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4 text-xs text-amber-800">
                        ⚠️ Minimal pembelian <strong>Rp 50.000</strong> (belum termasuk ongkir)
                    </div>

                    @if ($subtotal < 50000)
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4 text-xs text-red-700">
                            Belum mencapai minimal pembelian. Tambah <strong>Rp {{ number_format(50000 - $subtotal, 0, ',', '.') }}</strong> lagi.
                        </div>
                    @endif

                    <a href="{{ route('user.checkout.index') }}"
                       class="block w-full text-center py-3 {{ $subtotal >= 50000 ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-300 cursor-not-allowed' }} text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
                        Lanjut ke Checkout
                    </a>

                    <p class="text-xs text-gray-400 text-center mt-3">
                        Ongkir dihitung otomatis per km saat checkout.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- EMPTY STATE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center max-w-md mx-auto">
            <div class="text-7xl mb-4">🛒</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Keranjang Kosong</h2>
            <p class="text-sm text-gray-500 mb-6">Yuk mulai belanja tahu segar dan ampas tahu bermanfaat!</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('user.produk.index') }}"
                   class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition">
                    Belanja Tahu
                </a>
                <a href="{{ route('user.limbah.index') }}"
                   class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition">
                    Belanja Limbah
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function updateQty(key, delta) {
        const el = document.getElementById('qty-' + key);
        const newQty = parseInt(el.textContent) + delta;

        if (newQty < 1) return;

        fetch('{{ route('user.cart.update') }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ key: key, qty: newQty })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                el.textContent = newQty;
                document.getElementById('subtotal-' + key).textContent = formatRupiah(d.item_subtotal);
                document.getElementById('subtotalDisplay').textContent = formatRupiah(d.subtotal);

                // Update total items
                let totalItems = 0;
                document.querySelectorAll('[id^="qty-"]').forEach(q => totalItems += parseInt(q.textContent));
                document.getElementById('totalItems').textContent = totalItems + ' item';

                // Reload kalau subtotal di bawah minimal, biar alert update
                location.reload();
            } else {
                alert(d.message);
            }
        });
    }

    function removeItem(key) {
        if (!confirm('Hapus item ini dari keranjang?')) return;

        fetch('{{ route('user.cart.remove') }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ key: key })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('item-' + key).remove();
                location.reload();
            }
        });
    }

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }
</script>
@endpush