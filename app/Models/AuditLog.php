<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';

    // Insert-only log: hanya created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'pelaku_id',
        'tipe_pelaku',
        'aksi',
        'modul',
        'data_lama',
        'data_baru',
        'alamat_ip',
        'user_agent',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];
}