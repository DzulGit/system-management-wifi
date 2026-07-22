<?php

namespace Tests\Feature\PermohonanLayanan;

use App\Enums\StatusPermohonanEnum;
use App\Models\Admin;
use App\Models\PermohonanLayanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AlurPemasanganBaruTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test end-to-end: DITERIMA -> jadwalkan survey -> hasil survey berhasil
     * -> jadwalkan pemasangan -> hasil pemasangan selesai -> layanan AKTIF.
     *
     * Catatan: pakai Sanctum::actingAs() (bukan Bearer token manual via header)
     * karena test ini berpindah identitas (operasional <-> teknisi) beberapa kali
     * dalam satu test method. Guard Sanctum bisa "nyangkut" di user pertama kalau
     * switch identitas cuma lewat header Authorization biasa.
     */
    public function test_alur_lengkap_dari_diterima_sampai_layanan_aktif(): void
    {
        $operasional = Admin::factory()->operasional()->create();
        $teknisi = Admin::factory()->teknisi()->create();

        $permohonan = PermohonanLayanan::factory()->create([
            'status' => StatusPermohonanEnum::DITERIMA,
        ]);
        $permohonan->pelanggan()->update(['nomor_pelanggan' => null]);

        // 1. Operasional jadwalkan survey
        Sanctum::actingAs($operasional);

        $jadwalSurvey = $this->postJson("/api/admin/operasional/permohonan-layanan/{$permohonan->id}/jadwalkan-survey", [
            'admin_id' => $teknisi->id,
            'tanggal_survey' => now()->addDay()->toDateString(),
        ])->assertCreated()->json('data');

        $this->assertDatabaseHas('permohonan_layanan', [
            'id' => $permohonan->id,
            'status' => StatusPermohonanEnum::DIJADWALKAN->value,
        ]);

        // 2. Teknisi isi hasil survey: berhasil
        Sanctum::actingAs($teknisi);

        $this->patchJson("/api/admin/teknisi/jadwal-survey/{$jadwalSurvey['id']}/hasil", [
            'hasil' => 'berhasil',
        ])->assertOk();

        $this->assertDatabaseHas('permohonan_layanan', [
            'id' => $permohonan->id,
            'status' => StatusPermohonanEnum::PEMASANGAN->value,
        ]);

        // 3. Operasional jadwalkan pemasangan
        Sanctum::actingAs($operasional);

        $jadwalPemasangan = $this->postJson("/api/admin/operasional/permohonan-layanan/{$permohonan->id}/jadwalkan-pemasangan", [
            'admin_id' => $teknisi->id,
            'tanggal_pemasangan' => now()->addDays(3)->toDateString(),
        ])->assertCreated()->json('data');

        // 4. Teknisi isi hasil pemasangan: selesai -> trigger konversi
        Sanctum::actingAs($teknisi);

        $responseKonversi = $this->patchJson("/api/admin/teknisi/jadwal-pemasangan/{$jadwalPemasangan['id']}/hasil", [
            'hasil' => 'selesai',
        ]);

        $responseKonversi->assertOk();

        $this->assertDatabaseHas('permohonan_layanan', [
            'id' => $permohonan->id,
            'status' => StatusPermohonanEnum::DIKONVERSI->value,
        ]);
        $this->assertDatabaseHas('layanan_internet', [
            'permohonan_layanan_id' => $permohonan->id,
            'status' => 'aktif',
        ]);

        $permohonan->pelanggan->refresh();
        $this->assertNotNull($permohonan->pelanggan->nomor_pelanggan);
    }

    public function test_survey_kendala_membuat_status_ditunda(): void
    {
        $operasional = Admin::factory()->operasional()->create();
        $teknisi = Admin::factory()->teknisi()->create();

        $permohonan = PermohonanLayanan::factory()->create([
            'status' => StatusPermohonanEnum::DITERIMA,
        ]);

        Sanctum::actingAs($operasional);

        $jadwalSurvey = $this->postJson("/api/admin/operasional/permohonan-layanan/{$permohonan->id}/jadwalkan-survey", [
            'admin_id' => $teknisi->id,
            'tanggal_survey' => now()->addDay()->toDateString(),
        ])->json('data');

        Sanctum::actingAs($teknisi);

        $this->patchJson("/api/admin/teknisi/jadwal-survey/{$jadwalSurvey['id']}/hasil", [
            'hasil' => 'kendala',
            'catatan' => 'Lokasi belum terjangkau ODP.',
        ])->assertOk();

        $this->assertDatabaseHas('permohonan_layanan', [
            'id' => $permohonan->id,
            'status' => StatusPermohonanEnum::DITUNDA->value,
            'alasan_ditunda' => 'Lokasi belum terjangkau ODP.',
        ]);
    }

    public function test_teknisi_tidak_bisa_isi_hasil_jadwal_survey_milik_teknisi_lain(): void
    {
        $operasional = Admin::factory()->operasional()->create();
        $teknisiA = Admin::factory()->teknisi()->create();
        $teknisiB = Admin::factory()->teknisi()->create();

        $permohonan = PermohonanLayanan::factory()->create([
            'status' => StatusPermohonanEnum::DITERIMA,
        ]);

        Sanctum::actingAs($operasional);

        $jadwalSurvey = $this->postJson("/api/admin/operasional/permohonan-layanan/{$permohonan->id}/jadwalkan-survey", [
            'admin_id' => $teknisiA->id,
            'tanggal_survey' => now()->addDay()->toDateString(),
        ])->json('data');

        Sanctum::actingAs($teknisiB);

        $this->patchJson("/api/admin/teknisi/jadwal-survey/{$jadwalSurvey['id']}/hasil", [
            'hasil' => 'berhasil',
        ])->assertStatus(403);
    }
}