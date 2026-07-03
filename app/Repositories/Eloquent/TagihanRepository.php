<?php

namespace App\Repositories\Eloquent;

use App\Models\Tagihan;
use App\Repositories\Contracts\TagihanRepositoryInterface;

class TagihanRepository implements TagihanRepositoryInterface
{
    public function create(array $data): Tagihan
    {
        return Tagihan::create($data);
    }

    public function find(int $id): ?Tagihan
    {
        return Tagihan::find($id);
    }
}