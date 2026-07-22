# API Documentation

Base URL: `/api`. Auth pakai Bearer Token (Laravel Sanctum) — lihat `autentikasi.md` untuk cara dapat token.

| File | Isi |
|---|---|
| `autentikasi.md` | Login admin, login pelanggan (login-pertama, buat-password, login biasa), logout |
| `pendaftaran.md` | Endpoint publik untuk form Landing Page |
| `operasional.md` | Permohonan Layanan (verifikasi, jadwalkan survey/pemasangan), Laporan Kendala (terima, teruskan, tutup) |
| `teknisi.md` | Jadwal Survey & Pemasangan (isi hasil), Laporan Kendala (selesaikan) |
| `keuangan.md` | Lihat Tagihan (read-only) |
| `pelanggan.md` | Dashboard pelanggan: profil, layanan, tagihan, laporan kendala |
| `super-admin.md` | CRUD akun Admin |
| `webhook-xendit.md` | **Rencana** kontrak webhook pembayaran — belum diimplementasi |

## Konvensi Umum

- Semua response sukses dibungkus `{ "data": ... }`, kadang disertai `"message"`.
- List/pagination pakai format standar Laravel paginator (`data`, `current_page`, `total`, dst nested di dalam `data`).
- Error validasi: HTTP 422, format `{ "message": ..., "errors": { "field": ["pesan"] } }` (bawaan Laravel Form Request).
- Error otorisasi: HTTP 403, format `{ "message": "..." }`.
- Semua tanggal format `YYYY-MM-DD`, datetime format ISO 8601.
