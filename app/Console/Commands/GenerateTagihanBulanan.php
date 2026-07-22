<?php

namespace App\Console\Commands;

use App\Jobs\GenerateTagihanMassalJob;
use Illuminate\Console\Command;

class GenerateTagihanBulanan extends Command
{
    protected $signature = 'tagihan:generate-bulanan';

    protected $description = 'Dispatch job generate tagihan untuk semua layanan yang tanggal_aktif-nya jatuh hari ini';

    public function handle(): void
    {
        $sekarang = now();

        GenerateTagihanMassalJob::dispatch(
            $sekarang->day,
            $sekarang->month,
            $sekarang->year,
        );

        $this->info("Job generate tagihan untuk tanggal {$sekarang->toDateString()} berhasil di-dispatch.");
    }
}