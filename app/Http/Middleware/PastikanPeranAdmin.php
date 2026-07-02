<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanPeranAdmin
{
    /**
     * Proteksi rute berdasarkan `peran` admin. Dipasang SETELAH middleware
     * 'auth:sanctum' dan 'tipe-pengguna:admin'.
     *
     * Contoh: ->middleware('peran:keuangan')
     *         ->middleware('peran:operasional,super_admin')   // boleh lebih dari satu
     */
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        $admin = $request->user();

        if (! $admin instanceof Admin) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (! in_array($admin->peran->value, $peranDiizinkan, true)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}