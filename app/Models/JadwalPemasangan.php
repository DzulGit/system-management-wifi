<?php

namespace App\Models;

use App\Enums\HasilPemasanganEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPemasangan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pemasangan';

    protected $fillable = [
        'permohonan_layanan_id',
        'admin_id',
        'tanggal_pemasangan',
        'hasil',
        'alasan_penundaan',
    ];

    protected $casts = [
        'hasil' => HasilPemasanganEnum::class,
        'tanggal_pemasangan' => 'date',
    ];

    public function permohonanLayanan(): BelongsTo
    {
        return $this->belongsTo(PermohonanLayanan::class, 'permohonan_layanan_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}