<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class AdminFilter extends QueryFilter
{
    // ?peran=teknisi
    protected function peran(Builder $builder, string $nilai): void
    {
        $builder->where('peran', $nilai);
    }

    // ?status_aktif=1
    protected function statusAktif(Builder $builder, string $nilai): void
    {
        $builder->where('status_aktif', filter_var($nilai, FILTER_VALIDATE_BOOLEAN));
    }
}