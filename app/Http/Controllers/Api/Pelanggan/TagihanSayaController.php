<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Filters\TagihanFilter;
use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Repositories\Contracts\TagihanRepositoryInterface;
use Illuminate\Http\Request;

class TagihanSayaController extends Controller
{
    public function __construct(
        private readonly TagihanRepositoryInterface $tagihanRepository,
    ) {}

    public function index(Request $request, TagihanFilter $filter)
    {
        $this->authorize('viewAny', Tagihan::class);

        return response()->json([
            'data' => $this->tagihanRepository->paginateUntukPelanggan($request->user()->id, $filter),
        ]);
    }

    public function show(Tagihan $tagihan)
    {
        $this->authorize('view', $tagihan);

        $tagihan = $this->tagihanRepository->find($tagihan->id, ['layananInternet', 'pembayaran']);

        return response()->json(['data' => $tagihan]);
    }
}