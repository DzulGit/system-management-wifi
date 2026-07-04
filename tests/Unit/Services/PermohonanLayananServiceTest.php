<?php

namespace Tests\Unit\Services;

use App\Enums\StatusPermohonanEnum;
use App\Exceptions\TransisiStatusTidakValidException;
use App\Models\PermohonanLayanan;
use App\Services\PermohonanLayananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermohonanLayananServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transisi_status_valid_berhasil_dan_tercatat_di_riwayat(): void
    {
        $permohonan = PermohonanLayanan::factory()->create([
            'status' => StatusPermohonanEnum::MENUNGGU_VERIFIKASI,
        ]);

        $hasil = app(PermohonanLayananService::class)
            ->ubahStatus($permohonan, StatusPermohonanEnum::DITERIMA);

        $this->assertEquals(StatusPermohonanEnum::DITERIMA, $hasil->status);
        $this->assertDatabaseHas('riwayat_status_permohonan', [
            'permohonan_layanan_id' => $permohonan->id,
            'status_sebelumnya' => 'MENUNGGU_VERIFIKASI',
            'status_sesudahnya' => 'DITERIMA',
        ]);
    }

    public function test_transisi_status_meloncat_dilempar_exception(): void
    {
        $permohonan = PermohonanLayanan::factory()->create([
            'status' => StatusPermohonanEnum::MENUNGGU_VERIFIKASI,
        ]);

        $this->expectException(TransisiStatusTidakValidException::class);

        // Tidak boleh loncat langsung ke DIKONVERSI tanpa melalui tahap survey/pemasangan
        app(PermohonanLayananService::class)
            ->ubahStatus($permohonan, StatusPermohonanEnum::DIKONVERSI);
    }

    public function test_status_akhir_ditolak_tidak_bisa_diubah_lagi(): void
    {
        $permohonan = PermohonanLayanan::factory()->create([
            'status' => StatusPermohonanEnum::DITOLAK,
        ]);

        $this->expectException(TransisiStatusTidakValidException::class);

        app(PermohonanLayananService::class)
            ->ubahStatus($permohonan, StatusPermohonanEnum::DITERIMA);
    }

    public function test_riwayat_tidak_tercatat_kalau_transisi_gagal(): void
    {
        $permohonan = PermohonanLayanan::factory()->create([
            'status' => StatusPermohonanEnum::DITOLAK,
        ]);

        try {
            app(PermohonanLayananService::class)
                ->ubahStatus($permohonan, StatusPermohonanEnum::DITERIMA);
        } catch (TransisiStatusTidakValidException) {
            // diharapkan
        }

        $this->assertDatabaseCount('riwayat_status_permohonan', 0);
    }
}