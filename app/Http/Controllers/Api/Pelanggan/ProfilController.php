<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pelanggan\UbahProfilRequest;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(['data' => $request->user()]);
    }

    public function update(UbahProfilRequest $request)
    {
        $pelanggan = $request->user();
        $pelanggan->update($request->validated());

        return response()->json(['data' => $pelanggan->fresh()]);
    }
}