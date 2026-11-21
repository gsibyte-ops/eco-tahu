<?php

namespace App\Models;

use App\Support\MediaGuard;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'user_id',
        'judul',
        'slug',
        'deskripsi',
        'bahan',
        'langkah',
        'durasi_menit',
        'porsi',
        'tingkat_kesulitan',
        'media_type',
        'thumbnail',
        'video_path',
        'youtube_url',
        'status',
        'scheduled_at',
        'published_at',
    ];

    protected $casts = [
        'durasi_menit' => 'integer',
        'porsi'        => 'integer',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'thumbnail_url',
        'video_url',
        'youtube_embed_url',
        'youtube_thumbnail_url',
        'display_thumbnail_url',
        'share_url',
    ];

    public function getThumbnailUrlAttribute(): ?string
    {
        return MediaGuard::toFileUrl($this->thumbnail);
    }

    public function getVideoUrlAttribute(): ?string
    {
        return MediaGuard::toFileUrl($this->video_path);
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        return MediaGuard::toYoutubeEmbedUrl($this->youtube_url);
    }

    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        return MediaGuard::toYoutubeThumbnailUrl($this->youtube_url);
    }

    public function getDisplayThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_url ?: $this->youtube_thumbnail_url;
    }

    public function getShareUrlAttribute(): string
    {
        return url('/resep/' . $this->slug);
    }

    /**
     * Pecah daftar bahan per baris, buang baris kosong.
     */
    public function bahanList(): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $this->bahan) ?: [];
        return array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
    }

    /**
     * Pecah daftar langkah per baris, buang baris kosong.
     */
    public function langkahList(): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $this->langkah) ?: [];
        return array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'resep_likes', 'resep_id', 'user_id')->withTimestamps();
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope: resep yang sudah waktunya tayang tapi belum di-publish.
     */
    public function scopeDueForPublish($query)
    {
        return $query->where('status', 'scheduled')
                     ->whereNotNull('scheduled_at')
                     ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope: resep yang boleh dilihat user publik.
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
