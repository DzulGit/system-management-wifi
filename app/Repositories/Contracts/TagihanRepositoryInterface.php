<?php

namespace App\Repositories\Contracts;

use App\Models\Tagihan;

interface TagihanRepositoryInterface
{
    public function create(array $data): Tagihan;

    public function find(int $id): ?Tagihan;
}