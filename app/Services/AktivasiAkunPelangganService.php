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

        // Password default = nomor_pelanggan, di-hash otomatis lewat cast
        // 'password' => 'hashed' di model Pelanggan (JANGAN Hash::make() manual
        // di sini, nanti ke-hash dua kali dan login akan selalu gagal).
        // Ditaruh di sini (bukan di model event) karena nomor_pelanggan baru
        // benar-benar ada di titik ini, bukan saat Pelanggan::create() awal
        // di PendaftaranService.
        $pelanggan->update([
            'nomor_pelanggan' => $nomorPelanggan,
            'password' => $nomorPelanggan,
        ]);

        return $pelanggan->fresh();
    }
}