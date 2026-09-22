<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'refund';
    protected $fillable = [
        'pesanan_id', 'nominal_refund', 'alasan_batal',
        'bukti_transfer_balik', 'status_refund', 'tanggal_refund',
    ];

    protected $casts = [
        'tanggal_refund' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}