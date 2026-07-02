<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration terpisah untuk menutup referensi melingkar:
// permohonan_layanan.layanan_internet_id -> layanan_internet.id
// (tabel layanan_internet baru ada setelah migration sebelumnya dijalankan)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_layanan', function (Blueprint $table) {
            $table->foreign('layanan_internet_id')
                ->references('id')->on('layanan_internet')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_layanan', function (Blueprint $table) {
            $table->dropForeign(['layanan_internet_id']);
        });
    }
};
