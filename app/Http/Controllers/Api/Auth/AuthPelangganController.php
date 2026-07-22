<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BuatPasswordPelangganRequest;
use App\Http\Requests\Auth\LoginPelangganRequest;
use App\Http\Requests\Auth\LoginPertamaPelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthPelangganController extends Controller
{
    /**
     * Login pertama kali: nomor_pelanggan + nomor_hp, TANPA password.
     * Hanya berlaku selama password_sudah_dibuat masih false.
     * Token yang diterbitkan hanya bisa dipakai untuk endpoint buat-password,
     * karena middleware `pastikan.password` memblokir rute lain.
     */
    public function loginPertama(LoginPertamaPelangganRequest $request)
    {
        $data = $request->validated();

        $pelanggan = Pelanggan::where('nomor_pelanggan', $data['nomor_pelanggan'])
            ->where('nomor_hp', $data['nomor_hp'])
            ->where('password_sudah_dibuat', false)
            ->first();

        if (! $pelanggan) {
            throw ValidationException::withMessages([
                'nomor_pelanggan' => ['Data tidak ditemukan, atau password sudah pernah dibuat. Silakan login biasa.'],
            ]);
        }

        $token = $pelanggan->createToken('pelanggan-token-awal')->plainTextToken;

        return response()->json([
            'data' => [
                'pelanggan' => $pelanggan,
                'token' => $token,
                'wajib_buat_password' => true,
            ],
        ]);
    }

    /**
     * Login normal setelah pelanggan pernah membuat password.
     */
    public function login(LoginPelangganRequest $request)
    {
        $data = $request->validated();

        $pelanggan = Pelanggan::where('nomor_pelanggan', $data['nomor_pelanggan'])
            ->where('password_sudah_dibuat', true)
            ->first();

        if (! $pelanggan || ! Hash::check($data['password'], $pelanggan->password)) {
            throw ValidationException::withMessages([
                'nomor_pelanggan' => ['Nomor pelanggan atau password salah.'],
            ]);
        }

        $token = $pelanggan->createToken('pelanggan-token')->plainTextToken;

        return response()->json([
            'data' => [
                'pelanggan' => $pelanggan,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Wajib dipanggil setelah login-pertama, sebelum pelanggan bisa akses dashboard.
     */
    public function buatPassword(BuatPasswordPelangganRequest $request)
    {
        $pelanggan = $request->user();

        $pelanggan->update([
            'password' => $request->validated('password'), // otomatis di-hash via cast 'hashed'
            'password_sudah_dibuat' => true,
        ]);

        // Cabut token sementara (login-pertama), terbitkan token baru untuk sesi dashboard penuh
        $request->user()->currentAccessToken()->delete();
        $token = $pelanggan->createToken('pelanggan-token')->plainTextToken;

        return response()->json([
            'message' => 'Password berhasil dibuat.',
            'data' => [
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
