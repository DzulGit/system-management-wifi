<?php

namespace App\Http\Controllers\Api\Keuangan;

use App\Filters\TagihanFilter;
use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Repositories\Contracts\TagihanRepositoryInterface;

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

    // Sengaja TIDAK ADA store()/update() — TagihanPolicy melarang keduanya.
    // Tagihan hanya dibuat otomatis oleh sistem (GenerateTagihanMassalJob).
}