<?php

namespace App\Http\Requests\PermohonanLayanan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JadwalkanKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Opsional — cuma dipakai kalau Operasional pilih shortcut "pilih tim baku"
            'tim_teknisi_id' => ['nullable', 'exists:tim_teknisi,id'],
            // WAJIB — daftar teknisi yang benar-benar ditugaskan (baik dari
            // hasil "pilih tim" yang di-expand ke individu, atau assign manual)
            'teknisi_ids' => ['required', 'array', 'min:1'],
            'teknisi_ids.*' => [
                Rule::exists('admin', 'id')->where('peran', 'teknisi'),
            ],
            'tanggal_kerja' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}