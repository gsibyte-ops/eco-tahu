@extends('layouts.user')
@section('title', 'Sitemap')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="glass-card p-8 md:p-12">
        <h1 class="text-3xl font-extrabold mb-6" style="color: rgb(var(--text-primary));">Sitemap</h1>
        <ul class="list-disc pl-5 space-y-2 text-lg" style="color: rgb(var(--text-secondary));">
            <li><a href="{{ route('home') }}" class="hover:text-emerald-600 hover:underline">Beranda</a></li>
            <li><a href="{{ route('user.produk.index') }}" class="hover:text-emerald-600 hover:underline">Katalog Produk Tahu</a></li>
            <li><a href="{{ route('user.limbah.index') }}" class="hover:text-emerald-600 hover:underline">Katalog Produk Limbah</a></li>
            <li><a href="{{ route('user.edukasi.index') }}" class="hover:text-emerald-600 hover:underline">Artikel Edukasi</a></li>
            <li><a href="{{ route('halaman.cara-pesan') }}" class="hover:text-emerald-600 hover:underline">Cara Pesan</a></li>
            <li><a href="{{ route('halaman.faq') }}" class="hover:text-emerald-600 hover:underline">FAQ</a></li>
        </ul>
    </div>
</div>
@endsection