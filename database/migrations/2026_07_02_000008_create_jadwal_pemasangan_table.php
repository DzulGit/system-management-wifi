<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pemasangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_layanan_id')
                ->constrained('permohonan_layanan')->cascadeOnDelete();
            $table->foreignId('admin_id') // teknisi bertugas
                ->constrained('admin')->restrictOnDelete();
            $table->date('tanggal_pemasangan');
            // selesai | ditunda
            $table->string('hasil')->nullable();
            $table->text('alasan_penundaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pemasangan');
    }
};
