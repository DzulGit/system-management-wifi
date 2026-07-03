<?php

namespace App\Repositories\Contracts;

use App\Models\JadwalSurvey;

interface JadwalSurveyRepositoryInterface
{
    public function create(array $data): JadwalSurvey;

    public function update(JadwalSurvey $jadwal, array $data): JadwalSurvey;
}