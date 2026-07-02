# Tabel: `paket_internet`

Katalog paket **reguler** saja. Paket custom tidak masuk sini (lihat `tabel-permohonan-layanan.md`).

## Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_paket | string | |
| kecepatan_mbps | integer | |
| harga | decimal | |
| deskripsi | text, nullable | |
| status_aktif | boolean, default true | soft-disable, tanpa hapus (masih direferensikan layanan lama) |
| created_at, updated_at | timestamp | |

## Catatan Desain

- Paket custom yang disetujui Operasional **tidak otomatis** masuk katalog ini. Jika ternyata sering diminati, Operasional membuat entri baru di sini secara manual lewat menu CRUD Paket.
- `status_aktif = false` dipakai saat paket dihentikan penjualannya, tapi tetap harus ada agar `layanan_internet.paket_internet_id` lama tidak orphan.

## Relasi

- `paket_internet` 1—N `permohonan_layanan` (nullable, hanya jika `tipe_paket = reguler`)
- `paket_internet` 1—N `layanan_internet` (nullable, hanya jika `tipe_paket = reguler`)
