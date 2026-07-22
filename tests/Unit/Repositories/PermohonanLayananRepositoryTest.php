<?php

namespace Tests\Unit\Repositories;

use App\Enums\JenisPermohonanEnum;
use App\Enums\StatusPermohonanEnum;
use App\Enums\TipePaketEnum;
use App\Models\PaketInternet;
use App\Models\Pelanggan;
use App\Models\PermohonanLayanan;
use App\Repositories\Contracts\PermohonanLayananRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermohonanLayananRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_menyimpan_data_ke_database(): void
    {
        $pelanggan = Pelanggan::factory()->create();
        $paket = PaketInternet::factory()->create();

        $hasil = app(PermohonanLayananRepositoryInterface::class)->create([
            'nomor_permohonan' => 'PMH999999',
            'pelanggan_id' => $pelanggan->id,
            'jenis_permohonan' => JenisPermohonanEnum::PEMASANGAN_BARU,
            'paket_internet_id' => $paket->id,
            'tipe_paket' => TipePaketEnum::REGULER,
            'alamat_pemasangan' => 'Jl. Testing No. 1',
            'rt' => '001',
            'rw' => '002',
            'kode_pos' => '50000',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'status' => StatusPermohonanEnum::MENUNGGU_VERIFIKASI,
        ]);

        $this->assertDatabaseHas('permohonan_layanan', ['id' => $hasil->id, 'nomor_permohonan' => 'PMH999999']);
    }

    public function test_find_dengan_eager_load_relasi(): void
    {
        $permohonan = PermohonanLayanan::factory()->create();

        $hasil = app(PermohonanLayananRepositoryInterface::class)
            ->find($permohonan->id, ['pelanggan']);

        $this->assertTrue($hasil->relationLoaded('pelanggan'));
    }

    public function test_find_mengembalikan_null_kalau_tidak_ada(): void
    {
        $hasil = app(PermohonanLayananRepositoryInterface::class)->find(999999);

        $this->assertNull($hasil);
    }

    public function test_update_mengubah_data_dan_mengembalikan_versi_terbaru(): void
    {
        $permohonan = PermohonanLayanan::factory()->create(['alasan_ditolak' => null]);

        $hasil = app(PermohonanLayananRepositoryInterface::class)
            ->update($permohonan, ['alasan_ditolak' => 'Data tidak lengkap']);

        $this->assertEquals('Data tidak lengkap', $hasil->alasan_ditolak);
        $this->assertDatabaseHas('permohonan_layanan', [
            'id' => $permohonan->id,
            'alasan_ditolak' => 'Data tidak lengkap',
        ]);
    }
}