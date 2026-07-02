<?php

namespace App\Models;

use App\Enums\StatusPerangkatEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perangkat extends Model
{
    use HasFactory;

    protected $table = 'perangkat';

    protected $fillable = [
        'layanan_internet_id',
        'serial_number',
        'mac_address',
        'merek',
        'tipe',
        'status',
    ];

    protected $casts = [
        'status' => StatusPerangkatEnum::class,
    ];

    public function layananInternet(): BelongsTo
    {
        return $this->belongsTo(LayananInternet::class, 'layanan_internet_id');
    }
}