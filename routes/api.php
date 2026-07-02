<?php

use App\Http\Controllers\Api\Auth\AuthAdminController;
use App\Http\Controllers\Api\Auth\AuthPelangganController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthAdminController::class, 'login'])
        ->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'tipe-pengguna:admin'])->group(function () {
        Route::post('logout', [AuthAdminController::class, 'logout']);

        // Rute modul Operasional / Teknisi / Keuangan / Super Admin menyusul di sini,
        // masing-masing akan ditambah middleware pengecekan `peran` (tahap Authorization).
    });
});

Route::prefix('pelanggan')->group(function () {
    Route::post('login-pertama', [AuthPelangganController::class, 'loginPertama'])
        ->middleware('throttle:login-pertama');

    Route::post('login', [AuthPelangganController::class, 'login'])
        ->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'tipe-pengguna:pelanggan'])->group(function () {
        Route::post('buat-password', [AuthPelangganController::class, 'buatPassword']);

        Route::middleware('pastikan.password')->group(function () {
            Route::post('logout', [AuthPelangganController::class, 'logout']);

            // Rute dashboard pelanggan (profil, layanan, tagihan, laporan kendala)
            // menyusul di sini.
        });
    });
});