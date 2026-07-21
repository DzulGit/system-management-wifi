<?php

namespace App\Repositories\Eloquent;

use App\Models\TimTeknisi;
use App\Repositories\Contracts\TimTeknisiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TimTeknisiRepository implements TimTeknisiRepositoryInterface
{
    public function create(array $data): TimTeknisi
    {
        return DB::transaction(function () use ($data) {
            $tim = TimTeknisi::create(['nama_tim' => $data['nama_tim']]);
            $tim->anggota()->sync($data['anggota_ids']);

            return $tim->load('anggota');
        });
    }

    public function update(TimTeknisi $tim, array $data): TimTeknisi
    {
        return DB::transaction(function () use ($tim, $data) {
            $tim->update(collect($data)->only(['nama_tim', 'status_aktif'])->toArray());

            if (isset($data['anggota_ids'])) {
                $tim->anggota()->sync($data['anggota_ids']);
            }

            return $tim->fresh()->load('anggota');
        });
    }

    public function find(int $id): ?TimTeknisi
    {
        return TimTeknisi::with('anggota')->find($id);
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return TimTeknisi::with('anggota')->latest()->paginate($perPage);
    }

    public function listAktif(): Collection
    {
        return TimTeknisi::where('status_aktif', true)->with('anggota')->orderBy('nama_tim')->get();
    }
}