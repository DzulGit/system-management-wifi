<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_internet', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_layanan')->unique();

            // Asal-usul layanan ini (permohonan yang dikonversi)
            $table->foreignId('permohonan_layanan_id')
                ->constrained('permohonan_layanan')->restrictOnDelete();

            $table->foreignId('pelanggan_id')
                ->constrained('pelanggan')->cascadeOnDelete();

            $table->foreignId('paket_internet_id')->nullable()
                ->constrained('paket_internet')->nullOnDelete();

            // reguler | custom
            $table->string('tipe_paket');
            $table->string('nama_paket_custom')->nullable();
            $table->unsignedInteger('kecepatan_custom_mbps')->nullable();
            $table->decimal('harga_custom', 12, 2)->nullable();

            $table->text('alamat_pemasangan');
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('kode_pos', 5);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // aktif | nonaktif
            $table->string('status')->default('aktif');

            // Basis tanggal siklus tagihan bulanan
            $table->date('tanggal_aktif');

            $table->timestamps();

            $table->index('status');
            $table->index('tanggal_aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_internet');
    }
};
