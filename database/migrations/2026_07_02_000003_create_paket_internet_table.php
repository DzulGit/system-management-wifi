<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_internet', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->unsignedInteger('kecepatan_mbps');
            $table->decimal('harga', 12, 2);
            $table->text('deskripsi')->nullable();
            // Soft-disable: paket lama tetap ada karena masih direferensikan layanan lama
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_internet');
    }
};
