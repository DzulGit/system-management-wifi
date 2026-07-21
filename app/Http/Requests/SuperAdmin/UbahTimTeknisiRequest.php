<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbahTimTeknisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_tim' => ['sometimes', 'string', 'max:255'],
            'status_aktif' => ['sometimes', 'boolean'],
            'anggota_ids' => ['sometimes', 'array', 'min:1'],
            'anggota_ids.*' => [
                Rule::exists('admin', 'id')->where('peran', 'teknisi'),
            ],
        ];
    }
}