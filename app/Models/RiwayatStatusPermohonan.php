<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatusPermohonan extends Model
{
    protected $table = 'riwayat_status_permohonan';

    // Insert-only log: hanya created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'permohonan_layanan_id',
        'status_sebelumnya',
        'status_sesudahnya',
        'diubah_oleh',
        'catatan',
    ];

    public function permohonanLayanan(): BelongsTo
    {
        return $this->belongsTo(PermohonanLayanan::class, 'permohonan_layanan_id');
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'diubah_oleh');
    }
}