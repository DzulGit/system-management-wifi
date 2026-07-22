# API — Pendaftaran (Publik)

Endpoint untuk form Landing Page. **Tanpa login.**

---

## POST `/pendaftaran`

**Rate limit:** 3x/menit per IP.

**Content-Type:** `multipart/form-data` (karena ada upload file)

**Request Body**

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| nama_lengkap | string | ✔ | |
| nik | string(16) | ✔ | Harus unik |
| nomor_hp | string | ✔ | Harus unik, dipakai untuk login pertama |
| email | string | – | |
| alamat_pemasangan | string | ✔ | |
| rt | string | ✔ | |
| rw | string | ✔ | |
| kode_pos | string | ✔ | |
| latitude | numeric | ✔ | -90 s/d 90 |
| longitude | numeric | ✔ | -180 s/d 180 |
| tipe_paket | `reguler` \| `custom` | ✔ | |
| paket_internet_id | integer | wajib jika `reguler` | |
| nama_paket_custom | string | wajib jika `custom` | |
| kecepatan_custom_mbps | integer | wajib jika `custom` | |
| catatan_custom | string | – | |
| foto_ktp | file (image, max 2MB) | ✔ | |
| foto_selfie_ktp | file (image, max 2MB) | ✔ | |

**Response 201**
```json
{
  "message": "Pendaftaran berhasil diterima, silakan tunggu verifikasi dari tim kami.",
  "data": { "nomor_permohonan": "PMH000123" }
}
```

**Response 422** — validasi gagal (NIK/nomor HP sudah terdaftar, file tidak valid, dll).

## Yang Terjadi di Balik Layar

1. Baris baru dibuat di tabel `pelanggan` (belum punya `nomor_pelanggan`, belum bisa login).
2. Baris baru dibuat di `permohonan_layanan` dengan `status = MENUNGGU_VERIFIKASI`, `jenis_permohonan = pemasangan_baru`.
3. File KTP & selfie diupload ke Supabase Storage, database hanya menyimpan path-nya.

Lihat `docs/arsitektur/business-flow.md` bagian 1–4 untuk alur lengkap setelah ini (verifikasi → survey → pemasangan → aktif).
