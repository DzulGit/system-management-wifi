<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')
                ->constrained('tagihan')->cascadeOnDelete();
            $table->string('metode_pembayaran')->nullable();
            $table->decimal('jumlah_dibayar', 12, 2)->nullable();
            $table->string('referensi_xendit')->nullable();
            // pending | berhasil | gagal
            $table->string('status')->default('pending');
            // Payload mentah webhook, untuk audit/troubleshooting
            $table->json('payload_webhook')->nullable();
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
