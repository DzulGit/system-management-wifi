<?php

namespace App\Http\Middleware;

use App\Models\Pelanggan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanPasswordSudahDibuat
{
    /**
     * Blokir akses ke rute dashboard pelanggan selama password belum dibuat.
     * Dipasang di semua rute pelanggan KECUALI endpoint buat-password itu sendiri.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof Pelanggan && ! $user->password_sudah_dibuat) {
            abort(403, 'Anda wajib membuat password terlebih dahulu sebelum mengakses fitur ini.');
        }

        return $next($request);
    }
}