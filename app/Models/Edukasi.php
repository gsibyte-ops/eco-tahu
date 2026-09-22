<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edukasi extends Model
{
    protected $table = 'edukasi';
    protected $fillable = [
        'user_id', 'judul', 'slug', 'konten',
        'thumbnail', 'status', 'tanggal_mengunggah',
    ];

    protected $casts = [
        'tanggal_mengunggah' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}