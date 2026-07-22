<?php

namespace App\Enums;

enum StatusPermohonanEnum: string
{
    case MENUNGGU_VERIFIKASI = 'MENUNGGU_VERIFIKASI';
    case PERLU_REVISI = 'PERLU_REVISI';
    case DITERIMA = 'DITERIMA';
    case DITOLAK = 'DITOLAK';
    case DIJADWALKAN = 'DIJADWALKAN';
    case SURVEY = 'SURVEY';
    case PEMASANGAN = 'PEMASANGAN';
    case DITUNDA = 'DITUNDA';
    case DIKONVERSI = 'DIKONVERSI';

    /**
     * Peta transisi status yang valid (state machine).
     * Dipakai oleh PermohonanLayananService untuk validasi sebelum ubah status.
     */
    public function transisiValid(): array
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI => [self::PERLU_REVISI, self::DITERIMA, self::DITOLAK],
            self::PERLU_REVISI => [self::MENUNGGU_VERIFIKASI],
            self::DITERIMA => [self::DIJADWALKAN],
            self::DIJADWALKAN => [self::SURVEY],
            self::SURVEY => [self::DITUNDA, self::PEMASANGAN],
            self::PEMASANGAN => [self::DITUNDA, self::DIKONVERSI],
            self::DITUNDA => [self::SURVEY, self::PEMASANGAN], // kembali sesuai tahap sebelumnya
            self::DITOLAK, self::DIKONVERSI => [], // status akhir
        };
    }

    public function label(): string
    {
        return str_replace('_', ' ', ucwords(strtolower($this->value), '_'));
    }
}