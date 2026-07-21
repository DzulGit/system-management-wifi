<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tim_teknisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tim');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('tim_teknisi_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_teknisi_id')->constrained('tim_teknisi')->cascadeOnDelete();
            // Harus admin dengan peran teknisi — divalidasi di level Request, bukan FK constraint
            $table->foreignId('admin_id')->constrained('admin')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tim_teknisi_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tim_teknisi_anggota');
        Schema::dropIfExists('tim_teknisi');
    }
};