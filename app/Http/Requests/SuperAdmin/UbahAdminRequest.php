<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbahAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('admin', 'email')->ignore($this->route('admin'))],
            'peran' => ['sometimes', 'in:operasional,teknisi,keuangan'],
            'status_aktif' => ['sometimes', 'boolean'],
        ];
    }
}