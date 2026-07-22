<?php

namespace App\Http\Requests\PermohonanLayanan;

use Illuminate\Foundation\Http\FormRequest;

class HasilPemasanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hasil' => ['required', 'in:selesai,ditunda'],
            'alasan_penundaan' => ['required_if:hasil,ditunda', 'nullable', 'string'],
        ];
    }
}