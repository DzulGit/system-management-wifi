<?php

namespace Database\Factories;

use App\Models\PaketInternet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaketInternetFactory extends Factory
{
    protected $model = PaketInternet::class;

    public function definition(): array
    {
        return [
            'nama_paket' => fake()->randomElement(['Paket Hemat', 'Paket Reguler', 'Paket Premium']),
            'kecepatan_mbps' => fake()->randomElement([10, 20, 30, 50, 100]),
            'harga' => fake()->randomElement([150000, 250000, 350000, 500000]),
            'deskripsi' => fake()->sentence(),
            'status_aktif' => true,
        ];
    }
}