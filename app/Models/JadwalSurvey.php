<?php

namespace App\Models;

use App\Enums\HasilSurveyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalSurvey extends Model
{
    use HasFactory;

    protected $table = 'jadwal_survey';

    protected $fillable = [
        'permohonan_layanan_id',
        'admin_id',
        'tanggal_survey',
        'hasil',
        'catatan',
    ];

    protected $casts = [
        'hasil' => HasilSurveyEnum::class,
        'tanggal_survey' => 'date',
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