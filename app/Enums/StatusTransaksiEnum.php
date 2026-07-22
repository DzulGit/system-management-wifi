<?php

namespace App\Enums;

// Status baris di tabel pembayaran (bisa beberapa percobaan per tagihan)
enum StatusTransaksiEnum: string
{
    case PENDING = 'pending';
    case BERHASIL = 'berhasil';
    case GAGAL = 'gagal';
}