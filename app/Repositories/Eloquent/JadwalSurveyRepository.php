<?php

namespace App\Repositories\Eloquent;

use App\Models\JadwalSurvey;
use App\Repositories\Contracts\JadwalSurveyRepositoryInterface;

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
}