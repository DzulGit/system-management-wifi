<?php

namespace App\Http\Controllers\Api\Teknisi;

use App\Enums\HasilSurveyEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanLayanan\HasilSurveyRequest;
use App\Models\JadwalSurvey;
use App\Repositories\Contracts\JadwalSurveyRepositoryInterface;
use App\Services\JadwalSurveyService;
use Illuminate\Http\Request;

class JadwalSurveyController extends Controller
{
    public function __construct(
        private readonly JadwalSurveyRepositoryInterface $jadwalSurveyRepository,
        private readonly JadwalSurveyService $jadwalSurveyService,
    ) {}

    public function index(Request $request)
    {
        return response()->json([
            'data' => $this->jadwalSurveyRepository->paginateMilikTeknisiBelumSelesai($request->user()->id),
        ]);
    }

    public function show(Request $request, JadwalSurvey $jadwalSurvey)
    {
        abort_unless($jadwalSurvey->admin_id === $request->user()->id, 403);

        $jadwal = $this->jadwalSurveyRepository->find($jadwalSurvey->id, ['permohonanLayanan.pelanggan']);

        return response()->json(['data' => $jadwal]);
    }

    public function isiHasil(HasilSurveyRequest $request, JadwalSurvey $jadwalSurvey)
    {
        // Teknisi hanya boleh isi hasil jadwal miliknya sendiri
        abort_unless($jadwalSurvey->admin_id === $request->user()->id, 403);

        $data = $request->validated();

        $jadwal = $this->jadwalSurveyService->isiHasil(
            $jadwalSurvey,
            HasilSurveyEnum::from($data['hasil']),
            $data['catatan'] ?? null,
            $request->user(),
        );

        return response()->json(['data' => $jadwal]);
    }
}