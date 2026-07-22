<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kendala', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique();
            $table->foreignId('layanan_internet_id')
                ->constrained('layanan_internet')->cascadeOnDelete();
            $table->string('kategori_kendala');
            $table->text('deskripsi');
            // menunggu | diproses | ditugaskan | selesai | ditutup
            $table->string('status')->default('menunggu');
            $table->foreignId('ditugaskan_ke')->nullable()
                ->constrained('admin')->nullOnDelete();
            $table->text('hasil_penanganan')->nullable();
            $table->foreignId('ditutup_oleh')->nullable()
                ->constrained('admin')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kendala');
    }
};
