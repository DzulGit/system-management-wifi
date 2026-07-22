<?php

namespace App\Services;

use App\Models\Pelanggan;

class AktivasiAkunPelangganService
{
    public function __construct(
        private readonly GeneratorNomorService $generatorNomor,
    ) {}

    /**
     * Generate nomor_pelanggan HANYA jika pelanggan belum pernah punya layanan aktif
     * sebelumnya (nomor_pelanggan masih null). Idempotent — aman dipanggil berkali-kali
     * meski pelanggan tsb menambah layanan kedua/ketiga di kemudian hari.
     */
    public function aktivasiJikaLayananPertama(Pelanggan $pelanggan): Pelanggan
    {
        if ($pelanggan->nomor_pelanggan !== null) {
            return $pelanggan;
        }

        $nomorPelanggan = $this->generatorNomor->generate(
            Pelanggan::class,
            'nomor_pelanggan',
            'PLG'
        );

        $pelanggan->update(['nomor_pelanggan' => $nomorPelanggan]);

        return $pelanggan->fresh();
    }
}