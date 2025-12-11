<?php

namespace App\Console\Commands;

use App\Services\DosenMetricsAggregationService;
use Illuminate\Console\Command;

class AggregateDosenMetrics extends Command
{
    protected $signature = 'dosen:aggregate-metrics {tahun?}';
    protected $description = 'Hitung jumlah penelitian, P3M, dan publikasi dosen untuk tahun tertentu';

    public function handle(DosenMetricsAggregationService $service): int
    {
        $tahun = (int) ($this->argument('tahun') ?: now()->year);

        $this->info("Mengagregasi metrics dosen untuk tahun {$tahun} ...");

        $metrics = $service->aggregateForYear($tahun);

        $this->info("Selesai. Jumlah baris metrics: " . $metrics->count());

        return self::SUCCESS;
    }
}
