<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SintaSyncLog;
use Illuminate\Http\Request;
use App\Services\SintaCrawler;
use App\Services\DosenRankingService;
use App\Services\DosenMetricsAggregationService;

class DosenBerprestasiController extends Controller
{
    /**
     * Tampilkan ranking dosen berprestasi untuk tahun tertentu.
     */
    public function index(Request $request, DosenRankingService $rankingService)
    {
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        $user = auth()->user();
        $role = strtolower($user->role ?? '');

        try {
            $result  = $rankingService->hitungRanking($tahun);
            $ranking = $result['ranking'];
            $cr      = $result['cr'];
            $error   = null;
        } catch (\Throwable $e) {
            $ranking = collect();
            $cr      = null;
            $error   = $e->getMessage();
        }

        return view('tpk.dosen_berprestasi.index', [
            'tahun'   => $tahun,
            'ranking' => $ranking,
            'cr'      => $cr,
            'error'   => $error,
            'role'    => $role,
        ]);
    }

    /**
     * Sync data internal (Penelitian, P3M, Publikasi) dari SIMPATI.
     */
    public function syncInternal(Request $request, DosenMetricsAggregationService $metricsService)
    {
        // Cek apakah user adalah admin
        if (strtolower(auth()->user()->role ?? '') !== 'admin') {
            abort(403);
        }

        $tahun = (int) ($request->input('tahun') ?: now()->year);

        try {
            $metrics = $metricsService->aggregateForYear($tahun);
            $count   = $metrics->count();

            return redirect()
                ->route('tpk.dosen_berprestasi.index', ['tahun' => $tahun])
                ->with('status', "Sync data internal berhasil. {$count} baris metrics ter-update.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('tpk.dosen_berprestasi.index', ['tahun' => $tahun])
                ->with('error', 'Gagal sync data internal: ' . $e->getMessage());
        }
    }

    /**
     * Sync data SINTA (SINTA Score, SINTA 3Yr, Hibah, Scholar 1 thn).
     */
    public function syncSinta(
        Request $request,
        DosenMetricsAggregationService $metricsService,
        SintaCrawler $crawler
    ) {
        $tahun = (int) ($request->input('tahun') ?: now()->year);
        $user  = auth()->user();

        try {
            $metrics = $metricsService->aggregateFromSintaForYear($tahun, $crawler);
            $count   = $metrics->count();

            SintaSyncLog::create([
                'tahun'         => $tahun,
                'triggered_by'  => $user?->id,
                'source'        => 'web',
                'total_metrics' => $count,
                'status'        => 'success',
                'message'       => null,
            ]);

            return redirect()
                ->route('tpk.dosen_berprestasi.index', ['tahun' => $tahun])
                ->with('status', "Sync data SINTA berhasil. {$count} baris metrics ter-update.");
        } catch (\Throwable $e) {
            SintaSyncLog::create([
                'tahun'         => $tahun,
                'triggered_by'  => $user?->id,
                'source'        => 'web',
                'total_metrics' => 0,
                'status'        => 'failed',
                'message'       => $e->getMessage(),
            ]);

            return redirect()
                ->route('tpk.dosen_berprestasi.index', ['tahun' => $tahun])
                ->with('error', 'Gagal sync data SINTA: ' . $e->getMessage());
        }
    }
    /**
     * Export data ranking ke Excel (CSV).
     */
    public function exportExcel(Request $request, DosenRankingService $rankingService)
    {
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        try {
            $result  = $rankingService->hitungRanking($tahun);
            $ranking = $result['ranking'] ?? collect();
        } catch (\Throwable $e) {
            $ranking = collect();
        }

        $filename = "ranking-dosen-berprestasi-{$tahun}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($ranking) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'Peringkat',
                'Nama Dosen',
                'NIDN',
                'SINTA Score',
                'SINTA 3Yr',
                'Jumlah Hibah',
                'Publikasi Scholar (1 Thn)',
                'Jumlah Penelitian',
                'Jumlah P3M',
                'Jumlah Publikasi',
                'Skor Akhir'
            ], ';');

            // Data Rows
            foreach ($ranking as $row) {
                $nama = optional($row->dosen)->nama ?? (optional($row->user)->name ?? 'N/A');
                $nidn = optional($row->dosen)->nidn ?? 'N/A';

                fputcsv($file, [
                    $row->peringkat,
                    $nama,
                    $nidn,
                    $row->sinta_score,
                    $row->sinta_score_3yr,
                    $row->jumlah_hibah,
                    $row->publikasi_scholar_1th,
                    $row->jumlah_penelitian,
                    $row->jumlah_p3m,
                    $row->jumlah_publikasi,
                    number_format($row->skor_akhir, 6)
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
