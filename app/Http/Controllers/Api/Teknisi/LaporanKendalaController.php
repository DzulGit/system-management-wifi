<?php

namespace App\Http\Controllers\Api\Teknisi;

use App\Filters\LaporanKendalaFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanKendala\SelesaikanLaporanRequest;
use App\Models\LaporanKendala;
use App\Repositories\Contracts\LaporanKendalaRepositoryInterface;
use App\Services\LaporanKendalaService;
use Illuminate\Http\Request;

class LaporanKendalaController extends Controller
{
    public function __construct(
        private readonly LaporanKendalaRepositoryInterface $laporanKendalaRepository,
        private readonly LaporanKendalaService $laporanKendalaService,
    ) {}

    public function index(Request $request, LaporanKendalaFilter $filter)
    {
        return response()->json([
            'data' => $this->laporanKendalaRepository->paginateUntukTeknisi($request->user()->id, $filter),
        ]);
    }

    public function show(Request $request, LaporanKendala $laporanKendala)
    {
        abort_unless($laporanKendala->ditugaskan_ke === $request->user()->id, 403);

        $laporan = $this->laporanKendalaRepository->find($laporanKendala->id, ['layananInternet.pelanggan']);

        return response()->json(['data' => $laporan]);
    }

    public function selesaikan(SelesaikanLaporanRequest $request, LaporanKendala $laporanKendala)
    {
        $this->authorize('selesaikan', $laporanKendala);
        abort_unless($laporanKendala->ditugaskan_ke === $request->user()->id, 403);

        $laporan = $this->laporanKendalaService->selesaikan(
            $laporanKendala,
            $request->validated('hasil_penanganan'),
        );

        return response()->json(['data' => $laporan]);
    }
}