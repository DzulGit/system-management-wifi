# Tabel: `admin`

Akun internal untuk 4 level akses: 1 Super Admin + 3 role bisnis (Operasional, Teknisi, Keuangan).

## Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | uuid / bigint PK | |
| nama_lengkap | string | |
| email | string, unique | login admin |
| password | string (hashed) | |
| peran | enum: `super_admin`, `operasional`, `teknisi`, `keuangan` | 1 admin = 1 peran |
| status_aktif | boolean, default true | nonaktifkan tanpa hapus data |
| dibuat_oleh | FK → admin.id, nullable | jejak siapa yang membuat akun ini |
| created_at, updated_at | timestamp | |

## Catatan Desain

- **Super Admin bukan role operasional harian.** Hak akses: CRUD Admin, pengaturan sistem, backup/restore, pengaturan Payment Gateway, pengaturan Landing Page, akses penuh Audit Log. Tidak ikut proses bisnis (verifikasi permohonan, survey, dll).
- Super Admin **pertama** dibuat lewat `SuperAdminSeeder`, bukan via form publik. Setelah itu, **hanya Super Admin** yang boleh membuat akun admin lain (lihat `SuperAdmin/AdminController`).
- `status_aktif = false` dipakai untuk menonaktifkan admin yang resign, tanpa menghapus jejak data (relasi ke `permohonan_layanan`, `jadwal_survey`, dll tetap utuh).

## Relasi

- `admin` 1—N `permohonan_layanan` (via `diproses_oleh`)
- `admin` 1—N `jadwal_survey` (teknisi bertugas)
- `admin` 1—N `jadwal_pemasangan` (teknisi bertugas)
- `admin` 1—N `laporan_kendala` (via `ditugaskan_ke`, `ditutup_oleh`)
