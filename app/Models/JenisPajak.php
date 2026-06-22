<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPajak extends Model
{
    protected $table = 'jenis_pajak';

    protected $fillable = [
        'kode',
        'nama_jenis',
        'deskripsi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function arsipPajak()
    {
        return $this->hasMany(ArsipPajak::class);
    }
}
