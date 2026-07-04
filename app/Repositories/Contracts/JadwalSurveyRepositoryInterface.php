<?php

namespace App\Repositories\Contracts;

use App\Models\JadwalSurvey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JadwalSurveyRepositoryInterface
{
    public function create(array $data): JadwalSurvey;

    public function update(JadwalSurvey $jadwal, array $data): JadwalSurvey;

    public function find(int $id, array $with = []): ?JadwalSurvey;

    /** Jadwal milik 1 teknisi yang belum diisi hasilnya (fixed scope, bukan filter opsional). */
    public function paginateMilikTeknisiBelumSelesai(int $teknisiId, int $perPage = 20): LengthAwarePaginator;
}