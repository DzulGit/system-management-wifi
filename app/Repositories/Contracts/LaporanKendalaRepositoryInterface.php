<?php

namespace App\Repositories\Contracts;

use App\Models\LaporanKendala;

interface LaporanKendalaRepositoryInterface
{
    public function create(array $data): LaporanKendala;

    public function update(LaporanKendala $laporan, array $data): LaporanKendala;

    public function find(int $id): ?LaporanKendala;
}