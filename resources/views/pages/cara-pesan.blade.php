@extends('layouts.user')
@section('title', 'Cara Pesan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="glass-card p-8 md:p-12">
        <h1 class="text-3xl font-extrabold mb-6" style="color: rgb(var(--text-primary));">Cara Pesan</h1>
        <div class="space-y-4 leading-relaxed" style="color: rgb(var(--text-secondary));">
            <p>Memesan produk di EcoTahu sangat mudah. Ikuti langkah berikut:</p>
            <ol class="list-decimal pl-5 space-y-2">
                <li>Pilih produk tahu atau limbah yang ingin dibeli.</li>
                <li>Klik tombol <strong>Tambah ke Keranjang</strong>.</li>
                <li>Buka halaman Keranjang dan pastikan jumlah pesanan sudah benar.</li>
                <li>Klik <strong>Checkout</strong>, lalu isi data penerima dan alamat pengiriman.</li>
                <li>Pilih metode pembayaran (COD atau Transfer).</li>
                <li>Selesaikan pembayaran (jika transfer) dan unggah bukti pembayaran.</li>
                <li>Pesanan Anda akan diproses dan dikirim oleh kami.</li>
            </ol>
        </div>
    </div>
</div>
@endsection