<?php

namespace App\Repositories\Contracts;

use App\Models\LayananInternet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LayananInternetRepositoryInterface
{
    public function create(array $data): LayananInternet;

    public function update(LayananInternet $layanan, array $data): LayananInternet;

    public function find(int $id, array $with = []): ?LayananInternet;

    /** Dipakai Pelanggan — daftar layanan miliknya sendiri (tanpa filter dinamis, belum perlu). */
    public function paginateUntukPelanggan(int $pelangganId, int $perPage = 20): LengthAwarePaginator;
}