<?php

namespace App\Http\Requests\LaporanKendala;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeruskanKeTeknisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teknisi_id' => [
                'required',
                Rule::exists('admin', 'id')->where('peran', 'teknisi'),
            ],
        ];
    }
}