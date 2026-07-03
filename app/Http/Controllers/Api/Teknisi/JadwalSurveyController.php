<?php

namespace App\Http\Controllers\Api\Teknisi;

use App\Enums\HasilSurveyEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanLayanan\HasilSurveyRequest;
use App\Models\JadwalSurvey;
use App\Services\JadwalSurveyService;
use Illuminate\Http\Request;

class JadwalSurveyController extends Controller
{
    public function __construct(
        private readonly JadwalSurveyService $jadwalSurveyService,
    ) {}

    /**
     * Daftar jadwal survey milik teknisi yang login, yang belum diisi hasilnya.
     */
    public function index(Request $request)
    {
        $jadwal = JadwalSurvey::where('admin_id', $request->user()->id)
            ->whereNull('hasil')
            ->with('permohonanLayanan.pelanggan')
            ->orderBy('tanggal_survey')
            ->paginate(20);

        return response()->json(['data' => $jadwal]);
    }

    public function show(Request $request, JadwalSurvey $jadwalSurvey)
    {
        abort_unless($jadwalSurvey->admin_id === $request->user()->id, 403);

        return response()->json(['data' => $jadwalSurvey->load('permohonanLayanan.pelanggan')]);
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