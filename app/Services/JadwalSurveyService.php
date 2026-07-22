<?php

namespace App\Services;

use App\Enums\HasilSurveyEnum;
use App\Enums\StatusPermohonanEnum;
use App\Models\Admin;
use App\Models\JadwalSurvey;
use App\Models\PermohonanLayanan;
use App\Repositories\Contracts\JadwalSurveyRepositoryInterface;
use Illuminate\Support\Facades\DB;

class JadwalSurveyService
{
    public function __construct(
        private readonly JadwalSurveyRepositoryInterface $jadwalSurveyRepository,
        private readonly PermohonanLayananService $permohonanLayananService,
    ) {}

    /**
     * Dipakai untuk penjadwalan AWAL (dari status DITERIMA) MAUPUN re-jadwal
     * setelah sempat DITUNDA di tahap survey.
     */
    public function jadwalkan(
        PermohonanLayanan $permohonan,
        int $teknisiId,
        string $tanggalSurvey,
        Admin $diprosesOleh,
    ): JadwalSurvey {
        return DB::transaction(function () use ($permohonan, $teknisiId, $tanggalSurvey, $diprosesOleh) {
            $jadwal = $this->jadwalSurveyRepository->create([
                'permohonan_layanan_id' => $permohonan->id,
                'admin_id' => $teknisiId,
                'tanggal_survey' => $tanggalSurvey,
            ]);

            $statusTarget = $permohonan->status === StatusPermohonanEnum::DITERIMA
                ? StatusPermohonanEnum::DIJADWALKAN
                : StatusPermohonanEnum::SURVEY; // resume dari DITUNDA

            $this->permohonanLayananService->ubahStatus(
                $permohonan, $statusTarget, $diprosesOleh, 'Jadwal survey dibuat.'
            );

            return $jadwal;
        });
    }

    /**
     * Teknisi mengisi hasil survey. Kalau berhasil, status lanjut ke PEMASANGAN
     * (menunggu dijadwalkan Operasional). Kalau kendala, status DITUNDA.
     */
    public function isiHasil(
        JadwalSurvey $jadwal,
        HasilSurveyEnum $hasil,
        ?string $catatan,
        Admin $teknisi,
    ): JadwalSurvey {
        return DB::transaction(function () use ($jadwal, $hasil, $catatan, $teknisi) {
            $jadwal = $this->jadwalSurveyRepository->update($jadwal, [
                'hasil' => $hasil,
                'catatan' => $catatan,
            ]);

            $permohonan = $jadwal->permohonanLayanan;

            // Tandai proses survey resmi berjalan (DIJADWALKAN -> SURVEY).
            // Kalau statusnya sudah SURVEY (kasus resume dari DITUNDA), skip —
            // enum tidak izinkan transisi SURVEY -> SURVEY.
            if ($permohonan->status !== StatusPermohonanEnum::SURVEY) {
                $permohonan = $this->permohonanLayananService->ubahStatus(
                    $permohonan, StatusPermohonanEnum::SURVEY, $teknisi, 'Survey dilaksanakan.'
                );
            }

            if ($hasil === HasilSurveyEnum::BERHASIL) {
                $this->permohonanLayananService->ubahStatus(
                    $permohonan, StatusPermohonanEnum::PEMASANGAN, $teknisi, 'Survey berhasil, lanjut pemasangan.'
                );
            } else {
                $this->permohonanLayananService->ubahStatus(
                    $permohonan, StatusPermohonanEnum::DITUNDA, $teknisi, $catatan ?? 'Survey mengalami kendala.'
                );
                $permohonan->update(['alasan_ditunda' => $catatan]);
            }

            return $jadwal;
        });
    }
}