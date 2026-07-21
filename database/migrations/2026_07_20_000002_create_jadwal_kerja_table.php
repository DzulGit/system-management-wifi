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
            // NULLABLE — diisi kalau Operasional pakai shortcut "pilih tim baku"
            // (buat referensi/laporan saja). Yang MENENTUKAN siapa yang benar-benar
            // akses & bisa update pekerjaan ini adalah tabel jadwal_kerja_teknisi
            // di bawah, bukan kolom ini — supaya assign manual per-individu (tanpa
            // pilih tim) juga tetap didukung penuh.
            $table->foreignId('tim_teknisi_id')->nullable()
                ->constrained('tim_teknisi')->nullOnDelete();
            $table->date('tanggal_kerja');
            // selesai | kendala, null = belum dilaksanakan
            $table->string('hasil')->nullable();
            $table->text('catatan_kendala')->nullable();
            // Anggota MANA yang terakhir update — audit, bukan pembatas akses
            $table->foreignId('diisi_oleh')->nullable()
                ->constrained('admin')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal_kerja');
        });

        // Single source of truth siapa yang ditugaskan ke pekerjaan INI spesifik —
        // entah asalnya dari "pilih tim" (semua anggota tim di-insert ke sini)
        // atau assign manual satu-satu. Sama-sama berakhir di tabel yang sama.
        Schema::create('jadwal_kerja_teknisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kerja_id')->constrained('jadwal_kerja')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admin')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['jadwal_kerja_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kerja_teknisi');
        Schema::dropIfExists('jadwal_kerja');
    }
};