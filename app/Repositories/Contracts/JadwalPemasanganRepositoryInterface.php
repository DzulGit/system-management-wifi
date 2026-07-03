<?php

namespace App\Repositories\Contracts;

use App\Models\JadwalPemasangan;

interface JadwalPemasanganRepositoryInterface
{
    public function create(array $data): JadwalPemasangan;

    public function update(JadwalPemasangan $jadwal, array $data): JadwalPemasangan;
}