@extends('layouts.user')
@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Beranda</a>
        <span class="mx-2 text-emerald-400">/</span>
        <a href="{{ route('user.cart.index') }}" class="hover:text-emerald-600 transition">Keranjang</a>
        <span class="mx-2 text-emerald-400">/</span>
        <span class="text-gray-800 font-medium">Checkout</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-display text-gray-800">Checkout</h1>
        <p class="text-gray-500 mt-2">Isi data pengiriman dan pilih metode pembayaran yang paling nyaman.</p>
    </div>

    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-red-700 border-red-300/50">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="glass rounded-xl px-4 py-4 mb-5 text-sm text-red-800 border-red-300/50">
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
          data-subtotal="{{ $subtotal }}"
          data-ongkir-per-km="{{ $toko['ongkir_per_km'] }}"
          data-ongkir-minimal="{{ $toko['ongkir_minimal'] }}"
          data-radius-maks="{{ $toko['radius_maks_km'] }}"
          data-lat-toko="{{ $toko['lat'] }}"
          data-lng-toko="{{ $toko['lng'] }}"
          data-kecamatan='@json($kecamatanList)'
          data-payment="{{ old('payment_method', 'COD') }}"
          data-delivery="{{ old('delivery_type', 'delivery') }}"
          data-kecamatan-old="{{ old('kecamatan') }}"
          data-minimal-delivery="{{ $minimalDelivery }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-5">

                {{-- 1. Data Penerima --}}
                <div class="glass-card p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-md shadow-emerald-500/30">1</span>
                        Data Penerima
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_penerima" required value="{{ old('nama_penerima', auth()->user()->username) }}" class="glass-input w-full px-4 py-2.5 text-sm text-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telepon" required value="{{ old('no_telepon', auth()->user()->no_telepon) }}" class="glass-input w-full px-4 py-2.5 text-sm text-gray-700" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="alamat_pengiriman" required rows="3" class="glass-input w-full px-4 py-2.5 text-sm text-gray-700" placeholder="Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Patokan... (min. 10 karakter)">{{ old('alamat_pengiriman', auth()->user()->alamat) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter. Detail alamat (patokan) membantu kurir menemukan lokasi.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="2" class="glass-input w-full px-4 py-2.5 text-sm text-gray-700" placeholder="Contoh: Tahu dipisah dari ampas, dsb">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 2. Metode Pembayaran --}}
                <div class="glass-card p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-md shadow-emerald-500/30">2</span>
                        Metode Pembayaran
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="COD" x-model="payment" class="peer sr-only">
                            <div class="glass-input border-2 border-white/70 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 rounded-xl p-4 transition">
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
                            <div class="glass-input border-2 border-white/70 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 rounded-xl p-4 transition">
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

                    <div class="mt-4 pt-4 border-t border-white/50">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Pilih Tipe Pengiriman:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="delivery_type" value="pickup" x-model="delivery" class="peer sr-only">
                                <div class="glass-input border-2 border-white/70 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 rounded-xl p-4 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="text-2xl">🏪</div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Ambil di Tempat</p>
                                            <p class="text-xs text-emerald-600 font-semibold">GRATIS ONGKIR</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Ambil langsung di pabrik EcoTahu, tanpa biaya ongkir.</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="delivery_type" value="delivery" x-model="delivery" class="peer sr-only">
                                <div class="glass-input border-2 border-white/70 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 rounded-xl p-4 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="text-2xl">🚚</div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Diantar</p>
                                            <p class="text-xs text-amber-600 font-semibold">Ongkir otomatis</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Min. belanja Rp 15.000. Pilih kecamatan / pin lokasi.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div x-show="payment === 'Transfer'" x-cloak class="mt-4 pt-4 border-t border-white/50">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Pilih Bank Tujuan:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @foreach (['BCA', 'BRI', 'Mandiri', 'BNI'] as $bank)
                                <label class="cursor-pointer">
                                    <input type="radio" name="bank_tujuan" value="{{ $bank }}" x-model="bank" class="peer sr-only">
                                    <div class="glass-input border-2 border-white/70 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 rounded-xl px-3 py-3 text-center transition">
                                        <p class="font-bold text-sm text-gray-800">{{ $bank }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 glass rounded-xl p-3 text-xs text-blue-800 border-blue-300/50">
                            ⏰ Selesaikan pembayaran dalam <strong>24 jam</strong>, atau pesanan otomatis dibatalkan.
                        </div>
                    </div>
                </div>

                {{-- 3. Lokasi Pengiriman --}}
                <div x-show="butuhOngkir" x-cloak class="glass-card p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-md shadow-emerald-500/30">3</span>
                        Lokasi Pengiriman
                    </h3>

                    <div class="glass-amber rounded-xl p-3 mb-4 text-sm text-amber-800">
                        📍 Pilih <strong>kecamatan</strong> wajib. Kalau mau lebih presisi, boleh pin lokasi di peta (opsional).
                    </div>

                    <div x-show="butuhOngkir && subtotal < minimalDelivery" x-cloak
                         class="glass rounded-xl p-3 mb-4 text-sm text-red-700 border-red-300/50">
                        ⚠️ Minimum pembelian untuk pengiriman adalah
                        <strong class="tabular-nums">Rp <span x-text="formatNumber(minimalDelivery)"></span></strong>.
                        Belanja Anda kurang <strong class="tabular-nums">Rp <span x-text="formatNumber(minimalDelivery - subtotal)"></span></strong>.
                        Silakan pilih <strong>"Ambil di Tempat"</strong> atau tambah item.
                    </div>

                    {{-- Dropdown Kecamatan --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kecamatan <span class="text-red-500">*</span>
                        </label>
                        <select name="kecamatan" x-model="kecamatan" required
                                @change="hitungDariKecamatan()"
                                class="glass-input w-full px-4 py-2.5 text-sm text-gray-700">
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach ($kecamatanList as $nama => $koord)
                                <option value="{{ $nama }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Ongkir dihitung otomatis dari titik tengah kecamatan.</p>
                    </div>

                    {{-- Pin Peta (Opsional) --}}
                    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Pin Lokasi di Peta (Opsional)</p>
                            <p class="text-xs text-gray-500">Kalau di-pin, ongkir lebih presisi. Kalau nggak, pakai kecamatan.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="pakaiLokasiSaya()"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-lg text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Pakai Lokasi Saya
                            </button>
                            <button type="button" @click="resetPeta()"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reset Pin
                            </button>
                        </div>
                    </div>

                    {{-- PETA --}}
                    <div id="map"
                         style="width: 100%; height: 400px; border-radius: 1rem; overflow: hidden; background: #f3f4f6; position: relative;"
                         x-init="$nextTick(() => { setTimeout(() => initMap(), 300) })"></div>

                    <input type="hidden" name="lat_pin" :value="latPin">
                    <input type="hidden" name="lng_pin" :value="lngPin">

                    {{-- Info jarak & ongkir --}}
                    <div x-show="jarak > 0" x-cloak class="grid grid-cols-3 gap-3 mt-3">
                        <div class="glass rounded-xl p-3">
                            <p class="text-xs text-gray-500 mb-1">Sumber</p>
                            <p class="text-sm font-bold text-gray-800" x-text="sumber"></p>
                        </div>
                        <div class="glass rounded-xl p-3">
                            <p class="text-xs text-gray-500 mb-1">Jarak</p>
                            <p class="text-sm font-bold text-gray-800 tabular-nums" x-text="jarak.toFixed(2) + ' km'"></p>
                        </div>
                        <div class="glass rounded-xl p-3">
                            <p class="text-xs text-gray-500 mb-1">Ongkir</p>
                            <p class="text-sm font-bold text-emerald-600 tabular-nums" x-text="'Rp ' + formatNumber(ongkir)"></p>
                        </div>
                    </div>

                    <p x-show="jarak > 0 && diLuarRadius" class="text-xs text-red-600 font-semibold mt-2">
                        ⚠️ Lokasi di luar jangkauan (maks. {{ $toko['radius_maks_km'] }} km). Silakan pilih kecamatan lain.
                    </p>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="lg:col-span-1">
                <div class="glass-card p-5 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto mb-4 pb-4 border-b border-white/50">
                        @foreach ($cart as $item)
                            <div class="flex gap-3">
                                <div class="w-12 h-12 rounded-lg bg-white/40 flex-shrink-0 overflow-hidden border border-white/60">
                                    @if ($item['gambar'])
                                        <img src="{{ asset('storage/' . $item['gambar']) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xl">{{ $item['type'] === 'produk' ? '🥛' : '🌾' }}</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item['nama'] }}</p>
                                    <p class="text-xs text-gray-500 tabular-nums">{{ $item['qty'] }} × Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 tabular-nums">Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800 tabular-nums">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">
                                Ongkir
                                <span x-show="!butuhOngkir" class="text-xs text-emerald-600">(Ambil di Tempat)</span>
                                <span x-show="butuhOngkir && jarak > 0" class="text-xs">(<span x-text="jarak.toFixed(2)"></span> km)</span>
                            </span>
                            <span class="font-semibold text-gray-800 tabular-nums">Rp <span x-text="formatNumber(ongkir)"></span></span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t-2 border-white/50 mb-5">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="text-xl price text-gradient-green">Rp <span x-text="formatNumber(total)"></span></span>
                    </div>

                    <button type="submit"
                            :disabled="butuhOngkir && (!kecamatan || diLuarRadius || subtotal < minimalDelivery)"
                            class="glass-btn-primary w-full py-3 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:shadow-none">
                        <span x-show="!butuhOngkir">Buat Pesanan</span>
                        <span x-show="butuhOngkir && subtotal < minimalDelivery">Belanja Kurang</span>
                        <span x-show="butuhOngkir && subtotal >= minimalDelivery && !kecamatan">Pilih Kecamatan Dulu</span>
                        <span x-show="butuhOngkir && subtotal >= minimalDelivery && kecamatan && diLuarRadius">Di Luar Jangkauan</span>
                        <span x-show="butuhOngkir && subtotal >= minimalDelivery && kecamatan && !diLuarRadius">Buat Pesanan</span>
                    </button>

                    <p class="text-xs text-gray-400 text-center mt-3">Dengan klik "Buat Pesanan", Anda setuju dengan syarat & ketentuan kami.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function checkoutForm() {
        const form = document.querySelector('form[x-data]');
        return {
            payment: form.dataset.payment || 'COD',
            delivery: form.dataset.delivery || 'delivery',
            bank: null,
            subtotal: parseFloat(form.dataset.subtotal) || 0,
            ongkirPerKm: parseFloat(form.dataset.ongkirPerKm) || 2500,
            ongkirMinimal: parseFloat(form.dataset.ongkirMinimal) || 5000,
            radiusMaks: parseFloat(form.dataset.radiusMaks) || 10,
            latToko: parseFloat(form.dataset.latToko),
            lngToko: parseFloat(form.dataset.lngToko),
            kecamatanList: JSON.parse(form.dataset.kecamatan),
            minimalDelivery: parseFloat(form.dataset.minimalDelivery) || 15000,

            kecamatan: form.dataset.kecamatanOld || '',
            latPin: null,
            lngPin: null,
            jarak: 0,
            sumber: '',
            mapInitialized: false,

            map: null,
            marker: null,
            tokoMarker: null,
            circle: null,

            get butuhOngkir() {
                return this.delivery === 'delivery';
            },
            get ongkir() {
                if (!this.butuhOngkir) return 0;
                if (this.jarak <= 0) return 0;
                const hitung = Math.ceil(this.jarak) * this.ongkirPerKm;
                return Math.max(hitung, this.ongkirMinimal);
            },
            get total() {
                return this.subtotal + this.ongkir;
            },
            get diLuarRadius() {
                return this.jarak > this.radiusMaks;
            },

            formatNumber(num) {
                return Number(Math.round(num)).toLocaleString('id-ID');
            },

            haversine(lat1, lng1, lat2, lng2) {
                const R = 6371;
                const toRad = d => d * Math.PI / 180;
                const dLat = toRad(lat2 - lat1);
                const dLng = toRad(lng2 - lng1);
                const a = Math.sin(dLat / 2) ** 2
                        + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            },

            init() {
                if (this.kecamatan) this.hitungDariKecamatan();
            },

            initMap() {
                if (this.mapInitialized) return;

                const mapEl = document.getElementById('map');
                if (!mapEl) return;

                // Cek container punya dimensi
                if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
                    // Coba lagi 300ms kemudian
                    setTimeout(() => this.initMap(), 300);
                    return;
                }

                this.map = L.map('map', {
                    zoomControl: true,
                    scrollWheelZoom: true,
                }).setView([this.latToko, this.lngToko], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxZoom: 19,
                }).addTo(this.map);

                const tokoIcon = L.divIcon({
                    className: 'toko-marker',
                    html: '<div style="background:#10b981; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow: 0 4px 12px rgba(16,185,129,0.5); border: 3px solid white;">🏭</div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                });

                this.tokoMarker = L.marker([this.latToko, this.lngToko], { icon: tokoIcon })
                    .addTo(this.map)
                    .bindPopup('<strong>{{ $toko["nama"] }}</strong><br><span style="font-size:11px;">{{ $toko["alamat"] }}</span>');

                this.circle = L.circle([this.latToko, this.lngToko], {
                    radius: this.radiusMaks * 1000,
                    color: '#10b981',
                    fillColor: '#10b981',
                    fillOpacity: 0.08,
                    weight: 1.5,
                    dashArray: '5, 5',
                }).addTo(this.map);

                this.map.on('click', (e) => {
                    this.setPin(e.latlng.lat, e.latlng.lng);
                });

                // Sekali invalidateSize setelah tiles mulai load
                setTimeout(() => {
                    if (this.map) this.map.invalidateSize({ animate: false });
                }, 200);

                this.mapInitialized = true;
            },

            setPin(lat, lng) {
                this.latPin = lat;
                this.lngPin = lng;
                this.sumber = 'Pin peta';

                this.jarak = this.haversine(this.latToko, this.lngToko, lat, lng);

                if (this.marker) {
                    this.marker.setLatLng([lat, lng]);
                } else {
                    const userIcon = L.divIcon({
                        className: 'user-marker',
                        html: '<div style="background:#f59e0b; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow: 0 4px 12px rgba(245,158,11,0.5); border: 3px solid white;">📍</div>',
                        iconSize: [32, 32],
                        iconAnchor: [16, 16],
                    });
                    this.marker = L.marker([lat, lng], { icon: userIcon, draggable: true }).addTo(this.map);
                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.setPin(pos.lat, pos.lng);
                    });
                }

                const bounds = L.latLngBounds([[this.latToko, this.lngToko], [lat, lng]]);
                this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
            },

            resetPeta() {
                if (this.marker) {
                    this.map.removeLayer(this.marker);
                    this.marker = null;
                }
                this.latPin = null;
                this.lngPin = null;

                if (this.kecamatan) {
                    this.hitungDariKecamatan();
                } else {
                    this.jarak = 0;
                    this.sumber = '';
                }

                if (this.map) this.map.setView([this.latToko, this.lngToko], 13);
            },

            hitungDariKecamatan() {
                if (!this.kecamatan) {
                    if (!this.latPin) {
                        this.jarak = 0;
                        this.sumber = '';
                    }
                    return;
                }

                const data = this.kecamatanList[this.kecamatan];
                if (!data) return;

                if (this.latPin && this.lngPin) return;

                this.jarak = this.haversine(this.latToko, this.lngToko, data.lat, data.lng);
                this.sumber = 'Kec. ' + this.kecamatan;
            },

            pakaiLokasiSaya() {
                if (!navigator.geolocation) {
                    alert('Browser Anda tidak mendukung GPS. Silakan klik manual di peta.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.setPin(position.coords.latitude, position.coords.longitude);
                    },
                    (error) => {
                        const msg = {
                            1: 'Izin lokasi ditolak. Silakan klik manual di peta.',
                            2: 'Lokasi tidak tersedia. Silakan klik manual di peta.',
                            3: 'Timeout. Silakan klik manual di peta.',
                        }[error.code] || 'Gagal mendapatkan lokasi. Silakan klik manual di peta.';
                        alert(msg);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        }
    }
</script>
@endpush