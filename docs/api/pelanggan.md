# API — Dashboard Pelanggan

**Auth:** Bearer Token pelanggan. Semua endpoint (kecuali `buat-password`) juga dijaga middleware `pastikan.password` — kalau `password_sudah_dibuat = false`, semua request ke sini ditolak `403`. Lihat `docs/api/autentikasi.md`.

Base path: `/api/pelanggan`

---

## Profil

### GET `/profil`
Data profil pelanggan yang login (`password` tidak pernah ikut ter-serialize, di-`hidden`-kan di Model).

### PATCH `/profil`
**Request Body** (semua opsional, `sometimes`)
| Field | Tipe |
|---|---|
| nama_lengkap | string |
| email | string, nullable |

> `nik` dan `nomor_hp` **sengaja tidak bisa diubah** lewat endpoint ini — itu identitas & kredensial login, perubahan harus lewat proses lain yang lebih ketat (di luar scope MVP).

---

## Layanan Saya

### GET `/layanan`
List seluruh `layanan_internet` milik pelanggan (bisa lebih dari satu — mis. rumah + gudang).

### GET `/layanan/{id}`
Detail 1 layanan + relasi `paketInternet`, `perangkat`, `riwayatPerubahanPaket`, `riwayatRelokasi`.

**Response 403** kalau `{id}` bukan milik pelanggan yang login (dicek via `LayananInternetPolicy`).

---

## Tagihan Saya

### GET `/tagihan`
List tagihan dari seluruh layanan milik pelanggan. Filter: `status_pembayaran`, `periode_bulan`, `periode_tahun`.

### GET `/tagihan/{id}`
Detail tagihan + `pembayaran` (histori transaksi/webhook).

---

## Laporan Kendala Saya

### GET `/laporan-kendala`
List laporan kendala dari seluruh layanan milik pelanggan.

### GET `/laporan-kendala/{id}`
Detail 1 laporan.

### POST `/laporan-kendala`
Buat laporan baru untuk salah satu layanan miliknya.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| layanan_internet_id | integer | ✔ — **divalidasi harus milik pelanggan yang login** |
| kategori_kendala | string | ✔ |
| deskripsi | string | ✔ |

**Response 201** — objek laporan, `status = menunggu`.
**Response 422** — kalau `layanan_internet_id` bukan milik pelanggan yang login (pesan muncul sebagai validation error di field ini, bukan 403 — supaya tidak membocorkan apakah ID tersebut valid milik orang lain).
