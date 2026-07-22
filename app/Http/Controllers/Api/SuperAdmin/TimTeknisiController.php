<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\SimpanTimTeknisiRequest;
use App\Http\Requests\SuperAdmin\UbahTimTeknisiRequest;
use App\Models\TimTeknisi;
use App\Repositories\Contracts\TimTeknisiRepositoryInterface;

class TimTeknisiController extends Controller
{
    public function __construct(
        private readonly TimTeknisiRepositoryInterface $timTeknisiRepository,
    ) {}

    public function index()
    {
        return response()->json(['data' => $this->timTeknisiRepository->paginate()]);
    }

    public function show(TimTeknisi $timTeknisi)
    {
        return response()->json(['data' => $this->timTeknisiRepository->find($timTeknisi->id)]);
    }

    public function store(SimpanTimTeknisiRequest $request)
    {
        $tim = $this->timTeknisiRepository->create($request->validated());

        return response()->json(['data' => $tim], 201);
    }

    public function update(UbahTimTeknisiRequest $request, TimTeknisi $timTeknisi)
    {
        $tim = $this->timTeknisiRepository->update($timTeknisi, $request->validated());

        return response()->json(['data' => $tim]);
    }

    /** Dipakai dropdown Operasional saat jadwalkan kerja. */
    public function listAktif()
    {
        return response()->json(['data' => $this->timTeknisiRepository->listAktif()]);
    }
}