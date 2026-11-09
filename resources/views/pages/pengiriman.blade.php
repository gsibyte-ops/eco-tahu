@extends('layouts.user')
@section('title', 'Pengiriman')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="glass-card p-8 md:p-12">
        <h1 class="text-3xl font-extrabold mb-6" style="color: rgb(var(--text-primary));">Informasi Pengiriman</h1>
        <div class="space-y-4 leading-relaxed" style="color: rgb(var(--text-secondary));">
            <p>Kami melayani pengiriman dengan sistem <strong>Same-Day Delivery</strong> khusus untuk area Jember dan sekitarnya.</p>
            <ul class="list-disc pl-5 space-y-2">
                <li><strong>Jangkauan:</strong> Maksimal radius 10 km dari toko kami (Kecamatan Sumbersari).</li>
                <li><strong>Ongkos Kirim:</strong> Dihitung berdasarkan jarak kilometer (per km).</li>
                <li><strong>Minimal Pembelian:</strong> Rp 15.000 untuk pengiriman (Delivery).</li>
                <li><strong>Ambil di Tempat:</strong> Gratis ongkir jika Anda memilih opsi Pickup.</li>
            </ul>
        </div>
    </div>
</div>
@endsection