# Tabel: `audit_log`

Log generik untuk seluruh sistem, wajib ada sesuai requirement keamanan.

## Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| pelaku_id | bigint, nullable | id admin/pelanggan/sistem |
| tipe_pelaku | string | `admin` / `pelanggan` / `sistem` |
| aksi | string | mis. `ubah_status_permohonan`, `nonaktifkan_layanan` |
| modul | string | mis. `permohonan_layanan`, `layanan_internet` |
| data_lama | json, nullable | |
| data_baru | json, nullable | |
| alamat_ip | string, nullable | |
| user_agent | string, nullable | |
| created_at | timestamp | insert-only |

## Perbedaan dengan `riwayat_status_permohonan`

`audit_log` bersifat **umum** (semua modul, semua jenis aksi termasuk login, CRUD admin, dsb). `riwayat_status_permohonan` bersifat **spesifik** untuk timeline status satu permohonan — dipakai untuk ditampilkan langsung ke UI (riwayat proses pemasangan). Perubahan status `AKTIF ⇄ NONAKTIF` pada `layanan_internet` (kasusnya jarang, mis. isolir) cukup tercatat lewat `audit_log`, tidak perlu tabel riwayat khusus.

## Relasi

Tidak ada foreign key formal ke tabel lain (pelaku_id + tipe_pelaku bersifat polymorphic sederhana) — agar satu tabel bisa mencatat aksi dari berbagai sumber tanpa banyak nullable FK.
