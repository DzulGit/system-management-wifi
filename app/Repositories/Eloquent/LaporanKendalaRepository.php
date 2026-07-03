<?php

namespace App\Repositories\Eloquent;

use App\Models\LaporanKendala;
use App\Repositories\Contracts\LaporanKendalaRepositoryInterface;

class LaporanKendalaRepository implements LaporanKendalaRepositoryInterface
{
    public function create(array $data): LaporanKendala
    {
        return LaporanKendala::create($data);
    }

    public function update(LaporanKendala $laporan, array $data): LaporanKendala
    {
        $laporan->update($data);

        return $laporan->fresh();
    }

    public function find(int $id): ?LaporanKendala
    {
        return LaporanKendala::find($id);
    }
}