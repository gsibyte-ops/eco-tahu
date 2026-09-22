<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $fillable = [
        'pesanan_id', 'item_type', 'item_id', 'nama_item',
        'harga_satuan', 'jumlah', 'subtotal',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function detailPesanan()
    {
        return $this->morphMany(DetailPesanan::class, 'item');
    }
}