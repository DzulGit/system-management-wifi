# Modul: Permohonan Layanan

Mencakup 4 tabel: `permohonan_layanan`, `jadwal_survey`, `jadwal_pemasangan`, `riwayat_status_permohonan`.

Lihat state machine lengkap di `docs/arsitektur/business-flow.md`.

---

## 2.1 `permohonan_layanan`

Data yang **belum tentu** menjadi layanan aktif — mencakup pendaftaran baru maupun pengajuan relokasi.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | uuid / bigint PK | |
| nomor_permohonan | string, unique | format `PMH000001` |
| pelanggan_id | FK → pelanggan.id | |
| jenis_permohonan | enum: `pemasangan_baru`, `relokasi` | |
| layanan_internet_id | FK → layanan_internet.id, **nullable** | diisi **hanya** jika `jenis_permohonan = relokasi` (menunjuk layanan yang direlokasi) |
| paket_internet_id | FK → paket_internet.id, nullable | null jika `tipe_paket = custom` |
| tipe_paket | enum: `reguler`, `custom` | |
| nama_paket_custom | string, nullable | |
| kecepatan_custom_mbps | integer, nullable | |
| harga_custom | decimal, nullable | hasil negosiasi Operasional |
| catatan_custom | text, nullable | kebutuhan khusus dari form pendaftaran |
| alamat_pemasangan | text | |
| rt, rw, kode_pos | string | |
| latitude, longitude | decimal | |
| status | enum (lihat state machine) | |
| alasan_ditolak | text, nullable | |
| alasan_ditunda | text, nullable | |
| diproses_oleh | FK → admin.id, nullable | operasional yang menangani |
| created_at, updated_at | timestamp | |

**Nilai `status`:** `MENUNGGU_VERIFIKASI`, `PERLU_REVISI`, `DITERIMA`, `DITOLAK`, `DIJADWALKAN`, `SURVEY`, `PEMASANGAN`, `DITUNDA`, `DIKONVERSI`.

> Catatan: `AKTIF`/`NONAKTIF` **bukan** bagian dari status permohonan — itu status milik `layanan_internet` setelah dikonversi.

---

## 2.2 `jadwal_survey`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| permohonan_layanan_id | FK | |
| admin_id | FK → admin.id | teknisi bertugas |
| tanggal_survey | date | |
| hasil | enum: `berhasil`, `kendala`, nullable | null = belum dilaksanakan |
| catatan | text, nullable | |
| created_at, updated_at | timestamp | |

## 2.3 `jadwal_pemasangan`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| permohonan_layanan_id | FK | |
| admin_id | FK → admin.id | teknisi bertugas |
| tanggal_pemasangan | date | |
| hasil | enum: `selesai`, `ditunda`, nullable | |
| alasan_penundaan | text, nullable | |
| created_at, updated_at | timestamp | |

> Bisa ada lebih dari satu baris `jadwal_survey`/`jadwal_pemasangan` per permohonan jika sempat `DITUNDA` lalu dijadwalkan ulang.

## 2.4 `riwayat_status_permohonan`

Log setiap perubahan status untuk audit & timeline di dashboard.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| permohonan_layanan_id | FK | |
| status_sebelumnya | string, nullable | |
| status_sesudahnya | string | |
| diubah_oleh | FK → admin.id, nullable | |
| catatan | text, nullable | |
| created_at | timestamp | insert-only, tidak ada updated_at |

## Relasi

- `pelanggan` 1—N `permohonan_layanan`
- `admin` 1—N `permohonan_layanan` (via `diproses_oleh`)
- `permohonan_layanan` 1—N `jadwal_survey`, `jadwal_pemasangan`, `riwayat_status_permohonan`
- `permohonan_layanan` 0..1—0..1 `layanan_internet` (lihat `docs/arsitektur/erd.md` untuk detail arah relasi ganda)
