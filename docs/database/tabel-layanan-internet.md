# Modul: Layanan Internet

Mencakup 4 tabel: `layanan_internet`, `perangkat`, `riwayat_perubahan_paket`, `riwayat_relokasi`.

Berisi **layanan yang sudah resmi hidup** — hasil konversi dari `permohonan_layanan` yang `DIKONVERSI`.

---

## 3.1 `layanan_internet`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | uuid / bigint PK | |
| nomor_layanan | string, unique | format `LYN000001`, dibuat saat konversi |
| permohonan_layanan_id | FK → permohonan_layanan.id | asal-usul layanan ini |
| pelanggan_id | FK → pelanggan.id | |
| paket_internet_id | FK → paket_internet.id, nullable | |
| tipe_paket | enum: `reguler`, `custom` | |
| nama_paket_custom | string, nullable | disalin dari permohonan, berubah jika upgrade/downgrade custom |
| kecepatan_custom_mbps | integer, nullable | |
| harga_custom | decimal, nullable | |
| alamat_pemasangan | text | disalin dari permohonan, berubah jika relokasi |
| rt, rw, kode_pos | string | |
| latitude, longitude | decimal | |
| status | enum: `aktif`, `nonaktif` | |
| tanggal_aktif | date | **basis tanggal siklus tagihan bulanan** |
| created_at, updated_at | timestamp | |

---

## 3.2 `perangkat`

Pencatatan perangkat yang dipasang (ONU/ONT, Router, Access Point) — untuk keperluan klaim garansi & penggantian.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| layanan_internet_id | FK | |
| serial_number | string | |
| mac_address | string, nullable | |
| merek | string | |
| tipe | string | mis. `ONT`, `Router`, `Access Point` |
| status | enum: `terpasang`, `dilepas`, `rusak` | |
| created_at, updated_at | timestamp | |

---

## 3.3 `riwayat_perubahan_paket`

Histori upgrade/downgrade. Snapshot lama & baru disimpan eksplisit (bukan JSON).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| layanan_internet_id | FK | |
| nama_paket_lama | string | |
| kecepatan_lama_mbps | integer | |
| harga_lama | decimal | |
| nama_paket_baru | string | |
| kecepatan_baru_mbps | integer | |
| harga_baru | decimal | |
| jenis_perubahan | enum: `upgrade`, `downgrade` | |
| diubah_oleh | FK → admin.id | |
| tanggal_perubahan | date | |
| created_at, updated_at | timestamp | |

---

## 3.4 `riwayat_relokasi`

Ringkasan alamat lama → baru, diisi otomatis saat `permohonan_layanan` (jenis `relokasi`) dikonversi. Detail proses lengkap (survey, jadwal) tetap ada di `permohonan_layanan` terkait.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| layanan_internet_id | FK | |
| permohonan_layanan_id | FK | referensi ke permohonan relokasi terkait |
| alamat_lama, rt_lama, rw_lama, kode_pos_lama | string/text | |
| latitude_lama, longitude_lama | decimal | |
| alamat_baru, rt_baru, rw_baru, kode_pos_baru | string/text | |
| latitude_baru, longitude_baru | decimal | |
| tanggal_relokasi | date | |
| created_at, updated_at | timestamp | |

## Relasi

- `permohonan_layanan` 0..1—0..1 `layanan_internet` (hasil konversi / rujukan relokasi)
- `layanan_internet` 1—N `perangkat`, `riwayat_perubahan_paket`, `riwayat_relokasi`, `tagihan`, `laporan_kendala`
