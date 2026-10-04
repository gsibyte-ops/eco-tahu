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
        'lat_tujuan',
        'lng_tujuan',
        'lat_kurir',
        'lng_kurir',
        'lokasi_updated_at',
        'pakai_pin',
        'total_harga',
        'payment_method',
        'delivery_type',
        'bank_tujuan',
        'va_number',
        'payment_status',
        'order_status',
        'alasan_batal',
        'alamat_pengiriman',
        'kecamatan',
        'catatan',
        'tanggal_order',
        'expired_at',
    ];

    protected $casts = [
        'tanggal_order'     => 'datetime',
        'expired_at'        => 'datetime',
        'lokasi_updated_at' => 'datetime',
        'jarak_km'          => 'float',
        'lat_tujuan'        => 'float',
        'lng_tujuan'        => 'float',
        'lat_kurir'         => 'float',
        'lng_kurir'         => 'float',
        'pakai_pin'         => 'boolean',
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_at
            && $this->expired_at->isPast()
            && $this->payment_status === 'pending'
            && $this->order_status === 'pending';
    }

    public function getDeliveryLabelAttribute(): string
    {
        if ($this->delivery_type === 'pickup') {
            return 'Ambil di Tempat';
        }
        return 'Diantar';
    }

    public function isTrackingActive(): bool
    {
        return $this->order_status === 'dikirim'
            && $this->lat_kurir !== null
            && $this->lng_kurir !== null
            && $this->lokasi_updated_at !== null
            && $this->lokasi_updated_at->diffInMinutes(now()) < 5;
    }
}