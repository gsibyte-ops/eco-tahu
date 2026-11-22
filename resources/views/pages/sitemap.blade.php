@extends('layouts.user')
@section('title', 'Sitemap')

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center py-12 sm:py-16">
    <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card p-8 md:p-12">
            <h1 class="text-3xl font-extrabold mb-6" style="color: rgb(var(--text-primary));">Sitemap</h1>
            <ul class="list-disc pl-5 space-y-2 text-lg" style="color: rgb(var(--text-secondary));">
                <li><a href="{{ route('home') }}" class="hover:text-emerald-600 hover:underline">Beranda</a></li>
                <li><a href="{{ route('user.produk.index') }}" class="hover:text-emerald-600 hover:underline">Katalog Produk Tahu</a></li>
                <li><a href="{{ route('user.limbah.index') }}" class="hover:text-emerald-600 hover:underline">Katalog Produk Limbah</a></li>
                <li><a href="{{ route('user.resep.index') }}" class="hover:text-emerald-600 hover:underline">Resep Tahu</a></li>
                <li><a href="{{ route('user.edukasi.index') }}" class="hover:text-emerald-600 hover:underline">Artikel Edukasi</a></li>
                <li><a href="{{ route('halaman.bantuan') }}" class="hover:text-emerald-600 hover:underline">Pusat Bantuan</a></li>
                <li><a href="{{ route('halaman.cara-pesan') }}" class="hover:text-emerald-600 hover:underline">Cara Pesan</a></li>
                <li><a href="{{ route('halaman.pengiriman') }}" class="hover:text-emerald-600 hover:underline">Info Pengiriman</a></li>
                <li><a href="{{ route('halaman.refund') }}" class="hover:text-emerald-600 hover:underline">Kebijakan Refund</a></li>
                <li><a href="{{ route('halaman.faq') }}" class="hover:text-emerald-600 hover:underline">FAQ</a></li>
                <li><a href="{{ route('halaman.snk') }}" class="hover:text-emerald-600 hover:underline">Syarat & Ketentuan</a></li>
                <li><a href="{{ route('halaman.privasi') }}" class="hover:text-emerald-600 hover:underline">Kebijakan Privasi</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
