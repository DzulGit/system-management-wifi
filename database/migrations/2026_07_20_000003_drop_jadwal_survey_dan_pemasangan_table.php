<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

// Revisi arsitektur: survey & pemasangan digabung jadi satu tahap kunjungan
// (jadwal_kerja). Lihat docs/arsitektur/business-flow.md versi terbaru.
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('jadwal_survey');
        Schema::dropIfExists('jadwal_pemasangan');
    }

    public function down(): void
    {
        // Sengaja tidak di-recreate di down() — revisi ini bersifat final,
        // rollback penuh berarti restore dari migration lama kalau memang perlu.
    }
};