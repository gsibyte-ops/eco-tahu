<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $fillable = ['nama_kategori', 'slug', 'tipe'];

    public function produkTahu()
    {
        return $this->hasMany(ProdukTahu::class, 'kategori_id');
    }

    public function limbah()
    {
        return $this->hasMany(Limbah::class, 'kategori_id');
    }
}