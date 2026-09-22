@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.pesanan.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">← Kembali ke Daftar</a>
        <h2 class="text-xl font-bold text-gray-800 mt-1">Pesanan #{{ $pesanan->kode_pesanan }}</h2>
        <p class="text-sm text-gray-500">Dibuat {{ $pesanan->tanggal_order->format('d M Y, H:i') }}</p>
    </div>

    @php
        $badge = [
            'pending' => 'bg-amber-50 text-amber-700',
            'diproses' => 'bg-blue-50 text-blue-700',
            'dikirim' => 'bg-indigo-50 text-indigo-700',
            'selesai' => 'bg-emerald-50 text-emerald-700',
            'dibatalkan' => 'bg-red-50 text-red-700',
        ][$pesanan->order_status] ?? 'bg-gray-50 text-gray-700';
    @endphp
    <span class="px-4 py-2 rounded-xl text-sm font-bold {{ $badge }}">{{ ucfirst($pesanan->order_status) }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kolom Kiri: Detail Produk --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Detail Item --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Detail Item</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3">Produk</th>
                        <th class="pb-3">Harga</th>
                        <th class="pb-3">Qty</th>
                        <th class="pb-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($pesanan->detail as $d)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-800">{{ $d->nama_item }}</td>
                            <td class="py-3 text-gray-600">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 text-gray-600">{{ $d->jumlah }}</td>
                            <td class="py-3 text-right font-semibold text-gray-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="text-sm">
                    <tr>
                        <td colspan="3" class="py-2 text-right text-gray-500">Subtotal</td>
                        <td class="py-2 text-right font-semibold text-gray-800">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="py-2 text-right text-gray-500">Ongkir ({{ $pesanan->jarak_km }} km × Rp 5.000)</td>
                        <td class="py-2 text-right font-semibold text-gray-800">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t border-gray-100">
                        <td colspan="3" class="py-3 text-right font-bold text-gray-700">Total</td>
                        <td class="py-3 text-right font-bold text-emerald-600 text-base">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Alamat Pengiriman --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-3">Alamat Pengiriman</h3>
            <p class="text-sm text-gray-600">{{ $pesanan->alamat_pengiriman }}</p>
            @if ($pesanan->catatan)
                <p class="text-xs text-gray-500 mt-2"><strong>Catatan:</strong> {{ $pesanan->catatan }}</p>
            @endif
        </div>

        {{-- Info Refund (kalau ada) --}}
        @if ($pesanan->refund)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <h3 class="font-bold text-red-800 mb-3">⚠️ Pengajuan Refund</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-red-600 text-xs">Nominal</p>
                        <p class="font-bold text-red-800">Rp {{ number_format($pesanan->refund->nominal_refund, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-red-600 text-xs">Status</p>
                        <p class="font-bold text-red-800">{{ ucfirst($pesanan->refund->status_refund) }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-red-600 text-xs">Alasan</p>
                        <p class="text-red-800">{{ $pesanan->refund->alasan_batal }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Kolom Kanan: Info Pelanggan + Update Status --}}
    <div class="space-y-5">

        {{-- Info Pelanggan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Info Pelanggan</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-lg">
                    {{ strtoupper(substr($pesanan->user->username ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $pesanan->user->username ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $pesanan->user->email ?? '-' }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-xs text-gray-500">No. Telepon</p>
                    <p class="text-gray-800">{{ $pesanan->user->no_telepon ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Info Pembayaran --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Info Pembayaran</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-semibold text-gray-800">{{ $pesanan->payment_method }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status Bayar</span>
                    <span class="font-semibold {{ $pesanan->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ ucfirst($pesanan->payment_status) }}
                    </span>
                </div>
                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div>
                        <p class="text-gray-500 text-xs mb-2">Bukti Transfer</p>
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}"
                             class="w-full rounded-xl border border-gray-100">
                    </div>
                @endif
            </div>

            @if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status !== 'paid')
                <form action="{{ route('admin.pesanan.verifikasi', $pesanan->id) }}" method="POST" class="mt-4">
                    @csrf
                    <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition">
                        ✓ Verifikasi Pembayaran
                    </button>
                </form>
            @endif
        </div>

        {{-- Update Status --}}
        @if ($pesanan->order_status !== 'dibatalkan' && $pesanan->order_status !== 'selesai')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4">Update Status</h3>
                <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="order_status" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3">
                        <option value="pending" {{ $pesanan->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diproses" {{ $pesanan->order_status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="dikirim" {{ $pesanan->order_status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="selesai" {{ $pesanan->order_status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ $pesanan->order_status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    <button type="submit"
                            class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition"
                            onclick="return confirm('Update status pesanan ini?')">
                        Update Status
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@endsection