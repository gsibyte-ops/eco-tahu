@extends('layouts.user')
@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('user.cart.index') }}" class="hover:text-emerald-600">Keranjang</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Checkout</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Checkout</h1>
        <p class="text-gray-500">Isi data pengiriman dan pilih metode pembayaran yang paling nyaman.</p>
    </div>

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-5 bg-red-50 border border-red-300 text-red-800 px-4 py-4 rounded-xl text-sm">
            <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('user.checkout.store') }}"
          x-data="checkoutForm()"
          data-jarak="{{ old('jarak_km', 1) }}"
          data-payment="{{ old('payment_method', 'COD') }}"
          data-delivery="{{ old('delivery_type', 'delivery') }}"
          data-subtotal="{{ $subtotal }}"
          data-ongkir-per-km="{{ $ongkirPerKm }}"
          data-minimal-ongkir="{{ $minimalOngkir }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-5">

                {{-- 1. Data Penerima --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        Data Penerima
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_penerima" required value="{{ old('nama_penerima', auth()->user()->username) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telepon" required value="{{ old('no_telepon', auth()->user()->no_telepon) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="alamat_pengiriman" required rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Jalan, No. Rumah, Kelurahan, Kecamatan, Kota... (min. 10 karakter)">{{ old('alamat_pengiriman', auth()->user()->alamat) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm" placeholder="Contoh: Tahu dipisah dari ampas, dsb">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 2. Metode Pembayaran --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        Metode Pembayaran
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="COD" x-model="payment" class="peer sr-only">
                            <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 transition">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="text-3xl">💵</div>
                                    <div>
                                        <p class="font-bold text-gray-800">COD</p>
                                        <p class="text-xs text-gray-500">Bayar saat barang datang</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">Bayar tunai ke kurir saat barang tiba di alamat.</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Transfer" x-model="payment" class="peer sr-only">
                            <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 transition">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="text-3xl">🏦</div>
                                    <div>
                                        <p class="font-bold text-gray-800">Transfer Bank</p>
                                        <p class="text-xs text-gray-500">Dapat nomor Virtual Account</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">Pilih bank, transfer ke VA yang tersedia.</p>
                            </div>
                        </label>
                    </div>

                    {{-- PILIH TIPE PENGIRIMAN (SELALU MUNCUL — COD & Transfer) --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Pilih Tipe Pengiriman:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="delivery_type" value="pickup" x-model="delivery" class="peer sr-only">
                                <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="text-2xl">🏪</div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Ambil di Tempat</p>
                                            <p class="text-xs text-emerald-600 font-semibold">GRATIS ONGKIR</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Ambil langsung di toko EcoTahu, tanpa biaya ongkir.</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="delivery_type" value="delivery" x-model="delivery" class="peer sr-only">
                                <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="text-2xl">🚚</div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Diantar</p>
                                            <p class="text-xs text-amber-600 font-semibold">Mulai Rp 5.000</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Diantar ke alamat Anda, ongkir sesuai jarak.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Bank Picker (kalau Transfer) --}}
                    <div x-show="payment === 'Transfer'" x-cloak class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Pilih Bank Tujuan:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @foreach (['BCA', 'BRI', 'Mandiri', 'BNI'] as $bank)
                                <label class="cursor-pointer">
                                    <input type="radio" name="bank_tujuan" value="{{ $bank }}" x-model="bank" class="peer sr-only">
                                    <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl px-3 py-3 text-center transition">
                                        <p class="font-bold text-sm text-gray-800">{{ $bank }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-800">
                            ⏰ Selesaikan pembayaran dalam <strong>24 jam</strong>, atau pesanan otomatis dibatalkan.
                        </div>
                    </div>
                </div>

                {{-- 3. Jarak Pengiriman (cuma kalau delivery) --}}
                <div x-show="butuhOngkir" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                        Jarak Pengiriman
                    </h3>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4 text-sm text-amber-800">
                        📍 Ongkir <strong>Rp 2.500 per kilometer</strong> (minimum Rp 5.000) dari lokasi toko EcoTahu.
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jarak dari Toko (km) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="jarak_km" x-bind:required="butuhOngkir" min="1" max="100" step="0.1" x-model="jarak" value="{{ old('jarak_km', 1) }}" class="w-full px-4 py-2.5 pr-16 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">km</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">Estimasi jarak pengiriman dari toko ke alamat Anda (min. 1 km).</p>
                    </div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto mb-4 pb-4 border-b border-gray-100">
                        @foreach ($cart as $item)
                            <div class="flex gap-3">
                                <div class="w-12 h-12 rounded-lg bg-gray-50 flex-shrink-0 overflow-hidden border border-gray-100">
                                    @if ($item['gambar'])
                                        <img src="{{ asset('storage/' . $item['gambar']) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xl">{{ $item['type'] === 'produk' ? '🥛' : '🌾' }}</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item['nama'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['qty'] }} × Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">
                                Ongkir
                                <span x-show="!butuhOngkir" class="text-xs text-emerald-600">(Ambil di Tempat)</span>
                                <span x-show="butuhOngkir" class="text-xs">(<span x-text="jarak"></span> km)</span>
                            </span>
                            <span class="font-semibold text-gray-800">Rp <span x-text="formatNumber(ongkir)"></span></span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t-2 border-gray-100 mb-5">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="text-xl font-bold text-emerald-600">Rp <span x-text="formatNumber(total)"></span></span>
                    </div>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
                        Buat Pesanan
                    </button>

                    <p class="text-xs text-gray-400 text-center mt-3">Dengan klik "Buat Pesanan", Anda setuju dengan syarat & ketentuan kami.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function checkoutForm() {
        const form = document.querySelector('form[x-data]');
        return {
            jarak: parseFloat(form.dataset.jarak) || 1,
            payment: form.dataset.payment || 'COD',
            delivery: form.dataset.delivery || 'delivery',
            bank: null,
            subtotal: parseFloat(form.dataset.subtotal) || 0,
            ongkirPerKm: parseFloat(form.dataset.ongkirPerKm) || 2500,
            minimalOngkir: parseFloat(form.dataset.minimalOngkir) || 5000,

            get butuhOngkir() {
                return this.delivery === 'delivery';
            },
            get ongkir() {
                if (!this.butuhOngkir) return 0;
                const hitung = (parseFloat(this.jarak) || 0) * this.ongkirPerKm;
                return Math.max(hitung, this.minimalOngkir);
            },
            get total() { return this.subtotal + this.ongkir; },
            formatNumber(num) { return Number(Math.round(num)).toLocaleString('id-ID'); }
        }
    }
</script>
@endpush