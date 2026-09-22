<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $fillable = [
        'user_id', 'kode_pesanan', 'subtotal', 'ongkir', 'jarak_km',
        'total_harga', 'payment_method', 'payment_status', 'order_status',
        'alamat_pengiriman', 'catatan', 'tanggal_order',
    ];

    protected $casts = [
        'tanggal_order' => 'datetime',
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
}