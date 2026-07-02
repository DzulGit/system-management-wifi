<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status_permohonan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_layanan_id')
                ->constrained('permohonan_layanan')->cascadeOnDelete();
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_sesudahnya');
            $table->foreignId('diubah_oleh')->nullable()
                ->constrained('admin')->nullOnDelete();
            $table->text('catatan')->nullable();
            // Insert-only log: tidak perlu updated_at
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_permohonan');
    }
};
