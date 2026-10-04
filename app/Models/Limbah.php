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

    // REVIEWS
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function averageRating(): float
    {
        return round((float) ($this->reviews()->avg('rating') ?? 0), 1);
    }

    public function reviewCount(): int
    {
        return $this->reviews()->count();
    }
}