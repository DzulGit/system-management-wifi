<?php

namespace App\Repositories\Contracts;

use App\Models\PermohonanLayanan;

interface PermohonanLayananRepositoryInterface
{
    public function create(array $data): PermohonanLayanan;

    public function update(PermohonanLayanan $permohonan, array $data): PermohonanLayanan;

    public function find(int $id): ?PermohonanLayanan;
}