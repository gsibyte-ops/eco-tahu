@extends('layouts.admin')
@section('title', 'Detail Refund')
@section('page-title', 'Detail Refund')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="mb-6">
    <a href="{{ route('admin.refund.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">← Kembali ke Daftar Refund</a>
    <h2 class="text-xl font-bold text-gray-800 mt-1">Pengajuan Refund #{{ $refund->id }}</h2>
    <p class="text-sm text-gray-500">Diajukan {{ $refund->tanggal_refund->format('d M Y, H:i') }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kolom Kiri: Detail --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info Refund --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Informasi Refund</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Kode Pesanan</span>
                    <span class="font-semibold text-gray-800">#{{ $refund->pesanan->kode_pesanan }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Total Pesanan</span>
                    <span class="font-semibold text-gray-800">Rp {{ number_format($refund->pesanan->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Nominal Refund</span>
                    <span class="font-bold text-emerald-700 text-base">Rp {{ number_format($refund->nominal_refund, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Metode Pembayaran</span>
                    <span class="font-semibold text-gray-800">{{ $refund->pesanan->payment_method }}</span>
                </div>
                <div class="py-2">
                    <p class="text-gray-500 mb-1">Alasan Pembatalan</p>
                    <p class="text-gray-800 bg-gray-50 p-3 rounded-xl">{{ $refund->alasan_batal }}</p>
                </div>
            </div>
        </div>

        {{-- Detail Item Pesanan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Item yang Dipesan</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3">Produk</th>
                        <th class="pb-3">Qty</th>
                        <th class="pb-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($refund->pesanan->detail as $d)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-800">{{ $d->nama_item }}</td>
                            <td class="py-3 text-gray-600">{{ $d->jumlah }}</td>
                            <td class="py-3 text-right font-semibold text-gray-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Bukti Transfer Balik (kalau ada) --}}
        @if ($refund->bukti_transfer_balik)
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6">
                <h3 class="font-bold text-emerald-800 mb-3">✓ Bukti Transfer Balik</h3>
                <img src="{{ asset('storage/' . $refund->bukti_transfer_balik) }}"
                     class="w-full max-w-md rounded-xl border border-emerald-200">
            </div>
        @endif
    </div>

    {{-- Kolom Kanan: Aksi --}}
    <div class="space-y-5">

        {{-- Info Pelanggan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Rekening Tujuan</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-lg">
                    {{ strtoupper(substr($refund->pesanan->user->username ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $refund->pesanan->user->username ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $refund->pesanan->user->email ?? '-' }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-xs text-gray-500">No. Telepon</p>
                    <p class="text-gray-800">{{ $refund->pesanan->user->no_telepon ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Form Update Status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Proses Refund</h3>

            <form action="{{ route('admin.refund.update', $refund->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status Refund</label>
                    <select name="status_refund" required
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="pending" {{ $refund->status_refund == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diproses" {{ $refund->status_refund == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ $refund->status_refund == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ $refund->status_refund == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bukti Transfer Balik (Opsional)</label>
                    <input type="file" name="bukti_transfer_balik" accept="image/*"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                  file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-500 mt-1">Upload bukti transfer balik ke pelanggan.</p>
                </div>

                <button type="submit"
                        class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Info Status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Info Status</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Ajuan</span>
                    <span class="font-semibold text-gray-800">{{ $refund->tanggal_refund->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status Saat Ini</span>
                    @php
                        $badge = [
                            'pending' => 'bg-amber-50 text-amber-700',
                            'diproses' => 'bg-blue-50 text-blue-700',
                            'selesai' => 'bg-emerald-50 text-emerald-700',
                            'ditolak' => 'bg-red-50 text-red-700',
                        ][$refund->status_refund] ?? 'bg-gray-50 text-gray-700';
                    @endphp
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $badge }}">{{ ucfirst($refund->status_refund) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection