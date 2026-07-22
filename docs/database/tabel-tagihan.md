# Modul: Tagihan & Pembayaran

Mencakup 2 tabel: `tagihan`, `pembayaran`.

Tagihan dibuat **per layanan** (bukan per pelanggan), otomatis tiap bulan sesuai `tanggal_aktif` layanan masing-masing.

---

## 4.1 `tagihan`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nomor_tagihan | string, unique | |
| layanan_internet_id | FK | |
| periode_bulan | tinyint | 1–12 |
| periode_tahun | smallint | |
| nama_paket_snapshot | string | **disalin** saat invoice dibuat |
| kecepatan_snapshot_mbps | integer | **disalin** saat invoice dibuat |
| harga_snapshot | decimal | **disalin** saat invoice dibuat |
| total_tagihan | decimal | |
| tanggal_jatuh_tempo | date | |
| status_pembayaran | enum: `belum_bayar`, `sudah_bayar` | |
| xendit_invoice_id | string, nullable | |
| xendit_invoice_url | string, nullable | |
| dibayar_pada | timestamp, nullable | |
| created_at, updated_at | timestamp | |

**Unique constraint:** (`layanan_internet_id`, `periode_bulan`, `periode_tahun`) — mencegah tagihan ganda di bulan yang sama.

> **Kenapa snapshot?** Jika paket di-upgrade bulan depan, tagihan bulan lalu harus tetap menunjukkan harga lama. Data tidak diambil live dari `paket_internet`/`layanan_internet`.

---

## 4.2 `pembayaran`

Log transaksi aktual — dipisah dari status tagihan agar histori percobaan pembayaran/webhook tetap tercatat untuk audit.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tagihan_id | FK | |
| metode_pembayaran | string, nullable | terisi dari data Xendit |
| jumlah_dibayar | decimal | |
| referensi_xendit | string, nullable | |
| status | enum: `pending`, `berhasil`, `gagal` | |
| payload_webhook | json | payload mentah, untuk audit/troubleshooting |
| dibayar_pada | timestamp, nullable | |
| created_at, updated_at | timestamp | |

## Catatan Keamanan

- Webhook Xendit **wajib** divalidasi (signature/token) sebelum data diproses — lihat `docs/api/webhook-xendit.md` (menyusul).
- MVP belum ada denda keterlambatan — hanya `belum_bayar` / `sudah_bayar`.

## Relasi

- `layanan_internet` 1—N `tagihan`
- `tagihan` 1—N `pembayaran`
