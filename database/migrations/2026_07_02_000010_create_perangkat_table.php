<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perangkat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_internet_id')
                ->constrained('layanan_internet')->cascadeOnDelete();
            $table->string('serial_number');
            $table->string('mac_address')->nullable();
            $table->string('merek');
            $table->string('tipe'); // mis. ONT, Router, Access Point
            // terpasang | dilepas | rusak
            $table->string('status')->default('terpasang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perangkat');
    }
};
