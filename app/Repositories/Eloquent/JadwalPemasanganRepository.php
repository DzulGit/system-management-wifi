<?php

namespace App\Repositories\Eloquent;

use App\Models\JadwalPemasangan;
use App\Repositories\Contracts\JadwalPemasanganRepositoryInterface;

class JadwalPemasanganRepository implements JadwalPemasanganRepositoryInterface
{
    public function create(array $data): JadwalPemasangan
    {
        return JadwalPemasangan::create($data);
    }

    public function update(JadwalPemasangan $jadwal, array $data): JadwalPemasangan
    {
        $jadwal->update($data);

        return $jadwal->fresh();
    }
}