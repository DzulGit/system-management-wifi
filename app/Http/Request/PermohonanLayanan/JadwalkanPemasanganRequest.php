<?php

namespace App\Http\Requests\PermohonanLayanan;

use Illuminate\Foundation\Http\FormRequest;

class JadwalkanPemasanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id' => ['required', 'exists:admin,id'],
            'tanggal_pemasangan' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}