<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan')->unique();
            $table->foreignId('layanan_internet_id')
                ->constrained('layanan_internet')->restrictOnDelete();

            $table->unsignedTinyInteger('periode_bulan');
            $table->unsignedSmallInteger('periode_tahun');

            // Snapshot paket saat invoice dibuat, agar histori tidak berubah
            // walau paket di-upgrade/downgrade di kemudian hari.
            $table->string('nama_paket_snapshot');
            $table->unsignedInteger('kecepatan_snapshot_mbps');
            $table->decimal('harga_snapshot', 12, 2);

            $table->decimal('total_tagihan', 12, 2);
            $table->date('tanggal_jatuh_tempo');

            // belum_bayar | sudah_bayar
            $table->string('status_pembayaran')->default('belum_bayar');

            $table->string('xendit_invoice_id')->nullable();
            $table->string('xendit_invoice_url')->nullable();
            $table->timestamp('dibayar_pada')->nullable();

            $table->timestamps();

            $table->unique(
                ['layanan_internet_id', 'periode_bulan', 'periode_tahun'],
                'tagihan_unik_per_periode'
            );
            $table->index('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
