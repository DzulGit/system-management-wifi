<?php

namespace App\Services;

use App\Enums\JenisPermohonanEnum;
use App\Enums\StatusLayananEnum;
use App\Enums\StatusPermohonanEnum;
use App\Models\Admin;
use App\Models\LayananInternet;
use App\Models\PermohonanLayanan;
use App\Models\RiwayatRelokasi;
use App\Repositories\Contracts\LayananInternetRepositoryInterface;
use Illuminate\Support\Facades\DB;

class KonversiPermohonanService
{
    public function __construct(
        private readonly LayananInternetRepositoryInterface $layananInternetRepository,
        private readonly GeneratorNomorService $generatorNomor,
        private readonly PermohonanLayananService $permohonanLayananService,
        private readonly AktivasiAkunPelangganService $aktivasiAkunPelangganService,
    ) {}

    /**
     * Dipanggil saat Teknisi klik "Pemasangan Selesai".
     * Perilakunya beda tergantung jenis_permohonan — lihat docs/arsitektur/business-flow.md.
     */
    public function konversi(PermohonanLayanan $permohonan, ?Admin $diprosesOleh = null): LayananInternet
    {
        return DB::transaction(function () use ($permohonan, $diprosesOleh) {
            $layanan = match ($permohonan->jenis_permohonan) {
                JenisPermohonanEnum::PEMASANGAN_BARU => $this->konversiPemasanganBaru($permohonan),
                JenisPermohonanEnum::RELOKASI => $this->konversiRelokasi($permohonan),
            };

            $this->permohonanLayananService->ubahStatus(
                $permohonan,
                StatusPermohonanEnum::DIKONVERSI,
                $diprosesOleh,
                'Pemasangan selesai, permohonan dikonversi.'
            );

            return $layanan;
        });
    }

    private function konversiPemasanganBaru(PermohonanLayanan $permohonan): LayananInternet
    {
        $nomorLayanan = $this->generatorNomor->generate(
            LayananInternet::class,
            'nomor_layanan',
            'LYN'
        );

        $layanan = $this->layananInternetRepository->create([
            'nomor_layanan' => $nomorLayanan,
            'permohonan_layanan_id' => $permohonan->id,
            'pelanggan_id' => $permohonan->pelanggan_id,
            'paket_internet_id' => $permohonan->paket_internet_id,
            'tipe_paket' => $permohonan->tipe_paket,
            'nama_paket_custom' => $permohonan->nama_paket_custom,
            'kecepatan_custom_mbps' => $permohonan->kecepatan_custom_mbps,
            'harga_custom' => $permohonan->harga_custom,
            'alamat_pemasangan' => $permohonan->alamat_pemasangan,
            'rt' => $permohonan->rt,
            'rw' => $permohonan->rw,
            'kode_pos' => $permohonan->kode_pos,
            'latitude' => $permohonan->latitude,
            'longitude' => $permohonan->longitude,
            'status' => StatusLayananEnum::AKTIF,
            'tanggal_aktif' => now()->toDateString(),
        ]);

        // Generate nomor_pelanggan HANYA jika ini layanan pertama pelanggan tsb (idempotent)
        $this->aktivasiAkunPelangganService->aktivasiJikaLayananPertama($permohonan->pelanggan);

        return $layanan;
    }

    private function konversiRelokasi(PermohonanLayanan $permohonan): LayananInternet
    {
        /** @var LayananInternet $layanan */
        $layanan = $permohonan->layananDirelokasi;

        RiwayatRelokasi::create([
            'layanan_internet_id' => $layanan->id,
            'permohonan_layanan_id' => $permohonan->id,
            'alamat_lama' => $layanan->alamat_pemasangan,
            'rt_lama' => $layanan->rt,
            'rw_lama' => $layanan->rw,
            'kode_pos_lama' => $layanan->kode_pos,
            'latitude_lama' => $layanan->latitude,
            'longitude_lama' => $layanan->longitude,
            'alamat_baru' => $permohonan->alamat_pemasangan,
            'rt_baru' => $permohonan->rt,
            'rw_baru' => $permohonan->rw,
            'kode_pos_baru' => $permohonan->kode_pos,
            'latitude_baru' => $permohonan->latitude,
            'longitude_baru' => $permohonan->longitude,
            'tanggal_relokasi' => now()->toDateString(),
        ]);

        return $this->layananInternetRepository->update($layanan, [
            'alamat_pemasangan' => $permohonan->alamat_pemasangan,
            'rt' => $permohonan->rt,
            'rw' => $permohonan->rw,
            'kode_pos' => $permohonan->kode_pos,
            'latitude' => $permohonan->latitude,
            'longitude' => $permohonan->longitude,
        ]);
    }
}