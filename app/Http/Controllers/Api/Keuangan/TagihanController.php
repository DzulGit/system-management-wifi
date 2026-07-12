<?php

namespace App\Http\Controllers\Api\Keuangan;

use App\Filters\TagihanFilter;
use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Repositories\Contracts\TagihanRepositoryInterface;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function __construct(
        private readonly TagihanRepositoryInterface $tagihanRepository,
    ) {}

    public function index(TagihanFilter $filter)
    {
        $this->authorize('viewAny', Tagihan::class);

        return response()->json([
            'data' => $this->tagihanRepository->paginateSemua($filter),
        ]);
    }

    public function show(Tagihan $tagihan)
    {
        $this->authorize('view', $tagihan);

        $tagihan = $this->tagihanRepository->find($tagihan->id, ['layananInternet.pelanggan', 'pembayaran']);

        return response()->json(['data' => $tagihan]);
    }

    public function ringkasanOmzet(Request $request)
    {
        $tahun = $request->integer('tahun', now()->year);

        $data = \App\Models\Tagihan::selectRaw('periode_bulan, SUM(total_tagihan) as total_omzet, COUNT(*) as jumlah_tagihan')
            ->where('periode_tahun', $tahun)
            ->where('status_pembayaran', \App\Enums\StatusPembayaranEnum::SUDAH_BAYAR)
            ->groupBy('periode_bulan')
            ->orderBy('periode_bulan')
            ->get();

        return response()->json(['data' => $data]);
    }

    // Sengaja TIDAK ADA store()/update() — TagihanPolicy melarang keduanya.
    // Tagihan hanya dibuat otomatis oleh sistem (GenerateTagihanMassalJob).
}
