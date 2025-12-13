<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\DosenPerformanceMetric;
use App\Models\Publication;
use App\Models\ResearchProject;
use Illuminate\Support\Collection;

class DosenMetricsAggregationService
{
    /**
     * Hitung dan isi:
     *  - jumlah_penelitian
     *  - jumlah_p3m
     *  - jumlah_publikasi
     *
     * untuk setiap dosen pada tahun tertentu,
     * berdasarkan data di ResearchProject & Publication.
     *
     * Return: Collection<DosenPerformanceMetric>
     */
    public function aggregateForYear(int $tahun): Collection
    {
        // 1. Jumlah PENELITIAN per dosen (join lewat users)
        $penelitianCounts = ResearchProject::query()
            ->where('tahun_pelaksanaan', $tahun)
            ->where('validation_status', 'approved')   // kalau buat testing dan datanya belum approved, boleh di-comment dulu
            ->where('jenis', 'penelitian')
            ->join('dosens', 'research_projects.ketua_id', '=', 'dosens.user_id')
            ->selectRaw('dosens.id as dosen_id, COUNT(*) as total')
            ->groupBy('dosens.id')
            ->pluck('total', 'dosen_id'); // [dosen_id => total]

        // 2. Jumlah P3M (pengabdian) per dosen
        $p3mCounts = ResearchProject::query()
            ->where('tahun_pelaksanaan', $tahun)
            ->where('validation_status', 'approved')
            ->where('jenis', 'pengabdian')
            ->join('dosens', 'research_projects.ketua_id', '=', 'dosens.user_id')
            ->selectRaw('dosens.id as dosen_id, COUNT(*) as total')
            ->groupBy('dosens.id')
            ->pluck('total', 'dosen_id');

        // 3. Jumlah PUBLIKASI per dosen (owner_id -> users.id -> dosens.user_id)
        $publikasiCounts = Publication::query()
            ->where('tahun', $tahun)
            ->where('validation_status', 'approved')
            ->join('dosens', 'publications.owner_id', '=', 'dosens.user_id')
            ->selectRaw('dosens.id as dosen_id, COUNT(*) as total')
            ->groupBy('dosens.id')
            ->pluck('total', 'dosen_id');

        // 4. Loop semua dosen dan update metrics
        $dosens = Dosen::orderBy('id')->get();
        $result = collect();

        foreach ($dosens as $dosen) {
            $dosenId = (int) $dosen->id;

            /** @var DosenPerformanceMetric $metric */
            $metric = DosenPerformanceMetric::firstOrNew([
                'user_id' => $dosenId,   // FK ke dosens.id
                'tahun'   => $tahun,
            ]);

            $metric->jumlah_penelitian = (int) ($penelitianCounts[$dosenId] ?? 0);
            $metric->jumlah_p3m        = (int) ($p3mCounts[$dosenId] ?? 0);
            $metric->jumlah_publikasi  = (int) ($publikasiCounts[$dosenId] ?? 0);

            $metric->save();
            $result->push($metric);
        }

        return $result;
    }

    /**
     * Tarik data SINTA (Skor SINTA, SINTA 3Yr, Jumlah Hibah, Publikasi GS 1 tahun)
     * dan isi ke dosen_performance_metrics untuk tahun tertentu.
     */
    public function aggregateFromSintaForYear(int $tahun, SintaCrawler $crawler): Collection
    {
        $dosens = Dosen::query()
            ->whereNotNull('sinta_id')
            ->orderBy('id')
            ->get();

        $result = collect();

        foreach ($dosens as $dosen) {
            $sintaId = trim($dosen->sinta_id ?? '');
            $dosenId = (int) $dosen->id;

            if ($sintaId === '') {
                continue;
            }

            try {
                $scores      = $crawler->fetchScores($sintaId);
                $jumlahHibah = $crawler->countHibahFromMatricsOverall($sintaId);
                $pubGs1Th    = $crawler->countGoogleScholarPublicationsByYear($sintaId, $tahun);
            } catch (\Throwable $e) {
                // TODO: log kalau perlu
                continue;
            }

            /** @var DosenPerformanceMetric $metric */
            $metric = DosenPerformanceMetric::firstOrNew([
                'user_id' => $dosenId,   // FK ke dosens.id
                'tahun'   => $tahun,
            ]);

            $metric->sinta_score           = $scores['sinta_score_overall'] ?? 0.0;
            $metric->sinta_score_3yr       = $scores['sinta_score_3yr'] ?? 0.0;
            $metric->jumlah_hibah          = $jumlahHibah;
            $metric->publikasi_scholar_1th = $pubGs1Th;

            $metric->save();
            $result->push($metric);

            usleep(300000);
        }

        return $result;
    }
}
