<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'role_id', 'username', 'email', 'password', 'alamat', 'no_telepon', 'google_id', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['avatar_url'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function edukasi()
    {
        return $this->hasMany(Edukasi::class);
    }

    //reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper for avatar URL
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }
        $clean = ltrim(str_replace('storage/', '', $this->avatar), '/');
        return url('/files/' . $clean);
    }

    //helper
    public function isAdmin(): bool
    {
        return $this->role?->nama_role === 'admin';
    }
}