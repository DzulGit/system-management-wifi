<?php

namespace App\Enums;

enum JenisPerubahanPaketEnum: string
{
    case UPGRADE = 'upgrade';
    case DOWNGRADE = 'downgrade';
}