<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class TagihanFilter extends QueryFilter
{
    // ?status_pembayaran=belum_bayar
    protected function statusPembayaran(Builder $builder, string $nilai): void
    {
        $builder->where('status_pembayaran', $nilai);
    }

    // ?periode_bulan=7
    protected function periodeBulan(Builder $builder, string $nilai): void
    {
        $builder->where('periode_bulan', $nilai);
    }

    // ?periode_tahun=2026
    protected function periodeTahun(Builder $builder, string $nilai): void
    {
        $builder->where('periode_tahun', $nilai);
    }
}