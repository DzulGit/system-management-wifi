<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Pelanggan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PastikanTipePengguna
{
    /**
     * Mencegah token milik Pelanggan dipakai untuk mengakses rute Admin, atau sebaliknya.
     * Perlu karena guard 'sanctum' bersifat polymorphic (satu guard, banyak model),
     * jadi validasi tipe user harus eksplisit di level middleware.
     *
     * Contoh pemakaian: ->middleware('tipe-pengguna:admin')
     */
    public function handle(Request $request, Closure $next, string $tipe): Response
    {
        $kelasDiharapkan = match ($tipe) {
            'admin' => Admin::class,
            'pelanggan' => Pelanggan::class,
            default => throw new HttpException(500, "Tipe pengguna '{$tipe}' tidak dikenali."),
        };

        if (! $request->user() instanceof $kelasDiharapkan) {
            abort(403, 'Akses tidak diizinkan untuk tipe pengguna ini.');
        }

        return $next($request);
    }
}