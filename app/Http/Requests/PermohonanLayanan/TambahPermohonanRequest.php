<?php

namespace App\Http\Requests\PermohonanLayanan;

use Illuminate\Foundation\Http\FormRequest;

class TambahPermohonanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // proteksi via Policy di Controller
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['required', 'exists:pelanggan,id'],
            'jenis_permohonan' => ['required', 'in:pemasangan_baru,relokasi'],
            // Wajib diisi hanya kalau relokasi — menunjuk layanan yang mau direlokasi
            'layanan_internet_id' => ['required_if:jenis_permohonan,relokasi', 'nullable', 'exists:layanan_internet,id'],

            // Wajib diisi hanya kalau pemasangan_baru (relokasi mewarisi paket dari layanan lama)
            'tipe_paket' => ['required_if:jenis_permohonan,pemasangan_baru', 'nullable', 'in:reguler,custom'],
            'paket_internet_id' => ['nullable', 'exists:paket_internet,id'],
            'nama_paket_custom' => ['nullable', 'string'],
            'kecepatan_custom_mbps' => ['nullable', 'integer', 'min:1'],
            'harga_custom' => ['nullable', 'numeric', 'min:0'],
            'catatan_custom' => ['nullable', 'string'],

            'alamat_pemasangan' => ['required', 'string'],
            'rt' => ['required', 'string', 'max:3'],
            'rw' => ['required', 'string', 'max:3'],
            'kode_pos' => ['required', 'string', 'max:5'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}