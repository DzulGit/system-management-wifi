<?php

namespace App\Repositories\Eloquent;

use App\Models\PermohonanLayanan;
use App\Repositories\Contracts\PermohonanLayananRepositoryInterface;

class PermohonanLayananRepository implements PermohonanLayananRepositoryInterface
{
    public function create(array $data): PermohonanLayanan
    {
        return PermohonanLayanan::create($data);
    }

    public function update(PermohonanLayanan $permohonan, array $data): PermohonanLayanan
    {
        $permohonan->update($data);

        return $permohonan->fresh();
    }

    public function find(int $id): ?PermohonanLayanan
    {
        return PermohonanLayanan::find($id);
    }
}