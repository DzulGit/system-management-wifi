# API — Webhook Xendit (BELUM DIIMPLEMENTASI)

> **Status: rencana desain, bukan kode yang sudah jadi.** Xendit sengaja ditunda ke fase berikutnya sesuai keputusan awal ("Xendit payment gateway integration — explicitly deferred to later phase"). Dokumen ini disiapkan lebih awal supaya kontraknya jelas begitu modul ini digarap.

---

## Rencana Alur

```
Tagihan dibuat (GenerateTagihanMassalJob)
        │
        ▼
Generate invoice Xendit (API call ke Xendit)
        │
        ▼
Simpan xendit_invoice_id + xendit_invoice_url di tabel tagihan
        │
        ▼
Pelanggan bayar via link Xendit
        │
        ▼
Xendit kirim webhook  →  POST /api/webhook/xendit
        │
        ▼
WAJIB validasi signature/token sebelum diproses
        │
        ▼
PembayaranService:
  1. Simpan payload_webhook ke tabel `pembayaran` (audit)
  2. Update tagihan.status_pembayaran = sudah_bayar
  3. Trigger event PembayaranBerhasil → kirim email
```

## Rencana Endpoint

### POST `/webhook/xendit`

**Tanpa Sanctum** (bukan request dari pelanggan/admin, tapi dari server Xendit) — proteksinya lewat **validasi token/signature** dari header yang dikirim Xendit (`x-callback-token` atau signature HMAC, tergantung jenis produk Xendit yang dipakai), dicek manual di `XenditWebhookController` sebelum payload diproses sama sekali.

**Wajib diterapkan saat implementasi nanti:**
- Rute ini **harus** dikecualikan dari CSRF (otomatis aman untuk API stateless, tapi tetap perlu double-check).
- Signature/token tidak valid → tolak dengan `401`, **jangan proses apapun**.
- Payload mentah selalu disimpan ke `pembayaran.payload_webhook` (JSON) untuk audit/troubleshooting, terlepas dari valid atau tidak — tapi hanya proses lanjut (update status tagihan) kalau valid.
- Idempotency: kalau Xendit mengirim webhook yang sama dua kali (retry), jangan sampai `tagihan.status_pembayaran` diproses dobel atau `PembayaranBerhasil` event terkirim dua kali.

## Yang Perlu Diputuskan Saat Implementasi

- Produk Xendit yang dipakai (Invoice API vs Payment Link vs lainnya) — ini menentukan bentuk payload & mekanisme validasi signature-nya.
- Field mapping persis dari payload Xendit ke kolom `pembayaran` (`metode_pembayaran`, `referensi_xendit`, dll).
