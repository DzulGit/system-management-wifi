# API — Keuangan

**Auth:** Bearer Token admin, `peran` harus `keuangan` atau `super_admin`.
Base path: `/api/admin/keuangan`

**Penting:** Keuangan bersifat **read-only**. Tidak ada endpoint create/update tagihan — sesuai requirement awal ("Keuangan tidak boleh mengubah data"). Tagihan hanya dibuat otomatis oleh sistem (`GenerateTagihanMassalJob`, jalan tiap hari via Scheduler).

---

## GET `/tagihan`
List seluruh tagihan (paginated, 20/halaman).

**Query Filter (opsional)**
| Param | Contoh | Keterangan |
|---|---|---|
| status_pembayaran | `belum_bayar` \| `sudah_bayar` | |
| periode_bulan | `7` | 1–12 |
| periode_tahun | `2026` | |

**Response 200**
```json
{
  "data": {
    "data": [
      {
        "id": 1,
        "nomor_tagihan": "INV000001",
        "periode_bulan": 7,
        "periode_tahun": 2026,
        "nama_paket_snapshot": "Paket Reguler 20 Mbps",
        "harga_snapshot": "250000.00",
        "total_tagihan": "250000.00",
        "tanggal_jatuh_tempo": "2026-07-15",
        "status_pembayaran": "belum_bayar",
        "layanan_internet": { "pelanggan": { "nama_lengkap": "Ani" } }
      }
    ],
    "current_page": 1,
    "total": 1
  }
}
```

## GET `/tagihan/{id}`
Detail 1 tagihan + relasi `layananInternet.pelanggan` dan histori `pembayaran`.

---

## Catatan Snapshot Billing

`nama_paket_snapshot`, `kecepatan_snapshot_mbps`, `harga_snapshot` adalah **salinan** data paket saat tagihan dibuat — bukan referensi live ke `paket_internet`/`layanan_internet`. Kalau pelanggan upgrade/downgrade paket setelah tagihan terbit, tagihan lama **tidak berubah**. Ini disengaja (lihat `docs/database/tabel-tagihan.md`).

## Menyusul
Integrasi Xendit (generate invoice link, webhook pembayaran) — lihat `docs/api/webhook-xendit.md`, statusnya masih rencana/belum diimplementasi.
