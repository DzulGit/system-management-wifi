<?php

namespace Tests\Unit\Repositories;

use App\Enums\HasilSurveyEnum;
use App\Models\Admin;
use App\Models\JadwalSurvey;
use App\Models\PermohonanLayanan;
use App\Repositories\Contracts\JadwalSurveyRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalSurveyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_paginate_hanya_mengembalikan_jadwal_milik_teknisi_yang_belum_selesai(): void
    {
        $teknisiA = Admin::factory()->teknisi()->create();
        $teknisiB = Admin::factory()->teknisi()->create();
        $permohonan = PermohonanLayanan::factory()->create();

        JadwalSurvey::factory()->create([
            'admin_id' => $teknisiA->id,
            'permohonan_layanan_id' => $permohonan->id,
            'hasil' => null,
        ]);
        JadwalSurvey::factory()->create([
            'admin_id' => $teknisiA->id,
            'permohonan_layanan_id' => $permohonan->id,
            'hasil' => HasilSurveyEnum::BERHASIL, // sudah selesai, tidak boleh ikut
        ]);
        JadwalSurvey::factory()->create([
            'admin_id' => $teknisiB->id,
            'permohonan_layanan_id' => $permohonan->id,
            'hasil' => null,
        ]);

        $hasil = app(JadwalSurveyRepositoryInterface::class)
            ->paginateMilikTeknisiBelumSelesai($teknisiA->id);

        $this->assertCount(1, $hasil->items());
        $this->assertEquals($teknisiA->id, $hasil->items()[0]->admin_id);
    }
}