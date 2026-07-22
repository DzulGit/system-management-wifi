<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\JadwalSurvey;
use App\Models\PermohonanLayanan;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalSurveyFactory extends Factory
{
    protected $model = JadwalSurvey::class;

    public function definition(): array
    {
        return [
            'permohonan_layanan_id' => PermohonanLayanan::factory(),
            'admin_id' => Admin::factory()->teknisi(),
            'tanggal_survey' => now()->addDays(2)->toDateString(),
            'hasil' => null,
        ];
    }
}