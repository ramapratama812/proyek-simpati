<?php

namespace App\Console\Commands;

use App\Services\DosenMetricsAggregationService;
use App\Services\SintaCrawler;
use Illuminate\Console\Command;

class SyncDosenSintaMetrics extends Command
{
    protected $signature = 'dosen:sync-sinta-metrics {tahun?}';
    protected $description = 'Sinkronkan Skor SINTA, SINTA 3Yr, Jumlah Hibah dan Publikasi GS 1 tahun dari SINTA ke dosen_performance_metrics';

    public function handle(
        DosenMetricsAggregationService $metricsService,
        SintaCrawler $crawler
    ): int
    {
        $tahun = (int) ($this->argument('tahun') ?: now()->year);

        $this->info("Sinkronisasi data SINTA untuk tahun {$tahun} ...");

        $metrics = $metricsService->aggregateFromSintaForYear($tahun, $crawler);

        $this->info("Selesai. Data metrics yang ter-update: " . $metrics->count());

        return self::SUCCESS;
    }
}
