<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'role_id', 'username', 'email', 'password', 'alamat', 'no_telepon',
    ];

    protected $hidden = ['password', 'remember_token'];

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

    //helper
    public function isAdmin(): bool
    {
        return $this->role?->nama_role === 'admin';
    }
}