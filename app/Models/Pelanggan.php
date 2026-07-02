<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pelanggan extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'pelanggan';

    protected $fillable = [
        'nomor_pelanggan',
        'nama_lengkap',
        'nik',
        'nomor_hp',
        'email',
        'password',
        'password_sudah_dibuat',
        'foto_ktp',
        'foto_selfie_ktp',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password_sudah_dibuat' => 'boolean',
        'password' => 'hashed',
    ];

    public function permohonanLayanan(): HasMany
    {
        return $this->hasMany(PermohonanLayanan::class, 'pelanggan_id');
    }

    public function layananInternet(): HasMany
    {
        return $this->hasMany(LayananInternet::class, 'pelanggan_id');
    }
}