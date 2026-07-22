# API — Autentikasi

Base URL: `/api`

Semua endpoint di bawah ini **publik** (tanpa Bearer Token), kecuali disebutkan lain. Token yang didapat dipakai sebagai `Authorization: Bearer {token}` di endpoint lain.

---

## POST `/admin/login`

Login untuk Operasional, Teknisi, Keuangan, dan Super Admin.

**Rate limit:** 5x/menit per IP.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| email | string | ✔ |
| password | string | ✔ |

**Response 200**
```json
{
  "data": {
    "admin": { "id": 1, "nama_lengkap": "Budi", "email": "budi@isp.com", "peran": "operasional" },
    "token": "1|abcdef123456..."
  }
}
```

**Response 422** — email/password salah atau akun `status_aktif = false` (pesan digeneralisasi, tidak membedakan penyebab, demi keamanan).

---

## POST `/admin/logout`
**Auth:** Bearer Token (admin)

Mencabut token yang sedang dipakai (`currentAccessToken`).

**Response 200**
```json
{ "message": "Berhasil logout." }
```

---

## POST `/pelanggan/login-pertama`

Khusus login **pertama kali** setelah pemasangan selesai — sebelum pelanggan pernah membuat password.

**Rate limit:** 5x/menit per IP.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| nomor_pelanggan | string | ✔ |
| nomor_hp | string | ✔ |

**Response 200**
```json
{
  "data": {
    "pelanggan": { "id": 10, "nama_lengkap": "Ani", "nomor_pelanggan": "PLG000010" },
    "token": "5|xyz...",
    "wajib_buat_password": true
  }
}
```
Token ini **hanya bisa dipakai untuk endpoint `POST /pelanggan/buat-password`** — semua rute dashboard lain akan ditolak (403) selama password belum dibuat.

**Response 422** — kombinasi nomor_pelanggan + nomor_hp tidak ditemukan, ATAU password sudah pernah dibuat sebelumnya (harus pakai `/pelanggan/login` biasa).

---

## POST `/pelanggan/buat-password`
**Auth:** Bearer Token (pelanggan, token dari login-pertama)

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| password | string, min 8 | ✔ |
| password_confirmation | string, harus sama dengan `password` | ✔ |

**Response 200**
```json
{
  "message": "Password berhasil dibuat.",
  "data": { "token": "6|newtoken..." }
}
```
Token lama otomatis dicabut, dapat token baru untuk sesi dashboard penuh.

---

## POST `/pelanggan/login`

Login normal setelah password pernah dibuat.

**Rate limit:** 5x/menit per IP.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| nomor_pelanggan | string | ✔ |
| password | string | ✔ |

**Response 200**
```json
{
  "data": {
    "pelanggan": { "id": 10, "nama_lengkap": "Ani", "nomor_pelanggan": "PLG000010" },
    "token": "7|abc..."
  }
}
```

---

## POST `/pelanggan/logout`
**Auth:** Bearer Token (pelanggan, wajib `password_sudah_dibuat = true`)

**Response 200**
```json
{ "message": "Berhasil logout." }
```

---

## Catatan Keamanan

- Guard `sanctum` bersifat polymorphic (satu guard, dua model: `Admin` & `Pelanggan`). Setiap rute admin dijaga middleware `tipe-pengguna:admin`, setiap rute pelanggan dijaga `tipe-pengguna:pelanggan` — token milik satu tipe **tidak bisa** dipakai mengakses rute tipe lain.
- Rute admin per-role (Operasional/Teknisi/Keuangan/Super Admin) tambahan dijaga middleware `peran:...` — lihat dokumen role masing-masing.
