<?php

namespace App\Repositories\Eloquent;

use App\Models\LayananInternet;
use App\Repositories\Contracts\LayananInternetRepositoryInterface;

class LayananInternetRepository implements LayananInternetRepositoryInterface
{
    public function create(array $data): LayananInternet
    {
        return LayananInternet::create($data);
    }

    public function update(LayananInternet $layanan, array $data): LayananInternet
    {
        $layanan->update($data);

        return $layanan->fresh();
    }

    public function find(int $id): ?LayananInternet
    {
        return LayananInternet::find($id);
    }
}