<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'pesanan_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'komentar',
        'media_path',
        'media_type',
    ];

    protected $appends = ['media_url'];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function getMediaUrlAttribute(): ?string
    {
        return \App\Support\MediaGuard::toFileUrl($this->media_path);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function reviewable()
    {
        return $this->morphTo();
    }

    // Helper: string rating numerik (contoh: 4/5)
    public function getBintangAttribute(): string
    {
        return "{$this->rating}/5";
    }
}