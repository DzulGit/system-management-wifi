<?php

namespace App\Http\Requests\PermohonanLayanan;

use Illuminate\Foundation\Http\FormRequest;

class HasilKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hasil' => ['required', 'in:selesai,kendala'],
            'catatan_kendala' => ['required_if:hasil,kendala', 'nullable', 'string'],
        ];
    }
}