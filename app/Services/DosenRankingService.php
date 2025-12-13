<?php

namespace App\Services;

use App\Models\AhpCriteria;
use App\Models\AhpCriteriaComparison;
use App\Models\DosenPerformanceMetric;
use Illuminate\Support\Collection;

class DosenRankingService
{
    /**
     * Hitung skor & ranking dosen + CR AHP.
     *
     * Return:
     * [
     *   'cr'      => float,
     *   'ranking' => Collection<DosenPerformanceMetric>
     * ]
     */
    public function hitungRanking(int $tahun): array
    {
        // --- Guard 1: cek CR dari matriks perbandingan ---
        $cr = $this->hitungCr();

        if ($cr > 0.1) {
            throw new \RuntimeException(
                'Rasio konsistensi AHP (CR) = ' . number_format($cr, 4) .
                ' > 0.10. Perbaiki perbandingan kriteria terlebih dahulu.'
            );
        }

        // --- mapping kriteria -> kolom metrics (seperti sebelumnya) ---
        $fieldMap = [
            'SINTA_SCORE'       => 'sinta_score',
            'SINTA_SCORE_3YR'   => 'sinta_score_3yr',
            'JUMLAH_HIBAH'      => 'jumlah_hibah',
            'SCHOLAR_1YR'       => 'publikasi_scholar_1th',
            'JUMLAH_PENELITIAN' => 'jumlah_penelitian',
            'JUMLAH_P3M'        => 'jumlah_p3m',
            'JUMLAH_PUBLIKASI'  => 'jumlah_publikasi',
        ];

        $criteria = AhpCriteria::whereIn('kode', array_keys($fieldMap))
            ->orderBy('id')
            ->get()
            ->keyBy('kode');

        if ($criteria->isEmpty()) {
            throw new \RuntimeException('Belum ada kriteria AHP yang terdaftar.');
        }

        if ($criteria->whereNull('bobot')->count() > 0) {
            throw new \RuntimeException('Masih ada kriteria yang bobotnya NULL. Hitung bobot AHP dulu.');
        }

        $metrics = DosenPerformanceMetric::with('dosen')
            ->where('tahun', $tahun)
            ->get();

        if ($metrics->isEmpty()) {
            return [
                'cr'      => $cr,
                'ranking' => collect(),
            ];
        }

        // --- normalisasi benefit criteria + skor akhir ---
        $maxValues = [];
        foreach ($fieldMap as $kode => $field) {
            $max = $metrics->max($field);
            $maxValues[$kode] = $max > 0 ? (float) $max : 1.0;
        }

        foreach ($metrics as $m) {
            $score = 0.0;

            foreach ($fieldMap as $kode => $field) {
                $criteriaRow = $criteria[$kode] ?? null;
                if (! $criteriaRow) {
                    continue;
                }

                $weight = (float) $criteriaRow->bobot;
                $x_ij   = (float) ($m->{$field} ?? 0);
                $max    = (float) $maxValues[$kode];

                $normalized = $max > 0 ? ($x_ij / $max) : 0.0;

                $score += $normalized * $weight;
            }

            $m->skor_akhir = $score;
        }

        $sorted = $metrics->sortByDesc('skor_akhir')->values();

        foreach ($sorted as $index => $m) {
            $m->peringkat = $index + 1;
            $m->save();
        }

        return [
            'cr'      => $cr,
            'ranking' => $sorted,
        ];
    }

    /**
     * Hitung CR AHP dari ahp_criteria_comparisons (tanpa mengubah DB).
     */
    protected function hitungCr(): float
    {
        $criteria = AhpCriteria::orderBy('id')->get();
        $n = $criteria->count();

        if ($n <= 1) {
            return 0.0;
        }

        $matrix = array_fill(0, $n, array_fill(0, $n, 1.0));

        $comparisons = AhpCriteriaComparison::all()->keyBy(function ($c) {
            return $c->row_criteria_id . '-' . $c->col_criteria_id;
        });

        $idIndex = [];
        foreach ($criteria as $index => $c) {
            $idIndex[$c->id] = $index;
        }

        foreach ($comparisons as $comp) {
            $i = $idIndex[$comp->row_criteria_id] ?? null;
            $j = $idIndex[$comp->col_criteria_id] ?? null;
            if ($i === null || $j === null || $i === $j) {
                continue;
            }

            $val = (float) $comp->value;
            if ($val <= 0) {
                $val = 1.0;
            }

            $matrix[$i][$j] = $val;
            $matrix[$j][$i] = 1.0 / $val;
        }

        // geometric mean & bobot
        $geometricMeans = [];
        for ($i = 0; $i < $n; $i++) {
            $product = 1.0;
            for ($j = 0; $j < $n; $j++) {
                $product *= $matrix[$i][$j];
            }
            $geometricMeans[$i] = pow($product, 1.0 / $n);
        }

        $sumGM = array_sum($geometricMeans);
        if ($sumGM == 0.0) {
            return 1.0; // anggap totally inconsistent
        }

        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $weights[$i] = $geometricMeans[$i] / $sumGM;
        }

        // λmax
        $lambdaMax = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $rowSum += $matrix[$i][$j] * $weights[$j];
            }
            $lambdaMax += $rowSum / $weights[$i];
        }
        $lambdaMax /= $n;

        $ci = ($lambdaMax - $n) / ($n - 1);

        $riTable = [
            1 => 0.00,
            2 => 0.00,
            3 => 0.58,
            4 => 0.90,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            8 => 1.41,
            9 => 1.45,
            10 => 1.49,
        ];

        $ri = $riTable[$n] ?? 1.49;

        if ($ri == 0.0) {
            return 0.0;
        }

        return $ci / $ri;
    }
}
