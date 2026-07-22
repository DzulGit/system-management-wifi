<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_survey', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_layanan_id')
                ->constrained('permohonan_layanan')->cascadeOnDelete();
            $table->foreignId('admin_id') // teknisi bertugas
                ->constrained('admin')->restrictOnDelete();
            $table->date('tanggal_survey');
            // berhasil | kendala, null = belum dilaksanakan
            $table->string('hasil')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_survey');
    }
};
