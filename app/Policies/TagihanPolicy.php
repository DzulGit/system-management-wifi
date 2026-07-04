<?php

namespace App\Policies;

use App\Enums\PeranAdminEnum;
use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Tagihan;

class TagihanPolicy
{
    public function viewAny(Admin|Pelanggan $user): bool
    {
        if ($user instanceof Admin) {
            return in_array($user->peran, [PeranAdminEnum::KEUANGAN, PeranAdminEnum::SUPER_ADMIN], true);
        }

        return true; // pelanggan boleh lihat daftar tagihan miliknya sendiri
    }

    public function view(Admin|Pelanggan $user, Tagihan $tagihan): bool
    {
        if ($user instanceof Admin) {
            return in_array($user->peran, [PeranAdminEnum::KEUANGAN, PeranAdminEnum::SUPER_ADMIN], true);
        }

        return $tagihan->layananInternet->pelanggan_id === $user->id;
    }

    public function create(Admin $admin): bool
    {
        // Tagihan dibuat OTOMATIS oleh sistem (Scheduler/Job), bukan manual oleh Keuangan.
        return false;
    }

    public function update(Admin $admin, Tagihan $tagihan): bool
    {
        // Data tagihan tidak boleh diubah manual sama sekali — jaga integritas snapshot billing.
        // Sesuai requirement: Keuangan tidak boleh mengubah data.
        return false;
    }
}