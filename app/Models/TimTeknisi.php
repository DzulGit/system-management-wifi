<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimTeknisi extends Model
{
    use HasFactory;

    protected $table = 'tim_teknisi';

    protected $fillable = ['nama_tim', 'status_aktif'];

    protected $casts = ['status_aktif' => 'boolean'];

    public function anggota(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'tim_teknisi_anggota', 'tim_teknisi_id', 'admin_id')
            ->withTimestamps();
    }

    public function jadwalKerja(): HasMany
    {
        return $this->hasMany(JadwalKerja::class, 'tim_teknisi_id');
    }
}