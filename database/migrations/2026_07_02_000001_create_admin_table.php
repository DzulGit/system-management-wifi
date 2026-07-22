<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('password');
            // Level akses: super_admin (pengelola sistem) + 3 peran bisnis
            $table->string('peran'); // super_admin | operasional | teknisi | keuangan
            $table->boolean('status_aktif')->default(true);
            $table->foreignId('dibuat_oleh')->nullable()
                ->constrained('admin')->nullOnDelete();
            $table->timestamps();

            $table->index('peran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
