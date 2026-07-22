# API — Teknisi

**Auth:** Bearer Token admin, `peran` harus `teknisi` atau `super_admin`.
Base path: `/api/admin/teknisi`

Semua endpoint di sini otomatis ter-scope ke jadwal/laporan **milik teknisi yang login** — teknisi tidak bisa melihat atau mengubah milik teknisi lain (percobaan akan mendapat `403`).

---

## Jadwal Survey

### GET `/jadwal-survey`
List jadwal survey milik teknisi yang login, **yang belum diisi hasilnya** (`hasil IS NULL`), diurutkan berdasarkan `tanggal_survey` terdekat.

### GET `/jadwal-survey/{id}`
Detail 1 jadwal + relasi `permohonanLayanan.pelanggan`.

### PATCH `/jadwal-survey/{id}/hasil`
Isi hasil survey.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| hasil | `berhasil` \| `kendala` | ✔ |
| catatan | string | wajib jika `kendala` |

**Response 200** — objek `JadwalSurvey` yang sudah terisi hasil.

**Efek samping:**
- `berhasil` → status permohonan lanjut ke `PEMASANGAN` (menunggu Operasional jadwalkan pemasangan).
- `kendala` → status permohonan jadi `DITUNDA`, `alasan_ditunda` terisi dari `catatan`.

---

## Jadwal Pemasangan

### GET `/jadwal-pemasangan`
Sama seperti jadwal survey, tapi untuk pemasangan.

### GET `/jadwal-pemasangan/{id}`
Detail + relasi `permohonanLayanan.pelanggan`.

### PATCH `/jadwal-pemasangan/{id}/hasil`
Isi hasil pemasangan.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| hasil | `selesai` \| `ditunda` | ✔ |
| alasan_penundaan | string | wajib jika `ditunda` |

**Response 200**
- Kalau `hasil = selesai` → response berisi objek **`LayananInternet`** yang baru terbentuk (atau ter-update kalau kasus relokasi) — permohonan otomatis `DIKONVERSI`, dan kalau ini layanan pertama pelanggan, `nomor_pelanggan` ikut ter-generate.
- Kalau `hasil = ditunda` → response berisi objek `JadwalPemasangan`, status permohonan jadi `DITUNDA`.

---

## Laporan Kendala

### GET `/laporan-kendala`
List laporan yang **ditugaskan ke teknisi yang login**. Filter: `status`, `kategori_kendala`.

### GET `/laporan-kendala/{id}`
Detail laporan (hanya kalau `ditugaskan_ke` = teknisi yang login, selain itu `403`).

### PATCH `/laporan-kendala/{id}/selesaikan`
`DITUGASKAN` → `SELESAI`.

**Request Body**
| Field | Tipe | Wajib |
|---|---|---|
| hasil_penanganan | string | ✔ |

**Response 200** — objek laporan dengan `hasil_penanganan` terisi. Laporan baru benar-benar `DITUTUP` setelah Operasional konfirmasi (lihat `docs/api/operasional.md`).
