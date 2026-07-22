<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginAdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthAdminController extends Controller
{
    public function login(LoginAdminRequest $request)
    {
        $data = $request->validated();

        $admin = Admin::where('email', $data['email'])->first();

        // Pesan error sengaja digeneralisasi (tidak membedakan "email tidak ada"
        // vs "password salah" vs "akun nonaktif") untuk mencegah user enumeration.
        if (! $admin || ! $admin->status_aktif || ! Hash::check($data['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'data' => [
                'admin' => $admin,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout.',
        ]);
    }
}