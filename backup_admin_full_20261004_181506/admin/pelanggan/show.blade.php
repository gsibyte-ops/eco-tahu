@extends('layouts.admin')
@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')

{{-- BACK BUTTON --}}
<div class="mb-6">
    <a href="{{ route('admin.pelanggan.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 glass-card hover:border-emerald-300 hover:bg-emerald-50 txt-primary hover:txt-brand text-sm font-semibold rounded-xl shadow-sm transition group">
        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Pelanggan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kolom Kiri --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Profil --}}
        <div class="glass-card p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center txt-brand font-bold text-2xl">
                    {{ strtoupper(substr($pelanggan->username, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold txt-primary">{{ $pelanggan->username }}</h2>
                    <p class="text-sm txt-secondary">{{ $pelanggan->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs txt-secondary mb-1">No. Telepon</p>
                    <p class="txt-primary font-medium">{{ $pelanggan->no_telepon ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs txt-secondary mb-1">Terdaftar Sejak</p>
                    <p class="txt-primary font-medium">{{ $pelanggan->created_at->format('d M Y') }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs txt-secondary mb-1">Alamat</p>
                    <p class="txt-primary font-medium">{{ $pelanggan->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Riwayat Pesanan --}}
        <div class="glass-card p-6">
            <h3 class="font-bold txt-primary mb-4">Riwayat Pesanan (10 Terakhir)</h3>

            @if ($pelanggan->pesanan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-semibold txt-muted uppercase tracking-wider border-b bd-soft">
                                <th class="pb-3">Kode</th>
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Total</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach ($pelanggan->pesanan as $p)
                                @php
                                    $statusStyle = [
                                        'pending'    => 'background:#fef3c7; color:#b45309;',
                                        'diproses'   => 'background:#dbeafe; color:#1d4ed8;',
                                        'dikirim'    => 'background:#ede9fe; color:#6d28d9;',
                                        'selesai'    => 'background:#d1fae5; color:#047857;',
                                        'dibatalkan' => 'background:#fee2e2; color:#b91c1c;',
                                    ][$p->order_status] ?? 'background:#f3f4f6; color:#374151;';

                                    $modalData = [
                                        'kode' => $p->kode_pesanan,
                                        'order_status' => $p->order_status,
                                        'tanggal' => $p->tanggal_order->format('d M Y, H:i'),
                                        'alamat' => $p->alamat_pengiriman,
                                        'catatan' => $p->catatan,
                                        'subtotal' => (int) $p->subtotal,
                                        'ongkir' => (int) $p->ongkir,
                                        'jarak_km' => (float) $p->jarak_km,
                                        'total_harga' => (int) $p->total_harga,
                                        'payment_method' => $p->payment_method,
                                        'payment_status' => $p->payment_status,
                                        'user' => [
                                            'username' => $pelanggan->username,
                                            'email' => $pelanggan->email,
                                            'no_telepon' => $pelanggan->no_telepon ?? '-',
                                        ],
                                        'items' => $p->detail->map(fn ($d) => [
                                            'nama' => $d->nama_item,
                                            'harga' => (int) $d->harga_satuan,
                                            'qty' => $d->jumlah,
                                            'subtotal' => (int) $d->subtotal,
                                        ])->values()->toArray(),
                                        'pembayaran' => $p->pembayaran ? [
                                            'status' => $p->pembayaran->status_pembayaran,
                                            'bukti' => $p->pembayaran->bukti_transfer ? asset('storage/' . $p->pembayaran->bukti_transfer) : null,
                                        ] : null,
                                        'refund' => $p->refund ? [
                                            'nominal' => (int) $p->refund->nominal_refund,
                                            'alasan' => $p->refund->alasan_batal,
                                            'status' => $p->refund->status_refund,
                                        ] : null,
                                        'route_update_status' => route('admin.pesanan.updateStatus', $p->id),
                                        'route_verifikasi' => route('admin.pesanan.verifikasi', $p->id),
                                    ];
                                @endphp
                                <tr class="border-b bd-soft hover:bg-soft transition">
                                    <td class="py-3 font-semibold txt-primary">#{{ $p->kode_pesanan }}</td>
                                    <td class="py-3 txt-secondary">{{ $p->tanggal_order->format('d M Y') }}</td>
                                    <td class="py-3 font-semibold txt-primary">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="{{ $statusStyle }}">
                                            {{ ucfirst($p->order_status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <button type="button"
                                                data-pesanan="{{ json_encode($modalData, JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                                onclick="openPesananModal(JSON.parse(this.dataset.pesanan))"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 txt-brand-hover rounded-lg text-xs font-semibold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm txt-muted text-center py-6">Pelanggan ini belum pernah melakukan pesanan.</p>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan --}}
    <div class="space-y-5">

        {{-- Statistik --}}
        <div class="glass-card p-6">
            <h3 class="font-bold txt-primary mb-4">Statistik Pelanggan</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-soft rounded-xl">
                    <span class="text-sm txt-secondary">Total Pesanan</span>
                    <span class="font-bold txt-primary">{{ $statistik['total_pesanan'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-xl">
                    <span class="text-sm txt-brand-hover">Total Belanja</span>
                    <span class="font-bold txt-brand-hover">Rp {{ number_format($statistik['total_belanja'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                    <span class="text-sm txt-info">Pesanan Selesai</span>
                    <span class="font-bold txt-info">{{ $statistik['pesanan_selesai'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl">
                    <span class="text-sm txt-danger">Pesanan Dibatalkan</span>
                    <span class="font-bold txt-danger">{{ $statistik['pesanan_dibatalkan'] }}</span>
                </div>
            </div>
        </div>

        {{-- Kontak Cepat --}}
        <div class="glass-card p-6">
            <h3 class="font-bold txt-primary mb-3">Kontak Cepat</h3>
            <div class="space-y-2">

                {{-- Copy Email --}}
                <button type="button"
                        onclick="copyEmail('{{ $pelanggan->email }}')"
                        class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition text-left">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 txt-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold txt-primary">Copy Email</p>
                        <p class="text-xs txt-secondary truncate">{{ $pelanggan->email }}</p>
                    </div>
                </button>

                {{-- WhatsApp --}}
                @if ($pelanggan->no_telepon)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $pelanggan->no_telepon) }}"
                       target="_blank"
                       class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition text-left">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 txt-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold txt-primary">WhatsApp</p>
                            <p class="text-xs txt-secondary">{{ $pelanggan->no_telepon }}</p>
                        </div>
                    </a>
                @endif

                {{-- Gmail Web --}}
                <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to={{ $pelanggan->email }}&su={{ urlencode('Pesan dari EcoTahu') }}"
                   target="_blank" rel="noopener"
                   class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition text-left">
                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 11L2 4h20L12 11zm0 2.5l10-7v12a2 2 0 01-2 2H4a2 2 0 01-2-2v-12l10 7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold txt-primary">Buka Gmail Web</p>
                        <p class="text-xs txt-secondary">Kirim pesan Gmail ke pelanggan (perlu login Gmail dulu)</p>
                    </div>
                </a>

            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL DETAIL PESANAN --}}
{{-- ============================================ --}}
<div id="pesananModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 480px; max-height: 80vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 bg-emerald-600 text-white flex-shrink-0">
            <div>
                <p class="text-xs opacity-80">Pesanan</p>
                <h3 class="text-base font-bold">#<span id="mKode"></span></h3>
                <p class="text-xs opacity-80 mt-0.5" id="mTanggal"></p>
            </div>
            <div class="flex items-center gap-3">
                <span id="mStatusBadge" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/20 text-white"></span>
                <button onclick="closePesananModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 text-sm">

            {{-- Pelanggan --}}
            <div class="bg-soft rounded-xl p-3">
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Pelanggan</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center txt-brand font-bold text-base flex-shrink-0" id="mAvatar">U</div>
                    <div class="min-w-0">
                        <p class="font-semibold txt-primary" id="mUsername"></p>
                        <p class="text-xs txt-secondary truncate" id="mEmail"></p>
                        <p class="text-xs txt-secondary" id="mTelepon"></p>
                    </div>
                </div>
            </div>

            {{-- Alamat --}}
            <div class="bg-soft rounded-xl p-3">
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Alamat Pengiriman</p>
                <p class="txt-primary" id="mAlamat"></p>
                <p class="text-xs txt-secondary mt-1" id="mCatatan"></p>
            </div>

            {{-- Items --}}
            <div>
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Item Pesanan</p>
                <div class="border bd-soft rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-soft text-xs txt-secondary uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Produk</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="mItemsBody"></tbody>
                        <tfoot class="bg-soft text-sm">
                            <tr class="border-t bd-soft">
                                <td colspan="2" class="px-3 py-2 text-right txt-secondary">Subtotal</td>
                                <td class="px-3 py-2 text-right font-medium txt-primary" id="mSubtotal"></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-right txt-secondary">
                                    Ongkir <span class="text-xs txt-muted" id="mJarak"></span>
                                </td>
                                <td class="px-3 py-2 text-right font-medium txt-primary" id="mOngkir"></td>
                            </tr>
                            <tr class="border-t-2 bd-soft bg-emerald-50">
                                <td colspan="2" class="px-3 py-2 text-right font-bold txt-primary">Total</td>
                                <td class="px-3 py-2 text-right font-bold txt-brand" id="mTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Pembayaran --}}
            <div class="bg-soft rounded-xl p-3">
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Pembayaran</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs txt-secondary mb-0.5">Metode</p>
                        <p class="font-semibold txt-primary" id="mPaymentMethod"></p>
                    </div>
                    <div>
                        <p class="text-xs txt-secondary mb-0.5">Status</p>
                        <p class="font-semibold" id="mPaymentStatus"></p>
                    </div>
                </div>
                <div id="mBuktiWrapper" class="mt-2 hidden">
                    <p class="text-xs txt-secondary mb-1">Bukti Transfer</p>
                    <img id="mBukti" src="" alt="Bukti" class="w-full rounded-xl border bd-default">
                </div>
            </div>

            {{-- Refund --}}
            <div id="mRefundBox" class="hidden bg-red-50 border border-red-200 rounded-xl p-3">
                <p class="text-xs font-semibold text-red-500 uppercase mb-2">⚠️ Pengajuan Refund</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs txt-danger mb-0.5">Nominal</p>
                        <p class="font-bold text-red-800" id="mRefundNominal"></p>
                    </div>
                    <div>
                        <p class="text-xs txt-danger mb-0.5">Status</p>
                        <p class="font-bold text-red-800" id="mRefundStatus"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs txt-danger mb-0.5">Alasan</p>
                        <p class="text-red-800" id="mRefundAlasan"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const STATUS_COLORS = {
        pending: '#f59e0b',
        diproses: '#0ea5e9',
        dikirim: '#8b5cf6',
        selesai: '#10b981',
        dibatalkan: '#f43f5e',
    };

    const PAYMENT_STATUS_COLORS = {
        paid: '#10b981',
        pending: '#f59e0b',
        failed: '#ef4444',
        refunded: '#6b7280',
    };

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function openPesananModal(data) {
        const modal = document.getElementById('pesananModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        document.getElementById('mKode').textContent = data.kode;
        document.getElementById('mTanggal').textContent = data.tanggal;

        const badge = document.getElementById('mStatusBadge');
        badge.textContent = data.order_status.charAt(0).toUpperCase() + data.order_status.slice(1);
        badge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold text-white';
        badge.style.background = STATUS_COLORS[data.order_status] || '#6b7280';

        document.getElementById('mAvatar').textContent = data.user.username.charAt(0).toUpperCase();
        document.getElementById('mUsername').textContent = data.user.username;
        document.getElementById('mEmail').textContent = data.user.email;
        document.getElementById('mTelepon').textContent = data.user.no_telepon || '-';

        document.getElementById('mAlamat').textContent = data.alamat || '-';
        const catatan = document.getElementById('mCatatan');
        catatan.textContent = data.catatan ? 'Catatan: ' + data.catatan : '';

        const itemsBody = document.getElementById('mItemsBody');
        itemsBody.innerHTML = '';
        data.items.forEach(function(item) {
            itemsBody.innerHTML += `
                <tr class="border-b bd-soft">
                    <td class="px-3 py-2 txt-primary">
                        ${item.nama}
                        <p class="text-xs txt-secondary">${formatRupiah(item.harga)} × ${item.qty}</p>
                    </td>
                    <td class="px-3 py-2 text-center txt-secondary">${item.qty}</td>
                    <td class="px-3 py-2 text-right font-medium txt-primary">${formatRupiah(item.subtotal)}</td>
                </tr>
            `;
        });

        document.getElementById('mSubtotal').textContent = formatRupiah(data.subtotal);
        document.getElementById('mOngkir').textContent = formatRupiah(data.ongkir);
        document.getElementById('mJarak').textContent = '(' + data.jarak_km + ' km)';
        document.getElementById('mTotal').textContent = formatRupiah(data.total_harga);

        document.getElementById('mPaymentMethod').textContent = data.payment_method;
        const payStatus = document.getElementById('mPaymentStatus');
        payStatus.textContent = data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1);
        payStatus.style.color = PAYMENT_STATUS_COLORS[data.payment_status] || '#6b7280';

        const buktiWrapper = document.getElementById('mBuktiWrapper');
        if (data.pembayaran && data.pembayaran.bukti) {
            document.getElementById('mBukti').src = data.pembayaran.bukti;
            buktiWrapper.classList.remove('hidden');
        } else {
            buktiWrapper.classList.add('hidden');
        }

        const refundBox = document.getElementById('mRefundBox');
        if (data.refund) {
            document.getElementById('mRefundNominal').textContent = formatRupiah(data.refund.nominal);
            document.getElementById('mRefundAlasan').textContent = data.refund.alasan;
            document.getElementById('mRefundStatus').textContent = data.refund.status.charAt(0).toUpperCase() + data.refund.status.slice(1);
            refundBox.classList.remove('hidden');
        } else {
            refundBox.classList.add('hidden');
        }
    }

    function closePesananModal() {
        const modal = document.getElementById('pesananModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.getElementById('pesananModal').addEventListener('click', function(e) {
        if (e.target === this) closePesananModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePesananModal();
    });

    // === COPY EMAIL ===
    function copyEmail(email) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(email).then(() => {
                showToast('Email berhasil dicopy: ' + email);
            }).catch(() => fallbackCopy(email));
        } else {
            fallbackCopy(email);
        }
    }

    function fallbackCopy(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            showToast('Email berhasil dicopy: ' + text);
        } catch (e) {
            showToast('Gagal copy. Email: ' + text, 'error');
        }
        document.body.removeChild(ta);
    }

    function showToast(message, type = 'success') {
        const bg = type === 'success' ? '#10b981' : '#ef4444';
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 80px;
            right: 24px;
            z-index: 99999;
            background: ${bg};
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            opacity: 0;
            transform: translateX(100px);
            transition: all 0.3s ease;
            max-width: 300px;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 50);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endpush