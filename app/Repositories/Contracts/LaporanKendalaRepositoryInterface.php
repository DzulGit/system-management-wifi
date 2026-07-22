<?php

namespace App\Repositories\Contracts;

use App\Filters\LaporanKendalaFilter;
use App\Models\LaporanKendala;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LaporanKendalaRepositoryInterface
{
    public function create(array $data): LaporanKendala;

    public function update(LaporanKendala $laporan, array $data): LaporanKendala;

    public function find(int $id, array $with = []): ?LaporanKendala;

    /** Dipakai Operasional — lihat semua laporan. */
    public function paginateSemua(LaporanKendalaFilter $filter, int $perPage = 20): LengthAwarePaginator;

    /** Dipakai Pelanggan — hanya laporan milik layanan miliknya sendiri. */
    public function paginateUntukPelanggan(int $pelangganId, LaporanKendalaFilter $filter, int $perPage = 20): LengthAwarePaginator;

    /** Dipakai Teknisi — hanya laporan yang ditugaskan ke dirinya. */
    public function paginateUntukTeknisi(int $teknisiId, LaporanKendalaFilter $filter, int $perPage = 20): LengthAwarePaginator;
}