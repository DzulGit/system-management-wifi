# Tabel: `laporan_kendala`

Terikat ke **layanan_internet**, bukan langsung ke pelanggan — karena satu pelanggan bisa punya banyak layanan, kendala harus jelas terjadi di layanan mana.

## Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nomor_laporan | string, unique | |
| layanan_internet_id | FK | |
| kategori_kendala | string | |
| deskripsi | text | |
| status | enum: `menunggu`, `diproses`, `ditugaskan`, `selesai`, `ditutup` | |
| ditugaskan_ke | FK → admin.id, nullable | teknisi |
| hasil_penanganan | text, nullable | |
| ditutup_oleh | FK → admin.id, nullable | operasional |
| created_at, updated_at | timestamp | |

## State Machine

```
MENUNGGU → DIPROSES → DITUGASKAN → SELESAI → DITUTUP
```

Operasional bisa langsung `DITUTUP` dari `MENUNGGU`/`DIPROSES` tanpa lewat Teknisi (mis. laporan duplikat). Lihat detail alur di `docs/arsitektur/business-flow.md`.

## Relasi

- `layanan_internet` 1—N `laporan_kendala`
- `admin` 1—N `laporan_kendala` (via `ditugaskan_ke`, `ditutup_oleh`)
