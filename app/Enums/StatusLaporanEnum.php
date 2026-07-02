<?php

namespace App\Enums;

enum StatusLaporanEnum: string
{
    case MENUNGGU = 'menunggu';
    case DIPROSES = 'diproses';
    case DITUGASKAN = 'ditugaskan';
    case SELESAI = 'selesai';
    case DITUTUP = 'ditutup';
}