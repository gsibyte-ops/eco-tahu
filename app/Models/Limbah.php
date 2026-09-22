<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Limbah extends Model
{
    protected $table = 'limbah';
    protected $fillable = [
        'kategori_id', 'nama_limbah', 'slug', 'deskripsi',
        'harga', 'stok', 'satuan', 'gambar', 'status',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailPesanan()
    {
        return $this->morphMany(DetailPesanan::class, 'item');
    }
}