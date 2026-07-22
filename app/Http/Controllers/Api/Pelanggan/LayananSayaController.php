<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\LayananInternet;
use App\Repositories\Contracts\LayananInternetRepositoryInterface;
use Illuminate\Http\Request;

class LayananSayaController extends Controller
{
    public function __construct(
        private readonly LayananInternetRepositoryInterface $layananInternetRepository,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', LayananInternet::class);

        return response()->json([
            'data' => $this->layananInternetRepository->paginateUntukPelanggan($request->user()->id),
        ]);
    }

    public function show(LayananInternet $layanan)
    {
        $this->authorize('view', $layanan);

        $layanan = $this->layananInternetRepository->find(
            $layanan->id,
            ['paketInternet', 'perangkat', 'riwayatPerubahanPaket', 'riwayatRelokasi']
        );

        return response()->json(['data' => $layanan]);
    }
}