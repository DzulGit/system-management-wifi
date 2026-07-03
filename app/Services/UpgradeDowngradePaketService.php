<?php

namespace App\Services;

use App\Enums\JenisPerubahanPaketEnum;
use App\Enums\TipePaketEnum;
use App\Models\Admin;
use App\Models\LayananInternet;
use App\Models\PaketInternet;
use App\Models\RiwayatPerubahanPaket;
use App\Repositories\Contracts\LayananInternetRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpgradeDowngradePaketService
{
    public function __construct(
        private readonly LayananInternetRepositoryInterface $layananInternetRepository,
    ) {}

    /**
     * Ubah paket layanan (reguler ATAU custom). `jenis_perubahan` (upgrade/downgrade)
     * ditentukan OTOMATIS dari perbandingan kecepatan lama vs baru — tidak diinput
     * manual oleh admin, supaya tidak ada human error salah label di riwayat.
     *
     * $data yang diharapkan:
     *  - tipe_paket: 'reguler' | 'custom'
     *  - paket_internet_id            (wajib jika reguler)
     *  - nama_paket_custom, kecepatan_custom_mbps, harga_custom  (wajib jika custom)
     */
    public function ubah(LayananInternet $layanan, array $data, Admin $admin): LayananInternet
    {
        return DB::transaction(function () use ($layanan, $data, $admin) {
            [$namaLama, $kecepatanLama, $hargaLama] = $this->paketSaatIni($layanan);

            $tipePaketBaru = TipePaketEnum::from($data['tipe_paket']);

            if ($tipePaketBaru === TipePaketEnum::REGULER) {
                $paketBaru = PaketInternet::findOrFail($data['paket_internet_id']);

                $namaBaru = $paketBaru->nama_paket;
                $kecepatanBaru = $paketBaru->kecepatan_mbps;
                $hargaBaru = $paketBaru->harga;

                $dataUpdate = [
                    'tipe_paket' => TipePaketEnum::REGULER,
                    'paket_internet_id' => $paketBaru->id,
                    'nama_paket_custom' => null,
                    'kecepatan_custom_mbps' => null,
                    'harga_custom' => null,
                ];
            } else {
                $namaBaru = $data['nama_paket_custom'];
                $kecepatanBaru = $data['kecepatan_custom_mbps'];
                $hargaBaru = $data['harga_custom'];

                $dataUpdate = [
                    'tipe_paket' => TipePaketEnum::CUSTOM,
                    'paket_internet_id' => null,
                    'nama_paket_custom' => $namaBaru,
                    'kecepatan_custom_mbps' => $kecepatanBaru,
                    'harga_custom' => $hargaBaru,
                ];
            }

            $jenisPerubahan = $kecepatanBaru >= $kecepatanLama
                ? JenisPerubahanPaketEnum::UPGRADE
                : JenisPerubahanPaketEnum::DOWNGRADE;

            RiwayatPerubahanPaket::create([
                'layanan_internet_id' => $layanan->id,
                'nama_paket_lama' => $namaLama,
                'kecepatan_lama_mbps' => $kecepatanLama,
                'harga_lama' => $hargaLama,
                'nama_paket_baru' => $namaBaru,
                'kecepatan_baru_mbps' => $kecepatanBaru,
                'harga_baru' => $hargaBaru,
                'jenis_perubahan' => $jenisPerubahan,
                'diubah_oleh' => $admin->id,
                'tanggal_perubahan' => now()->toDateString(),
            ]);

            return $this->layananInternetRepository->update($layanan, $dataUpdate);
        });
    }

    private function paketSaatIni(LayananInternet $layanan): array
    {
        if ($layanan->tipe_paket === TipePaketEnum::REGULER) {
            $paket = $layanan->paketInternet;

            return [$paket->nama_paket, $paket->kecepatan_mbps, $paket->harga];
        }

        return [$layanan->nama_paket_custom, $layanan->kecepatan_custom_mbps, $layanan->harga_custom];
    }
}