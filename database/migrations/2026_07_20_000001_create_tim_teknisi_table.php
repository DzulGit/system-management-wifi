<?php

namespace App\Models;

use App\Enums\HasilKerjaEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JadwalKerja extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kerja';

    protected $fillable = [
        'permohonan_layanan_id',
        'tim_teknisi_id',
        'tanggal_kerja',
        'hasil',
        'catatan_kendala',
        'diisi_oleh',
    ];

    protected $casts = [
        'hasil' => HasilKerjaEnum::class,
        'tanggal_kerja' => 'date',
    ];

    public function permohonanLayanan(): BelongsTo
    {
        return $this->belongsTo(PermohonanLayanan::class, 'permohonan_layanan_id');
    }

    public function timTeknisi(): BelongsTo
    {
        return $this->belongsTo(TimTeknisi::class, 'tim_teknisi_id');
    }

    public function diisiOleh(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'diisi_oleh');
    }

    /** Sumber kebenaran siapa yang benar-benar bisa akses pekerjaan ini. */
    public function teknisi(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'jadwal_kerja_teknisi', 'jadwal_kerja_id', 'admin_id')
            ->withTimestamps();
    }
}