<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukTahu extends Model
{
    protected $table = 'produk_tahu';
    protected $fillable = [
        'kategori_id', 'nama_produk', 'slug', 'deskripsi',
        'harga', 'stok', 'gambar', 'status',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}