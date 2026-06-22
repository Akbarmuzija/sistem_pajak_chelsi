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
        'nip',
        'email',
        'jabatan',
        'foto',
        'role',
        'password',
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
        ];
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }

    public function arsipPajak()
    {
        return $this->hasMany(ArsipPajak::class);
    }

    public function arsipDisetujui()
    {
        return $this->hasMany(ArsipPajak::class, 'disetujui_oleh');
    }
}
