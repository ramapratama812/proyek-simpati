<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $role = strtolower($user->role ?? ''); // asumsi kolom 'role' ada di tabel users

        try {
            $ranking = $rankingService->hitungRanking($tahun);
            $error   = null;
        } catch (\Throwable $e) {
            $ranking = collect();
            $error   = $e->getMessage();
        }

        return view('tpk.dosen_berprestasi.index', [
            'tahun'   => $tahun,
            'ranking' => $ranking,
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
    )
    {
        // Cek apakah user adalah admin
        if (strtolower(auth()->user()->role ?? '') !== 'admin') {
            abort(403);
        }

        $tahun = (int) ($request->input('tahun') ?: now()->year);

        try {
            $metrics = $metricsService->aggregateFromSintaForYear($tahun, $crawler);
            $count   = $metrics->count();

            return redirect()
                ->route('tpk.dosen_berprestasi.index', ['tahun' => $tahun])
                ->with('status', "Sync data SINTA berhasil. {$count} baris metrics ter-update.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('tpk.dosen_berprestasi.index', ['tahun' => $tahun])
                ->with('error', 'Gagal sync data SINTA: ' . $e->getMessage());
        }
    }
}
