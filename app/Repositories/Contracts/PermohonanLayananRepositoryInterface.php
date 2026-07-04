<?php

namespace App\Repositories\Contracts;

use App\Filters\PermohonanLayananFilter;
use App\Models\PermohonanLayanan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PermohonanLayananRepositoryInterface
{
    public function create(array $data): PermohonanLayanan;

    public function update(PermohonanLayanan $permohonan, array $data): PermohonanLayanan;

    public function find(int $id, array $with = []): ?PermohonanLayanan;

    public function paginate(PermohonanLayananFilter $filter, int $perPage = 20): LengthAwarePaginator;
}