<?php

namespace App\Http\Controllers\Api\Teknisi;

use App\Enums\HasilPemasanganEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanLayanan\HasilPemasanganRequest;
use App\Models\JadwalPemasangan;
use App\Services\JadwalPemasanganService;
use Illuminate\Http\Request;

class JadwalPemasanganController extends Controller
{
    public function __construct(
        private readonly JadwalPemasanganService $jadwalPemasanganService,
    ) {}

    public function index(Request $request)
    {
        $jadwal = JadwalPemasangan::where('admin_id', $request->user()->id)
            ->whereNull('hasil')
            ->with('permohonanLayanan.pelanggan')
            ->orderBy('tanggal_pemasangan')
            ->paginate(20);

        return response()->json(['data' => $jadwal]);
    }

    public function show(Request $request, JadwalPemasangan $jadwalPemasangan)
    {
        abort_unless($jadwalPemasangan->admin_id === $request->user()->id, 403);

        return response()->json(['data' => $jadwalPemasangan->load('permohonanLayanan.pelanggan')]);
    }

    /**
     * Kalau hasil = selesai, respons berisi LayananInternet yang baru terbentuk
     * (atau ter-update, kalau ini kasus relokasi).
     */
    public function isiHasil(HasilPemasanganRequest $request, JadwalPemasangan $jadwalPemasangan)
    {
        abort_unless($jadwalPemasangan->admin_id === $request->user()->id, 403);

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