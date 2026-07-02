<?php

namespace App\Enums;

enum StatusPerangkatEnum: string
{
    case TERPASANG = 'terpasang';
    case DILEPAS = 'dilepas';
    case RUSAK = 'rusak';
}