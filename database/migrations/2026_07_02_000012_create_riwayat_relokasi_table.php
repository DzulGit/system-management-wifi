<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_relokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_internet_id')
                ->constrained('layanan_internet')->cascadeOnDelete();
            $table->foreignId('permohonan_layanan_id')
                ->constrained('permohonan_layanan')->restrictOnDelete();

            $table->text('alamat_lama');
            $table->string('rt_lama', 3);
            $table->string('rw_lama', 3);
            $table->string('kode_pos_lama', 5);
            $table->decimal('latitude_lama', 10, 7);
            $table->decimal('longitude_lama', 10, 7);

            $table->text('alamat_baru');
            $table->string('rt_baru', 3);
            $table->string('rw_baru', 3);
            $table->string('kode_pos_baru', 5);
            $table->decimal('latitude_baru', 10, 7);
            $table->decimal('longitude_baru', 10, 7);

            $table->date('tanggal_relokasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_relokasi');
    }
};
