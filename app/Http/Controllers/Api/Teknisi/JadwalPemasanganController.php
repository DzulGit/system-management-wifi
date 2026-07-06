<?php

namespace App\Http\Controllers\Api\Teknisi;

use App\Enums\HasilPemasanganEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanLayanan\HasilPemasanganRequest;
use App\Models\JadwalPemasangan;
use App\Repositories\Contracts\JadwalPemasanganRepositoryInterface;
use App\Services\JadwalPemasanganService;
use Illuminate\Http\Request;

class JadwalPemasanganController extends Controller
{
    public function __construct(
        private readonly JadwalPemasanganRepositoryInterface $jadwalPemasanganRepository,
        private readonly JadwalPemasanganService $jadwalPemasanganService,
    ) {}

    public function index(Request $request)
    {
        return response()->json([
            'data' => $this->jadwalPemasanganRepository->paginateMilikTeknisiBelumSelesai($request->user()->id),
        ]);
    }

    public function show(Request $request, JadwalPemasangan $jadwalPemasangan)
    {
        abort_unless((int) $jadwalPemasangan->admin_id === (int) $request->user()->id, 403);

        $jadwal = $this->jadwalPemasanganRepository->find($jadwalPemasangan->id, ['permohonanLayanan.pelanggan']);

        return response()->json(['data' => $jadwal]);
    }

    /**
     * Kalau hasil = selesai, respons berisi LayananInternet yang baru terbentuk
     * (atau ter-update, kalau ini kasus relokasi).
     */
    public function isiHasil(HasilPemasanganRequest $request, JadwalPemasangan $jadwalPemasangan)
    {
        abort_unless((int) $jadwalPemasangan->admin_id === (int) $request->user()->id, 403);

        $data = $request->validated();

        $hasil = $this->jadwalPemasanganService->isiHasil(
            $jadwalPemasangan,
            HasilPemasanganEnum::from($data['hasil']),
            $data['alasan_penundaan'] ?? null,
            $request->user(),
        );

        return response()->json(['data' => $hasil]);
    }
}