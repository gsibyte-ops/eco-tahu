<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edukasi extends Model
{
    protected $table = 'edukasi';

    protected $fillable = [
        'user_id',
        'judul',
        'slug',
        'konten',
        'thumbnail',
        'status',
        'scheduled_at',
        'published_at',
        'tanggal_mengunggah',
    ];

    protected $casts = [
        'tanggal_mengunggah' => 'datetime',
        'scheduled_at'       => 'datetime',
        'published_at'       => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: artikel yang sudah waktunya tayang tapi belum di-publish.
     */
    public function scopeDueForPublish($query)
    {
        return $query->where('status', 'scheduled')
                     ->whereNotNull('scheduled_at')
                     ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope: artikel yang boleh dilihat user publik.
     * - status publish, ATAU
     * - status scheduled tapi jadwalnya sudah lewat (fallback kalau scheduler mati)
     */
    public function scopeVisibleToUser($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'publish')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'scheduled')
                     ->whereNotNull('scheduled_at')
                     ->where('scheduled_at', '<=', now());
              });
        });
    }
}