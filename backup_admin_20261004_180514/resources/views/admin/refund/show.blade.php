@extends('layouts.admin')
@section('title', 'Detail Refund')
@section('page-title', 'Detail Refund')

@section('content')

{{-- SUCCESS ALERT --}}
@if (session('success'))
    <div class="mb-5 flex items-center gap-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-5 py-4 rounded-2xl shadow-lg shadow-emerald-200 animate-pulse-once">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-bold">Berhasil!</p>
            <p class="text-sm opacity-90">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

{{-- ERROR ALERT --}}
@if (session('error'))
    <div class="mb-5 flex items-center gap-3 bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-4 rounded-2xl shadow-lg shadow-red-200">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1">
            <p class="font-bold">Gagal!</p>
            <p class="text-sm opacity-90">{{ session('error') }}</p>
        </div>
    </div>
@endif

{{-- VALIDATION ERRORS --}}
@if ($errors->any())
    <div class="mb-5 flex items-start gap-3 bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-4 rounded-2xl shadow-lg shadow-red-200">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1">
            <p class="font-bold mb-1">Ada kesalahan:</p>
            <ul class="text-sm opacity-90 space-y-0.5 list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- BACK BUTTON --}}
<div class="mb-6">
    <a href="{{ route('admin.refund.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 text-sm font-semibold rounded-xl shadow-sm transition group">
        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Refund
    </a>
</div>

{{-- HEADER --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Pengajuan Refund #{{ $refund->id }}</h2>
    <p class="text-sm text-gray-500 mt-1">Diajukan {{ $refund->tanggal_refund->format('d M Y, H:i') }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kolom Kiri --}}
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
                    <p class="text-gray-500 mb-2">Alasan Pembatalan</p>
                    <p class="text-gray-800 bg-gray-50 p-3 rounded-xl">{{ $refund->alasan_batal }}</p>
                </div>
            </div>
        </div>

        {{-- Item Pesanan --}}
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

        {{-- Bukti Transfer Balik --}}
        @if ($refund->bukti_transfer_balik)
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6">
                <h3 class="font-bold text-emerald-800 mb-3">✓ Bukti Transfer Balik</h3>
                <img src="{{ asset('storage/' . $refund->bukti_transfer_balik) }}"
                     class="w-full max-w-md rounded-xl border border-emerald-200">
            </div>
        @endif
    </div>

    {{-- Kolom Kanan --}}
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

        {{-- Form Update --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Proses Refund</h3>

            <form action="{{ route('admin.refund.update', $refund->id) }}" method="POST" enctype="multipart/form-data"
                  class="space-y-4" x-data="{ status: '{{ old('status_refund', $refund->status_refund) }}' }">
                @csrf @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status Refund</label>
                    <select name="status_refund" x-model="status" required
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="pending">Pending</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>

                {{-- Template Pesan Cepat --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                    <p class="text-xs font-semibold text-blue-700 mb-2">💡 Template Pesan Cepat:</p>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button"
                                @click="if(status === 'selesai') $refs.catatan.value = 'Dana refund sudah berhasil kami transfer ke rekening Anda. Mohon cek mutasi rekening. Terima kasih sudah berbelanja di EcoTahu, kami mohon maaf atas ketidaknyamanannya. 🙏'"
                                class="text-xs px-2.5 py-1.5 bg-white hover:bg-emerald-50 border border-blue-200 text-blue-700 rounded-lg transition">
                            Template: Selesai
                        </button>
                        <button type="button"
                                @click="if(status === 'ditolak') $refs.catatan.value = 'Mohon maaf, pengajuan refund Anda tidak dapat kami proses karena pesanan sudah dalam proses pengiriman / alasan lain yang tidak memenuhi syarat pembatalan. Untuk info lebih lanjut, silakan hubungi admin.'"
                                class="text-xs px-2.5 py-1.5 bg-white hover:bg-red-50 border border-blue-200 text-blue-700 rounded-lg transition">
                            Template: Ditolak
                        </button>
                        <button type="button"
                                @click="if(status === 'diproses') $refs.catatan.value = 'Refund Anda sedang kami proses. Mohon tunggu maksimal 1x24 jam untuk dana masuk ke rekening Anda. Terima kasih atas kesabarannya.'"
                                class="text-xs px-2.5 py-1.5 bg-white hover:bg-blue-50 border border-blue-200 text-blue-700 rounded-lg transition">
                            Template: Diproses
                        </button>
                        <button type="button" @click="$refs.catatan.value = ''"
                                class="text-xs px-2.5 py-1.5 bg-white hover:bg-gray-50 border border-blue-200 text-gray-600 rounded-lg transition">
                            Kosongkan
                        </button>
                    </div>
                </div>

                {{-- Catatan Admin --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Pesan untuk Pelanggan
                        <span class="text-gray-400 font-normal">(opsional, akan tampil di halaman pelanggan)</span>
                    </label>
                    <textarea name="catatan_admin" x-ref="catatan" rows="4" maxlength="1000"
                              class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                              placeholder="Contoh: Dana sudah kami transfer... / Mohon maaf pengajuan ditolak karena...">{{ old('catatan_admin', $refund->catatan_admin) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Maksimal 1000 karakter.</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bukti Transfer Balik (Opsional)</label>
                    <input type="file" name="bukti_transfer_balik" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                  file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WEBP. Max 5MB.</p>
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

@push('scripts')
<style>
    @keyframes pulse-once {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.01); }
    }
    .animate-pulse-once {
        animation: pulse-once 0.6s ease-in-out;
    }
</style>
@endpush