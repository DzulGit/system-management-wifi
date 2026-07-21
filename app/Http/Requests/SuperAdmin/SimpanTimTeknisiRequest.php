<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanTimTeknisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_tim' => ['required', 'string', 'max:255'],
            'anggota_ids' => ['required', 'array', 'min:1'],
            'anggota_ids.*' => [
                Rule::exists('admin', 'id')->where('peran', 'teknisi'),
            ],
        ];
    }
}