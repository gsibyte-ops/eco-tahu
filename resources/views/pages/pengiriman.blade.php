@extends('layouts.user')
@section('title', 'Pengiriman')

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center py-12 sm:py-16">
    <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card p-8 md:p-12">
            <h1 class="text-3xl font-extrabold mb-6" style="color: rgb(var(--text-primary));">Informasi Pengiriman</h1>
            <div class="space-y-4 leading-relaxed" style="color: rgb(var(--text-secondary));">
                <p>Kami melayani pengiriman dengan sistem <strong>Same-Day Delivery</strong> khusus untuk area Jember dan sekitarnya.</p>
                <ul class="list-disc pl-5 space-y-2">
                    <li><strong>Jangkauan:</strong> Maksimal radius {{ config('toko.radius_maks_km') }} km dari lokasi pabrik kami ({{ config('toko.alamat') }}).</li>
                    <li><strong>Ongkos Kirim:</strong> Tarif minimal Rp {{ number_format(config('toko.ongkir_minimal'), 0, ',', '.') }} (0–2 km), selanjutnya Rp {{ number_format(config('toko.ongkir_per_km'), 0, ',', '.') }}/km berdasarkan jarak dari toko ke titik lokasi Anda.</li>
                    <li><strong>Minimal Pembelian:</strong> Rp {{ number_format(config('toko.minimal_delivery'), 0, ',', '.') }} untuk pengiriman (Delivery).</li>
                    <li><strong>Ambil di Tempat:</strong> Gratis ongkir jika Anda memilih opsi Pickup.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection