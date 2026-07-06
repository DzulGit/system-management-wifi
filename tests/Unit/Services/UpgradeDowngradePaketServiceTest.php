<?php

namespace Tests\Unit\Services;

use App\Enums\JenisPerubahanPaketEnum;
use App\Enums\TipePaketEnum;
use App\Models\Admin;
use App\Models\LayananInternet;
use App\Models\PaketInternet;
use App\Services\UpgradeDowngradePaketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpgradeDowngradePaketServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ganti_ke_paket_lebih_cepat_tercatat_sebagai_upgrade(): void
    {
        $paketLama = PaketInternet::factory()->create(['kecepatan_mbps' => 20, 'harga' => 200000]);
        $paketBaru = PaketInternet::factory()->create(['kecepatan_mbps' => 50, 'harga' => 400000]);
        $admin = Admin::factory()->operasional()->create();

        $layanan = LayananInternet::factory()->create([
            'tipe_paket' => TipePaketEnum::REGULER,
            'paket_internet_id' => $paketLama->id,
        ]);

        $hasil = app(UpgradeDowngradePaketService::class)->ubah($layanan, [
            'tipe_paket' => 'reguler',
            'paket_internet_id' => $paketBaru->id,
        ], $admin);

        $this->assertEquals($paketBaru->id, $hasil->paket_internet_id);
        $this->assertDatabaseHas('riwayat_perubahan_paket', [
            'layanan_internet_id' => $layanan->id,
            'jenis_perubahan' => JenisPerubahanPaketEnum::UPGRADE->value,
            'kecepatan_lama_mbps' => 20,
            'kecepatan_baru_mbps' => 50,
        ]);
    }

    public function test_ganti_ke_paket_lebih_lambat_tercatat_sebagai_downgrade(): void
    {
        $paketLama = PaketInternet::factory()->create(['kecepatan_mbps' => 50, 'harga' => 400000]);
        $paketBaru = PaketInternet::factory()->create(['kecepatan_mbps' => 20, 'harga' => 200000]);
        $admin = Admin::factory()->operasional()->create();

        $layanan = LayananInternet::factory()->create([
            'tipe_paket' => TipePaketEnum::REGULER,
            'paket_internet_id' => $paketLama->id,
        ]);

        app(UpgradeDowngradePaketService::class)->ubah($layanan, [
            'tipe_paket' => 'reguler',
            'paket_internet_id' => $paketBaru->id,
        ], $admin);

        $this->assertDatabaseHas('riwayat_perubahan_paket', [
            'layanan_internet_id' => $layanan->id,
            'jenis_perubahan' => JenisPerubahanPaketEnum::DOWNGRADE->value,
        ]);
    }

    public function test_ganti_dari_reguler_ke_custom(): void
    {
        $paketLama = PaketInternet::factory()->create(['kecepatan_mbps' => 20, 'harga' => 200000]);
        $admin = Admin::factory()->operasional()->create();

        $layanan = LayananInternet::factory()->create([
            'tipe_paket' => TipePaketEnum::REGULER,
            'paket_internet_id' => $paketLama->id,
        ]);

        $hasil = app(UpgradeDowngradePaketService::class)->ubah($layanan, [
            'tipe_paket' => 'custom',
            'nama_paket_custom' => 'Custom 100 Mbps',
            'kecepatan_custom_mbps' => 100,
            'harga_custom' => 700000,
        ], $admin);

        $this->assertEquals(TipePaketEnum::CUSTOM, $hasil->tipe_paket);
        $this->assertNull($hasil->paket_internet_id);
        $this->assertEquals('Custom 100 Mbps', $hasil->nama_paket_custom);
    }
}