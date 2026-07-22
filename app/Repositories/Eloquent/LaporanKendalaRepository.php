<?php

namespace App\Repositories\Eloquent;

use App\Filters\LaporanKendalaFilter;
use App\Models\LaporanKendala;
use App\Repositories\Contracts\LaporanKendalaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LaporanKendalaRepository implements LaporanKendalaRepositoryInterface
{
    public function create(array $data): LaporanKendala
    {
        return LaporanKendala::create($data);
    }

    public function update(LaporanKendala $laporan, array $data): LaporanKendala
    {
        $laporan->update($data);

        return $laporan->fresh();
    }

    public function find(int $id, array $with = []): ?LaporanKendala
    {
        return LaporanKendala::with($with)->find($id);
    }

    public function paginateSemua(LaporanKendalaFilter $filter, int $perPage = 20): LengthAwarePaginator
    {
        $query = LaporanKendala::query()->with('layananInternet.pelanggan')->latest();

        return $filter->apply($query)->paginate($perPage);
    }

    public function paginateUntukPelanggan(int $pelangganId, LaporanKendalaFilter $filter, int $perPage = 20): LengthAwarePaginator
    {
        $query = LaporanKendala::query()
            ->whereHas('layananInternet', fn ($q) => $q->where('pelanggan_id', $pelangganId))
            ->latest();

        return $filter->apply($query)->paginate($perPage);
    }

    public function paginateUntukTeknisi(int $teknisiId, LaporanKendalaFilter $filter, int $perPage = 20): LengthAwarePaginator
    {
        $query = LaporanKendala::query()
            ->where('ditugaskan_ke', $teknisiId)
            ->with('layananInternet.pelanggan')
            ->latest();

        return $filter->apply($query)->paginate($perPage);
    }
}