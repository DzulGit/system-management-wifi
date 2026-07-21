<?php

namespace App\Repositories\Contracts;

use App\Models\TimTeknisi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TimTeknisiRepositoryInterface
{
    public function create(array $data): TimTeknisi;

    public function update(TimTeknisi $tim, array $data): TimTeknisi;

    public function find(int $id): ?TimTeknisi;

    public function paginate(int $perPage = 20): LengthAwarePaginator;

    /** Dipakai dropdown Operasional saat jadwalkan kerja — hanya tim aktif. */
    public function listAktif(): \Illuminate\Database\Eloquent\Collection;
}