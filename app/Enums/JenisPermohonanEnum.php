<?php

namespace App\Enums;

enum JenisPermohonanEnum: string
{
    case PEMASANGAN_BARU = 'pemasangan_baru';
    case RELOKASI = 'relokasi';

    public function label(): string
    {
        return match ($this) {
            self::PEMASANGAN_BARU => 'Pemasangan Baru',
            self::RELOKASI => 'Relokasi',
        };
    }
}