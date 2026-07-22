<?php

namespace Database\Factories;

use App\Enums\StatusLaporanEnum;
use App\Models\LaporanKendala;
use App\Models\LayananInternet;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaporanKendalaFactory extends Factory
{
    protected $model = LaporanKendala::class;

    public function definition(): array
    {
        return [
            'nomor_laporan' => 'LPR'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'layanan_internet_id' => LayananInternet::factory(),
            'kategori_kendala' => fake()->randomElement(['Internet Lambat', 'Tidak Ada Sinyal', 'Perangkat Rusak']),
            'deskripsi' => fake()->sentence(),
            'status' => StatusLaporanEnum::MENUNGGU,
        ];
    }
}