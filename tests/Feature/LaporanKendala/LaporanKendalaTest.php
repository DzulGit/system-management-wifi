<?php

namespace Tests\Feature\LaporanKendala;

use App\Enums\StatusLaporanEnum;
use App\Models\Admin;
use App\Models\LaporanKendala;
use App\Models\LayananInternet;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanKendalaTest extends TestCase
{
    use RefreshDatabase;

    public function test_pelanggan_bisa_buat_laporan_untuk_layanan_miliknya(): void
    {
        $pelanggan = Pelanggan::factory()->sudahAktif()->create();
        $layanan = LayananInternet::factory()->create(['pelanggan_id' => $pelanggan->id]);

        $token = $pelanggan->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/pelanggan/laporan-kendala', [
                'layanan_internet_id' => $layanan->id,
                'kategori_kendala' => 'Internet Lambat',
                'deskripsi' => 'Internet lambat sejak pagi.',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('laporan_kendala', [
            'layanan_internet_id' => $layanan->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_pelanggan_tidak_bisa_buat_laporan_untuk_layanan_orang_lain(): void
    {
        $pelanggan = Pelanggan::factory()->sudahAktif()->create();
        $layananOrangLain = LayananInternet::factory()->create(); // pelanggan_id beda (default factory)

        $token = $pelanggan->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/pelanggan/laporan-kendala', [
                'layanan_internet_id' => $layananOrangLain->id,
                'kategori_kendala' => 'Internet Lambat',
                'deskripsi' => 'Coba akses punya orang lain.',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('layanan_internet_id');
    }

    public function test_operasional_bisa_teruskan_laporan_ke_teknisi(): void
    {
        $operasional = Admin::factory()->operasional()->create();
        $teknisi = Admin::factory()->teknisi()->create();

        $laporan = LaporanKendala::factory()->create([
            'status' => StatusLaporanEnum::DIPROSES,
        ]);

        $token = $operasional->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/admin/operasional/laporan-kendala/{$laporan->id}/teruskan-ke-teknisi", [
                'teknisi_id' => $teknisi->id,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('laporan_kendala', [
            'id' => $laporan->id,
            'status' => 'ditugaskan',
            'ditugaskan_ke' => $teknisi->id,
        ]);
    }

    public function test_teknisi_tidak_bisa_selesaikan_laporan_yang_bukan_miliknya(): void
    {
        $teknisiA = Admin::factory()->teknisi()->create();
        $teknisiB = Admin::factory()->teknisi()->create();

        $laporan = LaporanKendala::factory()->create([
            'status' => StatusLaporanEnum::DITUGASKAN,
            'ditugaskan_ke' => $teknisiA->id,
        ]);

        $token = $teknisiB->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/admin/teknisi/laporan-kendala/{$laporan->id}/selesaikan", [
                'hasil_penanganan' => 'Sudah diperbaiki.',
            ]);

        $response->assertStatus(403);
    }

    public function test_keuangan_tidak_bisa_akses_endpoint_operasional(): void
    {
        $keuangan = Admin::factory()->keuangan()->create();
        $laporan = LaporanKendala::factory()->create();

        $token = $keuangan->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/admin/operasional/laporan-kendala/{$laporan->id}");

        $response->assertStatus(403);
    }
}