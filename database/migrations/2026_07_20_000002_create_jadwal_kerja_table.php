<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_layanan_id')
                ->constrained('permohonan_layanan')->cascadeOnDelete();
            // Satu pekerjaan dimiliki SATU tim, bukan satu teknisi individu —
            // semua anggota tim akses data yang sama (single source of truth).
            $table->foreignId('tim_teknisi_id')
                ->constrained('tim_teknisi')->restrictOnDelete();
            $table->date('tanggal_kerja');
            // selesai | kendala, null = belum dilaksanakan
            $table->string('hasil')->nullable();
            $table->text('catatan_kendala')->nullable();
            // Anggota tim MANA yang terakhir update — buat audit, bukan buat batasi akses
            $table->foreignId('diisi_oleh')->nullable()
                ->constrained('admin')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal_kerja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kerja');
    }
};