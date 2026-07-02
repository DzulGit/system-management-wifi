<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_perubahan_paket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_internet_id')
                ->constrained('layanan_internet')->cascadeOnDelete();

            $table->string('nama_paket_lama');
            $table->unsignedInteger('kecepatan_lama_mbps');
            $table->decimal('harga_lama', 12, 2);

            $table->string('nama_paket_baru');
            $table->unsignedInteger('kecepatan_baru_mbps');
            $table->decimal('harga_baru', 12, 2);

            // upgrade | downgrade
            $table->string('jenis_perubahan');

            $table->foreignId('diubah_oleh')
                ->constrained('admin')->restrictOnDelete();
            $table->date('tanggal_perubahan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_perubahan_paket');
    }
};
