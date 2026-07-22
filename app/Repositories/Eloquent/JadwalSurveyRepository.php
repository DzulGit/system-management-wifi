<?php

namespace App\Repositories\Eloquent;

use App\Models\JadwalSurvey;
use App\Repositories\Contracts\JadwalSurveyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JadwalSurveyRepository implements JadwalSurveyRepositoryInterface
{
    public function create(array $data): JadwalSurvey
    {
        return JadwalSurvey::create($data);
    }

    public function update(JadwalSurvey $jadwal, array $data): JadwalSurvey
    {
        $jadwal->update($data);

        return $jadwal->fresh();
    }

    public function find(int $id, array $with = []): ?JadwalSurvey
    {
        return JadwalSurvey::with($with)->find($id);
    }

    public function paginateMilikTeknisiBelumSelesai(int $teknisiId, int $perPage = 20): LengthAwarePaginator
    {
        return JadwalSurvey::where('admin_id', $teknisiId)
            ->whereNull('hasil')
            ->with('permohonanLayanan.pelanggan')
            ->orderBy('tanggal_survey')
            ->paginate($perPage);
    }
}