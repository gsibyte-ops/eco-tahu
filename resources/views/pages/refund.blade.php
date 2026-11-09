@extends('layouts.user')
@section('title', 'Kebijakan Refund')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="glass-card p-8 md:p-12">
        <h1 class="text-3xl font-extrabold mb-6" style="color: rgb(var(--text-primary));">Kebijakan Refund (Pengembalian Dana)</h1>
        <div class="space-y-4 leading-relaxed" style="color: rgb(var(--text-secondary));">
            <p>Kepuasan pelanggan adalah prioritas kami. Namun, karena produk tahu adalah makanan segar, kami memiliki aturan ketat terkait refund:</p>
            <ul class="list-disc pl-5 space-y-2">
                <li>Refund dapat dilakukan jika pesanan <strong>batal</strong> (stok habis atau kurir gagal mengantar).</li>
                <li>Komplain produk rusak/basi wajib menyertakan <strong>video unboxing</strong> dan maksimal 1x24 jam setelah diterima.</li>
                <li>Dana akan dikembalikan ke rekening Anda maksimal 3 hari kerja setelah disetujui admin.</li>
            </ul>
        </div>
    </div>
</div>
@endsection