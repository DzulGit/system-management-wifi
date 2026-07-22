<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan')->unique();

            $table->foreignId('pelanggan_id')
                ->constrained('pelanggan')->cascadeOnDelete();

            // pemasangan_baru | relokasi
            $table->string('jenis_permohonan');

            // Diisi HANYA jika jenis_permohonan = relokasi (menunjuk layanan yang direlokasi).
            // FK constraint ditambahkan di migration terpisah karena referensi melingkar
            // dengan tabel layanan_internet (dibuat setelah tabel ini).
            $table->unsignedBigInteger('layanan_internet_id')->nullable();

            $table->foreignId('paket_internet_id')->nullable()
                ->constrained('paket_internet')->nullOnDelete();

            // reguler | custom
            $table->string('tipe_paket');
            $table->string('nama_paket_custom')->nullable();
            $table->unsignedInteger('kecepatan_custom_mbps')->nullable();
            $table->decimal('harga_custom', 12, 2)->nullable();
            $table->text('catatan_custom')->nullable();

            $table->text('alamat_pemasangan');
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('kode_pos', 5);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // MENUNGGU_VERIFIKASI | PERLU_REVISI | DITERIMA | DITOLAK |
            // DIJADWALKAN | SURVEY | PEMASANGAN | DITUNDA | DIKONVERSI
            $table->string('status')->default('MENUNGGU_VERIFIKASI');

            $table->text('alasan_ditolak')->nullable();
            $table->text('alasan_ditunda')->nullable();

            $table->foreignId('diproses_oleh')->nullable()
                ->constrained('admin')->nullOnDelete();

            $table->timestamps();

            $table->index('layanan_internet_id');
            $table->index('status');
            $table->index('jenis_permohonan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_layanan');
    }
};
