<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukTahu extends Model
{
    protected $table = 'produk_tahu';
    protected $fillable = [
        'kategori_id', 'nama_produk', 'slug', 'deskripsi',
        'harga', 'satuan', 'stok', 'gambar', 'status',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    // 👈 BARU: polymorphic relasi ke DetailPesanan
    public function detailPesanan()
    {
        return $this->morphMany(DetailPesanan::class, 'item');
    }
}