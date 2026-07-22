<?php

namespace App\Models;

use App\Enums\JenisPerubahanPaketEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPerubahanPaket extends Model
{
    use HasFactory;

    protected $table = 'riwayat_perubahan_paket';

    protected $fillable = [
        'layanan_internet_id',
        'nama_paket_lama',
        'kecepatan_lama_mbps',
        'harga_lama',
        'nama_paket_baru',
        'kecepatan_baru_mbps',
        'harga_baru',
        'jenis_perubahan',
        'diubah_oleh',
        'tanggal_perubahan',
    ];

    protected $casts = [
        'jenis_perubahan' => JenisPerubahanPaketEnum::class,
        'tanggal_perubahan' => 'date',
        'harga_lama' => 'decimal:2',
        'harga_baru' => 'decimal:2',
    ];

    public function layananInternet(): BelongsTo
    {
        return $this->belongsTo(LayananInternet::class, 'layanan_internet_id');
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'diubah_oleh');
    }
}