# API — Super Admin

**Auth:** Bearer Token admin, `peran` harus `super_admin`.
Base path: `/api/admin/super-admin`

Super Admin **tidak ikut proses bisnis harian** (tidak muncul di alur permohonan/survey/tagihan) — cakupannya cuma pengelolaan akun admin & pengaturan sistem. Akun Super Admin pertama **hanya** dibuat lewat `SuperAdminSeeder`, tidak ada endpoint publik untuk itu.

---

## GET `/admin`
List seluruh akun admin (paginated). Filter: `peran`, `status_aktif` (`1`/`0`).

## GET `/admin/{id}`
Detail 1 akun admin.

## POST `/admin`
Membuat akun admin baru.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| nama_lengkap | string | ✔ |
| email | string, unik | ✔ |
| password | string, min 8 | ✔ |
| peran | `operasional` \| `teknisi` \| `keuangan` | ✔ |

> `peran = super_admin` **sengaja ditolak** di endpoint ini (divalidasi di Request) — Super Admin baru cuma boleh dibuat lewat seeder/akses server langsung, bukan API.

**Response 201** — objek admin baru, `dibuat_oleh` otomatis terisi ID Super Admin yang login.

## PATCH `/admin/{id}`
Update data admin.

**Request Body** (semua `sometimes`)
| Field | Tipe |
|---|---|
| nama_lengkap | string |
| email | string, unik (kecuali dirinya sendiri) |
| peran | `operasional` \| `teknisi` \| `keuangan` |
| status_aktif | boolean |

## PATCH `/admin/{id}/nonaktifkan`
Shortcut untuk set `status_aktif = false` tanpa perlu kirim body. Admin yang dinonaktifkan tidak bisa login lagi (`AuthAdminController::login` mengecek `status_aktif`), tapi datanya tetap ada (relasi ke `permohonan_layanan`, `jadwal_survey`, dll tidak terputus).

## Menyusul
Endpoint pengaturan sistem lain (backup/restore, konfigurasi Payment Gateway, konfigurasi Landing Page) belum diimplementasi — di luar scope MVP saat ini.
