# API — Operasional

**Auth:** Bearer Token admin, `peran` harus `operasional` atau `super_admin`.
Base path: `/api/admin/operasional`

---

## Permohonan Layanan

### GET `/permohonan-layanan`
List permohonan (paginated, 20/halaman).

**Query Filter (opsional)**
| Param | Contoh | Keterangan |
|---|---|---|
| status | `MENUNGGU_VERIFIKASI` | Filter status persis |
| jenis_permohonan | `relokasi` | `pemasangan_baru` \| `relokasi` |
| pelanggan_id | `10` | |

### GET `/permohonan-layanan/{id}`
Detail 1 permohonan, termasuk relasi `pelanggan`, `paketInternet`, `riwayatStatus`, `jadwalSurvey`, `jadwalPemasangan`.

### POST `/permohonan-layanan`
Membuat permohonan **untuk pelanggan yang sudah ada** (tambah layanan / relokasi). Untuk pelanggan baru, gunakan endpoint publik `/api/pendaftaran`.

**Request Body**
| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| pelanggan_id | integer | ✔ | |
| jenis_permohonan | `pemasangan_baru` \| `relokasi` | ✔ | |
| layanan_internet_id | integer | wajib jika `relokasi` | Layanan yang direlokasi |
| tipe_paket | `reguler` \| `custom` | wajib jika `pemasangan_baru` | Relokasi otomatis warisi paket lama |
| paket_internet_id | integer | kondisional | |
| nama_paket_custom, kecepatan_custom_mbps, harga_custom, catatan_custom | | kondisional | |
| alamat_pemasangan, rt, rw, kode_pos, latitude, longitude | | ✔ | |

**Response 201** — objek `PermohonanLayanan` yang baru dibuat, `status = MENUNGGU_VERIFIKASI`.

### PATCH `/permohonan-layanan/{id}/verifikasi`
Terima / Tolak / Minta Revisi.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| status | `DITERIMA` \| `DITOLAK` \| `PERLU_REVISI` | ✔ |
| catatan | string | wajib jika `DITOLAK`/`PERLU_REVISI` |

**Response 200** — objek permohonan dengan status terbaru.
**Response 422** — transisi status tidak valid (mis. permohonan sudah `DIKONVERSI`).

### POST `/permohonan-layanan/{id}/jadwalkan-survey`
Buat jadwal survey (awal maupun re-jadwal setelah `DITUNDA`).

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| admin_id | integer (id Teknisi) | ✔ |
| tanggal_survey | date, `>= hari ini` | ✔ |

**Response 201** — objek `JadwalSurvey`. Status permohonan otomatis jadi `DIJADWALKAN` (atau `SURVEY` kalau resume dari `DITUNDA`).

### POST `/permohonan-layanan/{id}/jadwalkan-pemasangan`
Sama seperti di atas, untuk tahap pemasangan. Hanya valid setelah status permohonan `PEMASANGAN` (hasil survey berhasil) atau resume dari `DITUNDA` di tahap pemasangan.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| admin_id | integer (id Teknisi) | ✔ |
| tanggal_pemasangan | date, `>= hari ini` | ✔ |

---

## Laporan Kendala

### GET `/laporan-kendala`
List semua laporan (paginated). Filter: `status`, `kategori_kendala` (partial match).

### GET `/laporan-kendala/{id}`
Detail laporan + relasi `layananInternet.pelanggan`, `ditugaskanKe`, `ditutupOleh`.

### PATCH `/laporan-kendala/{id}/terima`
`MENUNGGU` → `DIPROSES`. Tidak ada request body.

### PATCH `/laporan-kendala/{id}/teruskan-ke-teknisi`
`DIPROSES` → `DITUGASKAN`.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| teknisi_id | integer (harus admin dengan peran teknisi) | ✔ |

### PATCH `/laporan-kendala/{id}/tutup`
Menutup laporan (dari `MENUNGGU`/`DIPROSES`/`SELESAI` → `DITUTUP`). Tidak ada request body.

---

## Menyusul
CRUD Paket Internet (`paket_internet`) belum dibuat — akan ditambahkan saat modul katalog paket digarap.
