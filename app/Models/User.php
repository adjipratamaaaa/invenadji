<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Scope untuk role
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeGudang($query)
    {
        return $query->where('role', 'gudang');
    }

    public function scopeKasir($query)
    {
        return $query->where('role', 'kasir');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Check role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGudang()
    {
        return $this->role === 'gudang';
    }

    public function isKasir()
    {
        return $this->role === 'kasir';
    }
}