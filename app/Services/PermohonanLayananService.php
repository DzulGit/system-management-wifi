<?php

namespace App\Services;

use App\Enums\StatusPermohonanEnum;
use App\Exceptions\TransisiStatusTidakValidException;
use App\Models\Admin;
use App\Models\PermohonanLayanan;
use App\Models\RiwayatStatusPermohonan;
use App\Repositories\Contracts\PermohonanLayananRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PermohonanLayananService
{
    public function __construct(
        private readonly PermohonanLayananRepositoryInterface $permohonanLayananRepository,
        private readonly GeneratorNomorService $generatorNomor,
    ) {}

    /**
     * Buat permohonan baru (pemasangan_baru ATAU relokasi).
     * Status awal selalu MENUNGGU_VERIFIKASI.
     */
    public function buatPermohonan(array $data): PermohonanLayanan
    {
        return DB::transaction(function () use ($data) {
            $data['nomor_permohonan'] = $this->generatorNomor->generate(
                PermohonanLayanan::class,
                'nomor_permohonan',
                'PMH'
            );
            $data['status'] = StatusPermohonanEnum::MENUNGGU_VERIFIKASI;

            $permohonan = $this->permohonanLayananRepository->create($data);

            $this->catatRiwayat(
                $permohonan,
                null,
                StatusPermohonanEnum::MENUNGGU_VERIFIKASI,
                null,
                'Permohonan diajukan.'
            );

            return $permohonan;
        });
    }

    /**
     * Validasi transisi lewat StatusPermohonanEnum::transisiValid(), lalu eksekusi
     * perubahan status + catat riwayat dalam satu transaksi.
     *
     * @throws TransisiStatusTidakValidException
     */
    public function ubahStatus(
        PermohonanLayanan $permohonan,
        StatusPermohonanEnum $statusBaru,
        ?Admin $diubahOleh = null,
        ?string $catatan = null,
    ): PermohonanLayanan {
        $statusSekarang = $permohonan->status;

        if (! in_array($statusBaru, $statusSekarang->transisiValid(), true)) {
            throw new TransisiStatusTidakValidException(
                "Tidak bisa mengubah status dari {$statusSekarang->value} ke {$statusBaru->value}."
            );
        }

        return DB::transaction(function () use ($permohonan, $statusBaru, $statusSekarang, $diubahOleh, $catatan) {
            $permohonan = $this->permohonanLayananRepository->update($permohonan, [
                'status' => $statusBaru,
            ]);

            $this->catatRiwayat($permohonan, $statusSekarang, $statusBaru, $diubahOleh?->id, $catatan);

            return $permohonan;
        });
    }

    private function catatRiwayat(
        PermohonanLayanan $permohonan,
        ?StatusPermohonanEnum $sebelum,
        StatusPermohonanEnum $sesudah,
        ?int $diubahOleh,
        ?string $catatan,
    ): void {
        RiwayatStatusPermohonan::create([
            'permohonan_layanan_id' => $permohonan->id,
            'status_sebelumnya' => $sebelum?->value,
            'status_sesudahnya' => $sesudah->value,
            'diubah_oleh' => $diubahOleh,
            'catatan' => $catatan,
        ]);
    }
}