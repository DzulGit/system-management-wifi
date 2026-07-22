<?php

namespace App\Repositories\Eloquent;

use App\Models\JadwalPemasangan;
use App\Repositories\Contracts\JadwalPemasanganRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JadwalPemasanganRepository implements JadwalPemasanganRepositoryInterface
{
    public function create(array $data): JadwalPemasangan
    {
        return JadwalPemasangan::create($data);
    }

    public function update(JadwalPemasangan $jadwal, array $data): JadwalPemasangan
    {
        $jadwal->update($data);

        return $jadwal->fresh();
    }

    public function find(int $id, array $with = []): ?JadwalPemasangan
    {
        return JadwalPemasangan::with($with)->find($id);
    }

    public function paginateMilikTeknisiBelumSelesai(int $teknisiId, int $perPage = 20): LengthAwarePaginator
    {
        return JadwalPemasangan::where('admin_id', $teknisiId)
            ->whereNull('hasil')
            ->with('permohonanLayanan.pelanggan')
            ->orderBy('tanggal_pemasangan')
            ->paginate($perPage);
    }
}