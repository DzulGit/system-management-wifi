<?php

namespace App\Http\Requests\Pelanggan;

use Illuminate\Foundation\Http\FormRequest;

class UbahProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // NIK & nomor_hp SENGAJA tidak bisa diubah sendiri oleh pelanggan (identitas & kredensial login)
        return [
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email'],
        ];
    }
}