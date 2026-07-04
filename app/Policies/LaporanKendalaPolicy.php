<?php

namespace App\Policies;

use App\Enums\PeranAdminEnum;
use App\Models\Admin;
use App\Models\LaporanKendala;
use App\Models\Pelanggan;

class LaporanKendalaPolicy
{
    public function viewAny(Admin|Pelanggan $user): bool
    {
        if ($user instanceof Admin) {
            return in_array($user->peran, [
                PeranAdminEnum::OPERASIONAL,
                PeranAdminEnum::TEKNISI,
                PeranAdminEnum::SUPER_ADMIN,
            ], true);
        }

        return true;
    }

    public function view(Admin|Pelanggan $user, LaporanKendala $laporan): bool
    {
        if ($user instanceof Admin) {
            return $this->viewAny($user);
        }

        return $laporan->layananInternet->pelanggan_id === $user->id;
    }

    public function create(Admin|Pelanggan $user): bool
    {
        if ($user instanceof Pelanggan) {
            return true;
        }

        return in_array($user->peran, [PeranAdminEnum::OPERASIONAL, PeranAdminEnum::SUPER_ADMIN], true);
    }

    /**
     * Terima laporan & teruskan ke Teknisi — khusus Operasional.
     */
    public function teruskanKeTeknisi(Admin $admin, LaporanKendala $laporan): bool
    {
        return in_array($admin->peran, [PeranAdminEnum::OPERASIONAL, PeranAdminEnum::SUPER_ADMIN], true);
    }

    /**
     * Isi hasil penanganan (status -> SELESAI) — khusus Teknisi.
     */
    public function selesaikan(Admin $admin, LaporanKendala $laporan): bool
    {
        return in_array($admin->peran, [PeranAdminEnum::TEKNISI, PeranAdminEnum::SUPER_ADMIN], true);
    }

    /**
     * Tutup laporan setelah pelanggan dipastikan puas — khusus Operasional.
     */
    public function tutup(Admin $admin, LaporanKendala $laporan): bool
    {
        return in_array($admin->peran, [PeranAdminEnum::OPERASIONAL, PeranAdminEnum::SUPER_ADMIN], true);
    }
}