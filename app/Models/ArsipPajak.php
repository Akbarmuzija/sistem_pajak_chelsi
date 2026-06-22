<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipPajak extends Model
{
    protected $table = 'arsip_pajak';

    protected $fillable = [
        'user_id',
        'jenis_pajak_id',
        'nomor_arsip',
        'tahun_pajak',
        'periode',
        'jumlah_pajak',
        'tanggal_setor',
        'nomor_bukti_setor',
        'file_dokumen',
        'keterangan',
        'status',
        'catatan_pimpinan',
        'disetujui_oleh',
        'tanggal_disetujui',
    ];

    protected $casts = [
        'tanggal_setor'    => 'date',
        'tanggal_disetujui' => 'datetime',
        'jumlah_pajak'     => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'badge-draft',
            'menunggu'  => 'badge-menunggu',
            'disetujui' => 'badge-disetujui',
            'ditolak'   => 'badge-ditolak',
            default     => 'badge-draft',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'menunggu'  => 'Menunggu Approval',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => 'Draft',
        };
    }

    public static function generateNomorArsip(): string
    {
        $year  = date('Y');
        $month = date('m');
        $last  = static::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        return 'ARS/' . $year . '/' . $month . '/' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
