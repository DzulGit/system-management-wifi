<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginPertamaPelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_pelanggan' => ['required', 'string'],
            'nomor_hp' => ['required', 'string'],
        ];
    }
}