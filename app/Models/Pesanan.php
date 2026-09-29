<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'user_id',
        'kode_pesanan',
        'subtotal',
        'ongkir',
        'jarak_km',
        'total_harga',
        'payment_method',
        'delivery_type',    // 👈 BARU: pickup / delivery
        'bank_tujuan',
        'va_number',
        'payment_status',
        'order_status',
        'alasan_batal',     // 👈 BARU
        'alamat_pengiriman',
        'catatan',
        'tanggal_order',
        'expired_at',       // 👈 BARU
    ];

    protected $casts = [
        'tanggal_order' => 'datetime',
        'expired_at'    => 'datetime',  // 👈 BARU
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }

    // Helper: Cek apakah pesanan sudah expired (belum bayar & lewat 24 jam)
    public function isExpired(): bool
    {
        return $this->expired_at
            && $this->expired_at->isPast()
            && $this->payment_status === 'pending'
            && $this->order_status === 'pending';
    }

    // Helper: Label tipe pengiriman
    public function getDeliveryLabelAttribute(): string
    {
        if ($this->delivery_type === 'pickup') {
            return 'Ambil di Tempat';
        }
        return 'Diantar';
    }
}