<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            // Polymorphic sederhana, tanpa FK formal (mencatat aksi dari berbagai sumber)
            $table->unsignedBigInteger('pelaku_id')->nullable();
            $table->string('tipe_pelaku')->nullable(); // admin | pelanggan | sistem
            $table->string('aksi');
            $table->string('modul');
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->string('alamat_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tipe_pelaku', 'pelaku_id']);
            $table->index('modul');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
