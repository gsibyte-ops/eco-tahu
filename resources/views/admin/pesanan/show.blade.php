@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@push('styles')
<style>
    /* CSS KHUSUS BIAR SIDEBAR & HEADER ILANG PAS PREVIEW/LIGHTBOX MUNCUL */
    body.modal-active #adminSidebar { display: none !important; }
    body.modal-active .sticky.top-0 { display: none !important; }
    body.modal-active .lg\:ml-\[17rem\] { margin-left: 0 !important; }
</style>
@endpush

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 txt-brand-hover px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.pesanan.index') }}" class="text-sm txt-secondary hover:txt-brand">← Kembali ke Daftar</a>
        <h2 class="text-xl font-bold txt-primary mt-1">Pesanan #{{ $pesanan->kode_pesanan }}</h2>
        <p class="text-sm txt-secondary">Dibuat {{ $pesanan->tanggal_order->format('d M Y, H:i') }}</p>
    </div>

    @php
        $badge = [
            'pending' => 'bg-amber-50 txt-accent',
            'diproses' => 'bg-blue-50 txt-info',
            'dikirim' => 'bg-indigo-50 text-indigo-700',
            'selesai' => 'bg-emerald-50 txt-brand-hover',
            'dibatalkan' => 'bg-red-50 txt-danger',
        ][$pesanan->order_status] ?? 'bg-soft txt-primary';
    @endphp
    <span class="px-4 py-2 rounded-xl text-sm font-bold {{ $badge }}">{{ ucfirst($pesanan->order_status) }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kolom Kiri --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Detail Item --}}
        <div class="glass-card p-6">
            <h3 class="font-bold txt-primary mb-4">Detail Item</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs font-semibold txt-muted uppercase tracking-wider border-b bd-soft">
                        <th class="pb-3">Produk</th>
                        <th class="pb-3">Harga</th>
                        <th class="pb-3">Qty</th>
                        <th class="pb-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($pesanan->detail as $d)
                        <tr class="border-b bd-soft">
                            <td class="py-3 txt-primary">{{ $d->nama_item }}</td>
                            <td class="py-3 txt-secondary">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 txt-secondary">{{ $d->jumlah }}</td>
                            <td class="py-3 text-right font-semibold txt-primary">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="text-sm">
                    <tr>
                        <td colspan="3" class="py-2 text-right txt-secondary">Subtotal</td>
                        <td class="py-2 text-right font-semibold txt-primary">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if (($pesanan->diskon ?? 0) > 0)
                        <tr>
                            <td colspan="3" class="py-2 text-right font-medium txt-brand">Diskon Order Pertama</td>
                            <td class="py-2 text-right font-semibold txt-brand">-Rp {{ number_format($pesanan->diskon, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="py-2 text-right txt-secondary">
                            Ongkir
                            @if ($pesanan->jarak_km > 0)
                                ({{ $pesanan->jarak_km }} km × Rp 2.500)
                            @else
                                (Ambil di Tempat)
                            @endif
                        </td>
                        <td class="py-2 text-right font-semibold txt-primary">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t bd-soft">
                        <td colspan="3" class="py-3 text-right font-bold txt-primary">Total</td>
                        <td class="py-3 text-right font-bold txt-brand text-base">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Alamat Pengiriman --}}
        <div class="glass-card p-6">
            <h3 class="font-bold txt-primary mb-3">Alamat Pengiriman</h3>
            <p class="text-sm txt-secondary">{{ $pesanan->alamat_pengiriman }}</p>
            <p class="text-xs txt-secondary mt-2"><strong>Tipe Pengiriman:</strong> {{ $pesanan->delivery_label }}</p>
            @if ($pesanan->catatan)
                <p class="text-xs txt-secondary mt-2"><strong>Catatan:</strong> {{ $pesanan->catatan }}</p>
            @endif
        </div>

        {{-- Info Refund (kalau ada) --}}
        @if ($pesanan->refund)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <h3 class="font-bold text-red-800 mb-3 flex items-center gap-1.5"><x-icon name="alert-triangle" class="w-4 h-4" /> Pengajuan Refund</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="txt-danger text-xs">Nominal</p>
                        <p class="font-bold text-red-800">Rp {{ number_format($pesanan->refund->nominal_refund, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="txt-danger text-xs">Status</p>
                        <p class="font-bold text-red-800">{{ ucfirst($pesanan->refund->status_refund) }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="txt-danger text-xs">Alasan</p>
                        <p class="text-red-800">{{ $pesanan->refund->alasan_batal }}</p>
                    </div>
                    @if ($pesanan->refund->nama_bank || $pesanan->refund->nomor_rekening || $pesanan->refund->atas_nama)
                        <div class="col-span-2 bg-white/80 rounded-xl p-3 border border-red-200">
                            <p class="txt-danger text-xs font-bold mb-1 flex items-center gap-1"><x-icon name="bank" class="w-3.5 h-3.5" /> Rekening Pengembalian Dana</p>
                            <p class="text-red-900 text-xs font-semibold">
                                {{ $pesanan->refund->nama_bank ?: '-' }} · {{ $pesanan->refund->nomor_rekening ?: '-' }}
                                @if ($pesanan->refund->atas_nama) (a.n. {{ $pesanan->refund->atas_nama }}) @endif
                            </p>
                        </div>
                    @endif
                    @if ($pesanan->refund->catatan_admin)
                        <div class="col-span-2">
                            <p class="txt-danger text-xs mb-1">Catatan Admin</p>
                            <p class="text-red-800 bg-white rounded-lg p-2">{{ $pesanan->refund->catatan_admin }}</p>
                        </div>
                    @endif
                </div>
                <a href="{{ route('admin.refund.show', $pesanan->refund->id) }}"
                   class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold txt-danger hover:text-red-900">
                    Lihat Detail Refund →
                </a>
            </div>
        @endif
    </div>

    {{-- Kolom Kanan --}}
    <div class="space-y-5">

        {{-- Info Pelanggan --}}
        <div class="glass-card p-6">
            <h3 class="font-bold txt-primary mb-4">Info Pelanggan</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center txt-brand font-bold text-lg">
                    {{ strtoupper(substr($pesanan->user->username ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold txt-primary">{{ $pesanan->user->username ?? '-' }}</p>
                    <p class="text-xs txt-secondary">{{ $pesanan->user->email ?? '-' }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-xs txt-secondary">No. Telepon</p>
                    <p class="txt-primary">{{ $pesanan->user->no_telepon ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Info Pembayaran + VA Display --}}
        <div class="glass-card p-6">
            <h3 class="font-bold txt-primary mb-4">Info Pembayaran</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="txt-secondary">Metode</span>
                    <span class="font-semibold txt-primary">{{ $pesanan->payment_method }}</span>
                </div>
                @if ($pesanan->bank_tujuan)
                    <div class="flex justify-between">
                        <span class="txt-secondary">Bank</span>
                        <span class="font-semibold txt-primary">{{ $pesanan->bank_tujuan }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="txt-secondary">VA Number</span>
                        <span class="font-semibold txt-primary font-mono">{{ $pesanan->va_number }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="txt-secondary">Status Bayar</span>
                    <span class="font-semibold {{ $pesanan->payment_status === 'paid' ? 'txt-brand' : 'txt-accent' }}">
                        {{ ucfirst($pesanan->payment_status) }}
                    </span>
                </div>
                @if ($pesanan->expired_at && $pesanan->payment_status === 'pending')
                    <div class="flex justify-between">
                        <span class="txt-secondary">Expired</span>
                        <span class="font-semibold {{ $pesanan->expired_at->isPast() ? 'txt-danger' : 'txt-primary' }}">
                            {{ $pesanan->expired_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                @endif
                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div>
                        <p class="txt-secondary text-xs mb-2">Bukti Transfer</p>
                        <div onclick="openLightbox('{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}')" class="cursor-pointer">
                            <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}"
                                 class="w-full rounded-xl border bd-soft hover:opacity-90 transition">
                        </div>
                        <p class="text-xs txt-secondary mt-1">Klik untuk lihat full size</p>
                    </div>
                @endif
            </div>

            @if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status !== 'paid')
                <form action="{{ route('admin.pesanan.verifikasi', $pesanan->id) }}" method="POST" class="mt-4">
                    @csrf
                    <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-1.5">
                        <x-icon name="check-circle" class="w-4 h-4" /> Verifikasi Pembayaran
                    </button>
                </form>
            @endif
        </div>

        {{-- Update Status / Batalkan --}}
        @if ($pesanan->order_status !== 'dibatalkan' && $pesanan->order_status !== 'selesai')

            {{-- Card Update Status --}}
            <div class="glass-card p-6">
                <h3 class="font-bold txt-primary mb-4">Update Status</h3>
                <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="order_status" required
                            class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3">
                        <option value="pending" {{ $pesanan->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diproses" {{ $pesanan->order_status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="dikirim" {{ $pesanan->order_status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="selesai" {{ $pesanan->order_status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <button type="submit"
                            class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition"
                            onclick="return confirm('Update status pesanan ini?')">
                        Update Status
                    </button>
                </form>
            </div>

            {{-- Card Batalkan --}}
            <div class="bg-red-50 border border-red-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-red-800 mb-4">Batalkan Pesanan</h3>
                <form action="{{ route('admin.pesanan.batalkan', $pesanan->id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan pesanan ini? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="alasan_batal" class="block text-sm font-medium text-red-800 mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
                        <input type="text" name="alasan_batal" id="alasan_batal"
                               class="w-full px-4 py-2.5 rounded-xl border border-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                               required placeholder="Misal: Stok habis, pembayaran tidak valid...">
                    </div>
                    <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition">
                        Batalkan Pesanan
                    </button>
                </form>
            </div>

        @elseif ($pesanan->order_status === 'dibatalkan')
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <h3 class="font-bold text-red-800 mb-3">Pesanan Dibatalkan</h3>
                <div class="text-sm">
                    <p class="txt-danger text-xs mb-1">Alasan Pembatalan</p>
                    <p class="text-red-800 font-semibold">{{ $pesanan->alasan_batal ?? 'Tidak ada alasan yang dicatat' }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@push('modals')
{{-- LIGHTBOX PREVIEW BUKTI TRANSFER --}}
<div id="imageLightbox" class="fixed inset-0 hidden"
     style="z-index: 99999; background: rgba(0,0,0,0.88); backdrop-filter: blur(8px);">
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 24px;" onclick="closeLightbox()">
        <img id="imageLightboxImg" src="" alt="Preview Bukti"
             onclick="event.stopPropagation()"
             style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
    </div>
    <button type="button" onclick="closeLightbox()"
            style="position: absolute; top: 20px; right: 20px; width: 48px; height: 48px; background: #ef4444; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100000; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"
            title="Tutup">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endpush

@push('scripts')
<script>
    function openLightbox(url) {
        document.getElementById('imageLightboxImg').src = url;
        document.getElementById('imageLightbox').classList.remove('hidden');
        document.body.classList.add('modal-active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('imageLightbox').classList.add('hidden');
        document.getElementById('imageLightboxImg').src = '';
        document.body.classList.remove('modal-active');
        document.body.style.overflow = '';
    }

    document.getElementById('imageLightbox')?.addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endpush