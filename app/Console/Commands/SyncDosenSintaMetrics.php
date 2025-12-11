<?php

namespace App\Console\Commands;

use App\Models\SintaSyncLog;
use App\Services\SintaCrawler;
use Illuminate\Console\Command;
use App\Services\DosenMetricsAggregationService;

class SyncDosenSintaMetrics extends Command
{
    protected $signature = 'dosen:sync-sinta-metrics {tahun?}';
    protected $description = 'Sinkronkan Skor SINTA, SINTA 3Yr, Jumlah Hibah dan Publikasi GS 1 tahun dari SINTA ke dosen_performance_metrics';

    public function handle(
        DosenMetricsAggregationService $service,
        SintaCrawler $crawler
    ): int {
        $tahun = (int) ($this->argument('tahun') ?: now()->year);

        $this->info("Sinkronisasi data SINTA untuk tahun {$tahun} ...");

        try {
            $metrics = $service->aggregateFromSintaForYear($tahun, $crawler);
            $count   = $metrics->count();

            SintaSyncLog::create([
                'tahun'         => $tahun,
                'triggered_by'  => null,
                'source'        => 'console',
                'total_metrics' => $count,
                'status'        => 'success',
                'message'       => null,
            ]);

            $this->info("Selesai. Data metrics yang ter-update: {$count}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            SintaSyncLog::create([
                'tahun'         => $tahun,
                'triggered_by'  => null,
                'source'        => 'console',
                'total_metrics' => 0,
                'status'        => 'failed',
                'message'       => $e->getMessage(),
            ]);

            $this->error('Gagal sync data SINTA: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
