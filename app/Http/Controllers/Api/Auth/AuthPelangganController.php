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
     * Login normal. Menerima baik pelanggan yang sudah pernah membuat
     * password sendiri, maupun yang masih pakai password default
     * (= nomor_pelanggan, di-set otomatis lewat model event Pelanggan::booted()).
     * Validasi cukup lewat Hash::check() — kolom password_sudah_dibuat
     * TIDAK dipakai sebagai filter di sini, hanya untuk menentukan apakah
     * popup "ganti password" perlu ditampilkan di dashboard.
     */
    public function login(LoginPelangganRequest $request)
    {
        $data = $request->validated();

        $pelanggan = Pelanggan::where('nomor_pelanggan', $data['nomor_pelanggan'])->first();

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

    /**
     * Data pelanggan yang sedang login. Dipakai dashboard untuk cek
     * password_sudah_dibuat dari sumber data yang benar (tabel pelanggan),
     * bukan dari response login. Daftarkan route ini dengan middleware
     * auth:sanctum, misal: Route::get('/pelanggan/me', [..., 'me']).
     */
    public function me(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }
}