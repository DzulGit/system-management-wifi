<?php

namespace App\Http\Requests\PermohonanLayanan;

use Illuminate\Foundation\Http\FormRequest;

class HasilSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hasil' => ['required', 'in:berhasil,kendala'],
            'catatan' => ['required_if:hasil,kendala', 'nullable', 'string'],
        ];
    }
}