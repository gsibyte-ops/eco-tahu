@extends('layouts.admin')
@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.pelanggan.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">← Kembali ke Daftar Pelanggan</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kolom Kiri: Profil + Riwayat --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Profil --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-2xl">
                    {{ strtoupper(substr($pelanggan->username, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $pelanggan->username }}</h2>
                    <p class="text-sm text-gray-500">{{ $pelanggan->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 mb-1">No. Telepon</p>
                    <p class="text-gray-800 font-medium">{{ $pelanggan->no_telepon ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Terdaftar Sejak</p>
                    <p class="text-gray-800 font-medium">{{ $pelanggan->created_at->format('d M Y') }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 mb-1">Alamat</p>
                    <p class="text-gray-800 font-medium">{{ $pelanggan->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Riwayat Pesanan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Riwayat Pesanan (10 Terakhir)</h3>

            @if ($pelanggan->pesanan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-3">Kode</th>
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Total</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach ($pelanggan->pesanan as $p)
                                <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                                    <td class="py-3 font-semibold text-gray-800">#{{ $p->kode_pesanan }}</td>
                                    <td class="py-3 text-gray-500">{{ $p->tanggal_order->format('d M Y') }}</td>
                                    <td class="py-3 font-semibold text-gray-800">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        @php
                                            $badge = [
                                                'pending' => 'bg-amber-50 text-amber-700',
                                                'diproses' => 'bg-blue-50 text-blue-700',
                                                'dikirim' => 'bg-indigo-50 text-indigo-700',
                                                'selesai' => 'bg-emerald-50 text-emerald-700',
                                                'dibatalkan' => 'bg-red-50 text-red-700',
                                            ][$p->order_status] ?? 'bg-gray-50 text-gray-700';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $badge }}">{{ ucfirst($p->order_status) }}</span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('admin.pesanan.show', $p->id) }}" class="text-emerald-600 hover:underline text-xs font-medium">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-6">Pelanggan ini belum pernah melakukan pesanan.</p>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Statistik --}}
    <div class="space-y-5">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Statistik Pelanggan</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-600">Total Pesanan</span>
                    <span class="font-bold text-gray-800">{{ $statistik['total_pesanan'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-xl">
                    <span class="text-sm text-emerald-700">Total Belanja</span>
                    <span class="font-bold text-emerald-700">Rp {{ number_format($statistik['total_belanja'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                    <span class="text-sm text-blue-700">Pesanan Selesai</span>
                    <span class="font-bold text-blue-700">{{ $statistik['pesanan_selesai'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl">
                    <span class="text-sm text-red-700">Pesanan Dibatalkan</span>
                    <span class="font-bold text-red-700">{{ $statistik['pesanan_dibatalkan'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-3">Kontak Cepat</h3>
            <div class="space-y-2">
                <a href="mailto:{{ $pelanggan->email }}"
                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="text-sm text-gray-700">Kirim Email</span>
                </a>
                @if ($pelanggan->no_telepon)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $pelanggan->no_telepon) }}" target="_blank"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-sm text-gray-700">WhatsApp</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection