# Alur Bisnis & State Machine

## 1. Pendaftaran (Pemasangan Baru)

```
Landing Page (Pelanggan baru submit form)
        │
        ▼
Buat/cocokkan data pelanggan (cek unik NIK & nomor_hp)
        │
        ▼
Buat permohonan_layanan (jenis_permohonan = pemasangan_baru)
status = MENUNGGU_VERIFIKASI
```

## 2. Verifikasi (Operasional)

Operasional dapat: **terima**, **tolak**, atau **minta revisi**.

```
MENUNGGU_VERIFIKASI
      │
      ├──► PERLU_REVISI ──(pelanggan revisi)──► MENUNGGU_VERIFIKASI
      │
      ├──► DITOLAK   [status akhir]
      │
      ▼
   DITERIMA
      │
      ▼ (Operasional buat jadwal_survey)
  DIJADWALKAN
```

## 3. Survey & Pemasangan (Teknisi)

```
DIJADWALKAN
      │
      ▼
    SURVEY
      │
      ├──► DITUNDA (wajib isi alasan) ──(dijadwalkan ulang)──► SURVEY
      │
      ▼ (survey berhasil, buat jadwal_pemasangan)
  PEMASANGAN
      │
      ├──► DITUNDA (wajib isi alasan) ──(dijadwalkan ulang)──► PEMASANGAN
      │
      ▼ (Teknisi klik "Pemasangan Selesai")
  DIKONVERSI   [status akhir permohonan]
```

**Setiap transisi status dicatat di `riwayat_status_permohonan`** (status sebelumnya, status sesudah, siapa yang mengubah, kapan).

## 4. Konversi Menjadi Layanan Aktif

Saat Teknisi memilih **"Pemasangan Selesai"**, sistem (via `KonversiPermohonanService`) menjalankan dalam satu **Database Transaction**:

**Jika `jenis_permohonan = pemasangan_baru`:**
1. Buat baris baru di `layanan_internet` (copy alamat, paket, koordinat dari permohonan).
2. Generate `nomor_layanan` (format `LYN000001`, unik).
3. Set `layanan_internet.status = AKTIF`, `tanggal_aktif = hari ini`.
4. **Jika ini layanan pertama pelanggan tsb** → generate `nomor_pelanggan` (format `PLG000001`) dan set `pelanggan.password_sudah_dibuat = false` (menunggu set password pertama).
5. Ubah `permohonan_layanan.status = DIKONVERSI`.

**Jika `jenis_permohonan = relokasi`:**
1. Update alamat, RT/RW, kode pos, koordinat pada `layanan_internet` yang dirujuk (`permohonan_layanan.layanan_internet_id`).
2. Catat baris baru di `riwayat_relokasi` (alamat lama → alamat baru).
3. Ubah `permohonan_layanan.status = DIKONVERSI`.
4. **Tidak** membuat `layanan_internet` baru, **tidak** generate nomor baru.

## 5. Login Pertama & Aktivasi Akun

```
Login pakai: nomor_pelanggan + nomor_hp (tanpa password)
        │
        ▼
Wajib buat password baru
        │
        ▼
password_sudah_dibuat = true
        │
        ▼
Baru boleh akses dashboard
```

Login berikutnya: `nomor_pelanggan` + `password`.

## 6. Tambah Layanan (Pelanggan Lama)

Operasional memilih pelanggan yang sudah ada → buat `permohonan_layanan` baru (`jenis_permohonan = pemasangan_baru`, `pelanggan_id` = pelanggan yang sudah ada). Alur selanjutnya **sama persis** dengan pendaftaran baru (survey → pemasangan → konversi), kecuali langkah generate `nomor_pelanggan` di-skip karena sudah ada.

## 7. Relokasi

Operasional/Pelanggan mengajukan `permohonan_layanan` (`jenis_permohonan = relokasi`, `layanan_internet_id` diisi). Alur survey → pemasangan tetap dijalankan (karena lokasi baru butuh pengecekan ODP & instalasi ulang), tapi hasil konversinya **meng-update** layanan yang sudah ada, bukan membuat baru (lihat bagian 4).

## 8. Upgrade / Downgrade Paket

**Tidak melalui `permohonan_layanan`** — tidak butuh kunjungan teknisi/survey fisik.

```
Operasional pilih layanan_internet → pilih paket baru
        │
        ▼
UpgradeDowngradePaketService:
  1. Catat riwayat_perubahan_paket (paket lama vs paket baru)
  2. Update layanan_internet.paket_internet_id (atau kolom custom)
```

## 9. Paket Custom

```
Pelanggan pilih "Paket Custom" di form
  → isi kecepatan diinginkan, estimasi perangkat, jenis penggunaan, kebutuhan khusus
        │
        ▼
permohonan_layanan.tipe_paket = custom (paket_internet_id = null)
        │
        ▼
Operasional negosiasi harga
        │
        ├──► Setuju sebagai penawaran khusus → isi harga_custom, lanjut proses normal
        ├──► Setuju jadi paket reguler baru → Operasional CRUD paket_internet terpisah (manual)
        └──► Tolak → permohonan_layanan.status = DITOLAK
```

## 10. Tagihan Bulanan (Otomatis)

```
Scheduler harian (Laravel Task Scheduling)
        │
        ▼
Cari layanan_internet dengan status = AKTIF
  DAN tanggal (hari-bulan) tanggal_aktif == hari ini
        │
        ▼
GenerateTagihanMassalJob (via Queue)
        │
        ▼
Buat baris tagihan baru dengan SNAPSHOT paket saat ini:
  nama_paket_snapshot, kecepatan_snapshot_mbps, harga_snapshot
  (agar histori tagihan tidak berubah walau paket di-upgrade nanti)
```

Constraint unik: (`layanan_internet_id`, `periode_bulan`, `periode_tahun`) — mencegah tagihan dobel.

## 11. Pembayaran (Xendit)

```
Tagihan dibuat → generate invoice Xendit → simpan xendit_invoice_id/url
        │
        ▼
Pelanggan bayar via link Xendit
        │
        ▼
Webhook masuk → XenditWebhookController
        │
        ▼
WAJIB validasi signature/token webhook sebelum diproses
        │
        ▼
PembayaranService:
  1. Simpan payload_webhook ke tabel pembayaran (untuk audit)
  2. Update tagihan.status_pembayaran = sudah_bayar
  3. Trigger event PembayaranBerhasil → kirim email
```

## 12. Laporan Kendala

```
Pelanggan buat laporan (terikat ke layanan_internet tertentu)
status = MENUNGGU
        │
        ▼
Operasional terima → DIPROSES
        │
        ▼
Operasional teruskan ke Teknisi → DITUGASKAN
        │
        ▼
Teknisi selesaikan pekerjaan → SELESAI
        │
        ▼
Operasional pastikan pelanggan puas → DITUTUP  [status akhir]
```

Operasional juga bisa langsung **DITUTUP** dari status `MENUNGGU`/`DIPROSES` jika ternyata tidak perlu diteruskan ke Teknisi (mis. laporan duplikat atau bukan gangguan teknis).
