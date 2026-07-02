# Dokumentasi — Sistem Manajemen Pelanggan WiFi (MVP)

Dokumentasi dipecah per modul agar mudah di-maintain. Setiap perubahan kecil cukup mengubah file terkait, tanpa menyentuh dokumen lain.

## Struktur

```
docs/
├── README.md                          (file ini)
├── arsitektur/
│   ├── erd.md                         ERD & relasi antar entitas
│   ├── business-flow.md               Alur bisnis & state machine per modul
│   └── struktur-project.md            Struktur folder Laravel & konvensi penamaan
├── database/
│   ├── tabel-admin.md
│   ├── tabel-pelanggan.md
│   ├── tabel-paket-internet.md
│   ├── tabel-permohonan-layanan.md    permohonan_layanan, jadwal_survey, jadwal_pemasangan, riwayat_status_permohonan
│   ├── tabel-layanan-internet.md      layanan_internet, perangkat, riwayat_perubahan_paket, riwayat_relokasi
│   ├── tabel-tagihan.md               tagihan, pembayaran
│   ├── tabel-laporan-kendala.md
│   └── tabel-audit-log.md
├── api/                                (menyusul — desain endpoint per role)
├── testing/                            (menyusul — skenario Feature & Unit Test)
└── deployment/                         (menyusul — panduan deploy & environment)
```

## Status Pengerjaan

| Tahap | Status |
|---|---|
| 1. ERD | ✅ Selesai |
| 2. Relasi antar entitas | ✅ Selesai |
| 3. Struktur folder Laravel | ✅ Selesai |
| 4. Alur bisnis | ✅ Selesai |
| 5. Migration | ⏳ Menunggu persetujuan |
| 6. Implementasi fitur | ⏳ Belum mulai |

## Cara Membaca

Mulai dari `arsitektur/erd.md` untuk gambaran besar hubungan antar tabel, lalu masuk ke `database/` untuk detail kolom per modul, dan `arsitektur/business-flow.md` untuk memahami urutan proses bisnisnya.
