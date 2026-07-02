# Struktur Project Laravel & Konvensi Penamaan

## Prinsip

Controller **tidak boleh** berisi query Eloquent langsung. Business logic (state machine, generate nomor, snapshot data) ada di **Service**. Akses data dibungkus **Repository** agar mudah di-unit-test dengan mock — sesuai kebutuhan "Unit Test untuk Service, Repository, Business Logic".

## Struktur Folder

```
app/
├── Console/
│   └── Commands/
│       └── GenerateTagihanBulanan.php        # dipanggil scheduler
│
├── Enums/
│   ├── PeranAdminEnum.php                    # super_admin, operasional, teknisi, keuangan
│   ├── JenisPermohonanEnum.php               # pemasangan_baru, relokasi
│   ├── StatusPermohonanEnum.php
│   ├── StatusLayananEnum.php                 # aktif, nonaktif
│   ├── StatusLaporanEnum.php
│   ├── TipePaketEnum.php
│   ├── StatusPembayaranEnum.php
│   └── JenisPerubahanPaketEnum.php
│
├── Http/
│   ├── Controllers/Api/
│   │   ├── Auth/
│   │   │   ├── AuthAdminController.php
│   │   │   └── AuthPelangganController.php
│   │   ├── Operasional/
│   │   │   ├── PermohonanLayananController.php
│   │   │   ├── PaketInternetController.php
│   │   │   └── LaporanKendalaController.php
│   │   ├── Teknisi/
│   │   │   ├── JadwalSurveyController.php
│   │   │   └── JadwalPemasanganController.php
│   │   ├── Keuangan/
│   │   │   └── TagihanController.php
│   │   ├── SuperAdmin/
│   │   │   └── AdminController.php            # CRUD akun admin
│   │   ├── Pelanggan/
│   │   │   ├── ProfilController.php
│   │   │   ├── LayananSayaController.php
│   │   │   ├── TagihanSayaController.php
│   │   │   └── LaporanKendalaSayaController.php
│   │   └── Webhook/
│   │       └── XenditWebhookController.php
│   │
│   ├── Requests/
│   │   ├── PermohonanLayanan/
│   │   │   ├── SimpanPermohonanRequest.php
│   │   │   ├── UbahStatusPermohonanRequest.php
│   │   │   └── UpgradeDowngradePaketRequest.php
│   │   └── ...
│   │
│   ├── Resources/                             # API Resource (response formatting)
│   └── Middleware/
│
├── Models/
│   ├── Admin.php
│   ├── Pelanggan.php
│   ├── PaketInternet.php
│   ├── PermohonanLayanan.php
│   ├── JadwalSurvey.php
│   ├── JadwalPemasangan.php
│   ├── RiwayatStatusPermohonan.php
│   ├── LayananInternet.php
│   ├── Perangkat.php
│   ├── RiwayatPerubahanPaket.php
│   ├── RiwayatRelokasi.php
│   ├── Tagihan.php
│   ├── Pembayaran.php
│   ├── LaporanKendala.php
│   └── AuditLog.php
│
├── Policies/
│   ├── PermohonanLayananPolicy.php
│   ├── LayananInternetPolicy.php
│   ├── TagihanPolicy.php
│   └── LaporanKendalaPolicy.php
│
├── Repositories/
│   ├── Contracts/                             # interface (DIP - SOLID)
│   │   ├── PermohonanLayananRepositoryInterface.php
│   │   ├── LayananInternetRepositoryInterface.php
│   │   ├── TagihanRepositoryInterface.php
│   │   └── ...
│   └── Eloquent/
│       ├── PermohonanLayananRepository.php
│       ├── LayananInternetRepository.php
│       ├── TagihanRepository.php
│       └── ...
│
├── Services/
│   ├── PermohonanLayananService.php            # validasi state machine status permohonan
│   ├── KonversiPermohonanService.php           # konversi permohonan → layanan_internet
│   ├── GenerateNomorPermohonanService.php
│   ├── GenerateNomorLayananService.php
│   ├── GenerateNomorPelangganService.php
│   ├── AktivasiAkunPelangganService.php
│   ├── UpgradeDowngradePaketService.php
│   ├── GenerateTagihanService.php              # + snapshot paket
│   ├── PembayaranService.php                   # proses webhook Xendit
│   ├── LaporanKendalaService.php
│   └── AuditLogService.php
│
├── Events/
│   ├── PermohonanDikonversi.php
│   ├── TagihanDibuat.php
│   └── PembayaranBerhasil.php
│
├── Listeners/
│   ├── KirimEmailAktivasi.php
│   ├── KirimEmailInvoice.php
│   └── KirimEmailStatusPembayaran.php
│
├── Jobs/
│   └── GenerateTagihanMassalJob.php
│
├── Notifications/
│   ├── EmailAktivasiAkun.php
│   ├── EmailInvoiceBaru.php
│   └── EmailStatusPembayaran.php
│
├── Observers/
│   └── AuditLogObserver.php                    # opsional, catat perubahan model penting
│
└── Exceptions/
    └── TransisiStatusTidakValidException.php

database/
├── migrations/
├── factories/
└── seeders/
    └── SuperAdminSeeder.php

tests/
├── Feature/
│   ├── Auth/
│   ├── PermohonanLayanan/
│   ├── LayananInternet/
│   ├── Tagihan/
│   └── LaporanKendala/
└── Unit/
    ├── Services/
    └── Repositories/

routes/
├── api.php
└── api_webhook.php                              # rute khusus webhook, tanpa auth standar
```

## Konvensi Penamaan (contoh konkret)

| Jenis | Bahasa Indonesia (dipakai) | Bahasa Inggris (tidak dipakai) |
|---|---|---|
| Tabel | `permohonan_layanan` | ~~service_applications~~ |
| Kolom | `tanggal_aktif` | ~~activated_at~~ |
| Model | `PermohonanLayanan` | ~~ServiceApplication~~ |
| Service | `KonversiPermohonanService` | ~~ApplicationConversionService~~ |
| Repository | `TagihanRepository` | ~~InvoiceRepository~~ |
| Controller | `JadwalSurveyController` | ~~SurveyScheduleController~~ |
| Request | `UbahStatusPermohonanRequest` | ~~UpdateApplicationStatusRequest~~ |
| Enum | `StatusPermohonanEnum` | ~~ApplicationStatusEnum~~ |
| Policy | `TagihanPolicy` | ~~InvoicePolicy~~ |

**Pengecualian:** nama file/kelas bawaan Laravel (`Controller`, `FormRequest`, `Model`, `Middleware`, `ServiceProvider`, dsb) tetap Bahasa Inggris karena itu konvensi framework.
