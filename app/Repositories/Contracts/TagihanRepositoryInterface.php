<?php

namespace App\Repositories\Contracts;

use App\Filters\TagihanFilter;
use App\Models\Tagihan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagihanRepositoryInterface
{
    public function create(array $data): Tagihan;

    public function find(int $id, array $with = []): ?Tagihan;

    /** Dipakai Keuangan — lihat semua tagihan. */
    public function paginateSemua(TagihanFilter $filter, int $perPage = 20): LengthAwarePaginator;

    /** Dipakai Pelanggan — hanya tagihan dari layanan miliknya sendiri. */
    public function paginateUntukPelanggan(int $pelangganId, TagihanFilter $filter, int $perPage = 20): LengthAwarePaginator;
}