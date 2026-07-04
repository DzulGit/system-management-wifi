<?php

namespace App\Http\Requests\LaporanKendala;

use Illuminate\Foundation\Http\FormRequest;

class SelesaikanLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hasil_penanganan' => ['required', 'string'],
        ];
    }
}