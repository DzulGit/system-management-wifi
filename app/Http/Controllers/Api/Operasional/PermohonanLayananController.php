<?php

namespace App\Http\Controllers\Api\Operasional;

use App\Enums\JenisPermohonanEnum;
use App\Enums\StatusPermohonanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanLayanan\JadwalkanPemasanganRequest;
use App\Http\Requests\PermohonanLayanan\JadwalkanSurveyRequest;
use App\Http\Requests\PermohonanLayanan\TambahPermohonanRequest;
use App\Http\Requests\PermohonanLayanan\VerifikasiPermohonanRequest;
use App\Models\LayananInternet;
use App\Models\PermohonanLayanan;
use App\Services\JadwalPemasanganService;
use App\Services\JadwalSurveyService;
use App\Services\PermohonanLayananService;
use Illuminate\Http\Request;

class PermohonanLayananController extends Controller
{
    public function __construct(
        private readonly PermohonanLayananService $permohonanLayananService,
        private readonly JadwalSurveyService $jadwalSurveyService,
        private readonly JadwalPemasanganService $jadwalPemasanganService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', PermohonanLayanan::class);

        $permohonan = PermohonanLayanan::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->with('pelanggan')
            ->latest()
            ->paginate(20);

        return response()->json(['data' => $permohonan]);
    }

    public function show(PermohonanLayanan $permohonan)
    {
        $this->authorize('view', $permohonan);

        return response()->json([
            'data' => $permohonan->load(['pelanggan', 'paketInternet', 'riwayatStatus', 'jadwalSurvey', 'jadwalPemasangan']),
        ]);
    }

    /**
     * Operasional membuat permohonan atas nama pelanggan yang SUDAH ADA
     * (tambah layanan / relokasi). Untuk pendaftaran pelanggan baru, pakai
     * endpoint publik Api\Pendaftaran\PendaftaranController.
     */
    public function store(TambahPermohonanRequest $request)
    {
        $this->authorize('create', PermohonanLayanan::class);

        $data = $request->validated();

        if ($data['jenis_permohonan'] === JenisPermohonanEnum::RELOKASI->value) {
            $layananLama = LayananInternet::findOrFail($data['layanan_internet_id']);

            // Relokasi mewarisi paket dari layanan lama — bukan pilih paket baru
            $data['tipe_paket'] = $layananLama->tipe_paket->value;
            $data['paket_internet_id'] = $layananLama->paket_internet_id;
            $data['nama_paket_custom'] = $layananLama->nama_paket_custom;
            $data['kecepatan_custom_mbps'] = $layananLama->kecepatan_custom_mbps;
            $data['harga_custom'] = $layananLama->harga_custom;
        }

        $permohonan = $this->permohonanLayananService->buatPermohonan($data);

        return response()->json(['data' => $permohonan], 201);
    }

    /**
     * Terima / Tolak / Minta Revisi.
     */
    public function verifikasi(VerifikasiPermohonanRequest $request, PermohonanLayanan $permohonan)
    {
        $this->authorize('ubahStatus', $permohonan);

        $data = $request->validated();
        $statusBaru = StatusPermohonanEnum::from($data['status']);

        $permohonan = $this->permohonanLayananService->ubahStatus(
            $permohonan,
            $statusBaru,
            $request->user(),
            $data['catatan'] ?? null,
        );

        if ($statusBaru === StatusPermohonanEnum::DITOLAK) {
            $permohonan->update(['alasan_ditolak' => $data['catatan'] ?? null]);
        }

        return response()->json(['data' => $permohonan]);
    }

    /**
     * Jadwalkan survey — dipakai untuk penjadwalan awal maupun re-jadwal setelah DITUNDA.
     */
    public function jadwalkanSurvey(JadwalkanSurveyRequest $request, PermohonanLayanan $permohonan)
    {
        $this->authorize('ubahStatus', $permohonan);

        $jadwal = $this->jadwalSurveyService->jadwalkan(
            $permohonan,
            $request->validated('admin_id'),
            $request->validated('tanggal_survey'),
            $request->user(),
        );

        return response()->json(['data' => $jadwal], 201);
    }

    /**
     * Jadwalkan pemasangan — hanya valid setelah status PEMASANGAN (survey berhasil)
     * atau saat resume dari DITUNDA di tahap pemasangan.
     */
    public function jadwalkanPemasangan(JadwalkanPemasanganRequest $request, PermohonanLayanan $permohonan)
    {
        $this->authorize('ubahStatus', $permohonan);

        $jadwal = $this->jadwalPemasanganService->jadwalkan(
            $permohonan,
            $request->validated('admin_id'),
            $request->validated('tanggal_pemasangan'),
            $request->user(),
        );

        return response()->json(['data' => $jadwal], 201);
    }
}
