<?php

namespace App\Repositories\Contracts;

use App\Models\LayananInternet;

interface LayananInternetRepositoryInterface
{
    public function create(array $data): LayananInternet;

    public function update(LayananInternet $layanan, array $data): LayananInternet;

    public function find(int $id): ?LayananInternet;
}