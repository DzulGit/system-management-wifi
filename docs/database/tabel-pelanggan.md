# Tabel: `pelanggan`

Identitas pelanggan. **Tidak menyimpan status proses pemasangan** — status ada di `permohonan_layanan` dan `layanan_internet`.

## Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | uuid / bigint PK | |
| nomor_pelanggan | string, unique, nullable | diisi otomatis saat layanan **pertama** dikonversi jadi AKTIF. Format `PLG000001` |
| nama_lengkap | string | |
| nik | string(16), unique | satu orang = satu identitas pelanggan |
| nomor_hp | string, unique | dipakai untuk login pertama & komunikasi |
| email | string | |
| password | string (hashed), nullable | null selama belum set password pertama kali |
| password_sudah_dibuat | boolean, default false | gate akses dashboard |
| foto_ktp | string (path Supabase Storage) | database hanya simpan path |
| foto_selfie_ktp | string (path Supabase Storage) | |
| created_at, updated_at | timestamp | |

## Catatan Desain

- `nomor_pelanggan` **nullable** di awal karena baris `pelanggan` dibuat saat submit permohonan (untuk cek NIK/nomor_hp unik), sebelum tentu disetujui. Nomor baru diisi setelah layanan pertama resmi AKTIF.
- Relokasi & tambah layanan **tidak** membuat baris `pelanggan` baru — tetap pakai baris yang sama, hanya menambah baris di `permohonan_layanan`/`layanan_internet`.
- Login pertama: `nomor_pelanggan` + `nomor_hp` (tanpa password) → wajib set password → `password_sudah_dibuat = true`. Login berikutnya: `nomor_pelanggan` + `password`.

## Relasi

- `pelanggan` 1—N `permohonan_layanan`
- `pelanggan` 1—N `layanan_internet`
