<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    public function __construct(
        protected readonly Request $request,
    ) {}

    /**
     * Terapkan filter yang cocok dengan query string ke query builder.
     * Query param yang tidak punya method pasangan (mis. typo, atau memang
     * bukan filter yang didukung) diabaikan begitu saja, bukan error.
     *
     * Contoh: ?status=DITERIMA akan memanggil method status() kalau ada.
     */
    public function apply(Builder $builder): Builder
    {
        foreach ($this->request->query() as $nama => $nilai) {
            if ($nilai === null || $nilai === '') {
                continue;
            }

            $metode = Str::camel($nama);

            if (method_exists($this, $metode)) {
                $this->$metode($builder, $nilai);
            }
        }

        return $builder;
    }
}