<?php

namespace App\Repositories\Contracts;

use App\Models\JadwalPemasangan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JadwalPemasanganRepositoryInterface
{
    public function create(array $data): JadwalPemasangan;

    public function update(JadwalPemasangan $jadwal, array $data): JadwalPemasangan;

    public function find(int $id, array $with = []): ?JadwalPemasangan;

    public function paginateMilikTeknisiBelumSelesai(int $teknisiId, int $perPage = 20): LengthAwarePaginator;
}