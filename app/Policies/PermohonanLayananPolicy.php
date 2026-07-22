<?php

namespace App\Policies;

use App\Enums\PeranAdminEnum;
use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\PermohonanLayanan;

class PermohonanLayananPolicy
{
    public function viewAny(Admin|Pelanggan $user): bool
    {
        // Daftar tetap difilter sesuai kepemilikan/kebutuhan di level query controller.
        return true;
    }

    public function view(Admin|Pelanggan $user, PermohonanLayanan $permohonan): bool
    {
        if ($user instanceof Admin) {
            return true; // semua peran admin boleh melihat detail
        }

        return (int) $permohonan->pelanggan_id === (int) $user->id;
    }

    public function create(Admin|Pelanggan $user): bool
    {
        // Pelanggan: daftar/ajukan relokasi sendiri lewat landing page.
        // Operasional & Super Admin: bisa buat atas nama pelanggan (mis. tambah layanan).
        if ($user instanceof Pelanggan) {
            return true;
        }

        return in_array($user->peran, [PeranAdminEnum::OPERASIONAL, PeranAdminEnum::SUPER_ADMIN], true);
    }

    /**
     * Terima / Tolak / Minta Revisi — khusus Operasional.
     */
    public function ubahStatus(Admin $admin, PermohonanLayanan $permohonan): bool
    {
        return in_array($admin->peran, [PeranAdminEnum::OPERASIONAL, PeranAdminEnum::SUPER_ADMIN], true);
    }

    public function delete(Admin $admin, PermohonanLayanan $permohonan): bool
    {
        return $admin->peran === PeranAdminEnum::SUPER_ADMIN;
    }
}