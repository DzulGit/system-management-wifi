<?php

namespace App\Services;

use App\Enums\HasilPemasanganEnum;
use App\Enums\StatusPermohonanEnum;
use App\Models\Admin;
use App\Models\JadwalPemasangan;
use App\Models\LayananInternet;
use App\Models\PermohonanLayanan;
use App\Repositories\Contracts\JadwalPemasanganRepositoryInterface;
use Illuminate\Support\Facades\DB;

class JadwalPemasanganService
{
    public function __construct(
        private readonly JadwalPemasanganRepositoryInterface $jadwalPemasanganRepository,
        private readonly PermohonanLayananService $permohonanLayananService,
        private readonly KonversiPermohonanService $konversiPermohonanService,
    ) {}

    /**
     * Dipakai untuk penjadwalan AWAL (status sudah PEMASANGAN, hasil dari survey
     * yang berhasil) MAUPUN re-jadwal setelah sempat DITUNDA di tahap pemasangan.
     */
    public function jadwalkan(
        PermohonanLayanan $permohonan,
        int $teknisiId,
        string $tanggalPemasangan,
        Admin $diprosesOleh,
    ): JadwalPemasangan {
        return DB::transaction(function () use ($permohonan, $teknisiId, $tanggalPemasangan, $diprosesOleh) {
            $jadwal = $this->jadwalPemasanganRepository->create([
                'permohonan_layanan_id' => $permohonan->id,
                'admin_id' => $teknisiId,
                'tanggal_pemasangan' => $tanggalPemasangan,
            ]);

            // Kalau baru resume dari DITUNDA, pastikan status kembali ke PEMASANGAN dulu
            if ($permohonan->status === StatusPermohonanEnum::DITUNDA) {
                $this->permohonanLayananService->ubahStatus(
                    $permohonan, StatusPermohonanEnum::PEMASANGAN, $diprosesOleh, 'Jadwal pemasangan ulang dibuat.'
                );
            }

            return $jadwal;
        });
    }

    /**
     * Teknisi mengisi hasil pemasangan. Kalau selesai, permohonan langsung
     * DIKONVERSI jadi layanan_internet resmi (lewat KonversiPermohonanService).
     * Kalau ditunda, status DITUNDA.
     */
    public function isiHasil(
        JadwalPemasangan $jadwal,
        HasilPemasanganEnum $hasil,
        ?string $alasanPenundaan,
        Admin $teknisi,
    ): JadwalPemasangan|LayananInternet {
        return DB::transaction(function () use ($jadwal, $hasil, $alasanPenundaan, $teknisi) {
            $jadwal = $this->jadwalPemasanganRepository->update($jadwal, [
                'hasil' => $hasil,
                'alasan_penundaan' => $alasanPenundaan,
            ]);

            $permohonan = $jadwal->permohonanLayanan;

            if ($hasil === HasilPemasanganEnum::SELESAI) {
                return $this->konversiPermohonanService->konversi($permohonan, $teknisi);
            }

            $this->permohonanLayananService->ubahStatus(
                $permohonan, StatusPermohonanEnum::DITUNDA, $teknisi, $alasanPenundaan ?? 'Pemasangan tertunda.'
            );
            $permohonan->update(['alasan_ditunda' => $alasanPenundaan]);

            return $jadwal;
        });
    }
}