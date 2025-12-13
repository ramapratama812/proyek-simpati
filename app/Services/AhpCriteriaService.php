<?php

namespace App\Services;

use App\Models\AhpCriteria;
use App\Models\AhpCriteriaComparison;

class AhpCriteriaService
{
    /**
     * Hitung bobot AHP untuk semua kriteria berdasarkan
     * tabel ahp_criteria_comparisons, simpan ke DB, dan
     * kembalikan hasilnya + CR.
     */
    public function calculateAndSaveWeights(): array
    {
        $criteria = AhpCriteria::orderBy('id')->get();
        $n = $criteria->count();

        if ($n === 0) {
            throw new \RuntimeException('Tidak ada kriteria yang terdaftar.');
        }

        // 1. Bangun matriks NxN, default 1
        $matrix = array_fill(0, $n, array_fill(0, $n, 1.0));

        $comparisons = AhpCriteriaComparison::all()->keyBy(function ($c) {
            return $c->row_criteria_id . '-' . $c->col_criteria_id;
        });

        // map id kriteria -> index baris/kolom
        $idIndex = [];
        foreach ($criteria as $index => $c) {
            $idIndex[$c->id] = $index;
        }

        // isi matriks bagian atas (row < col) dari DB
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
            $matrix[$j][$i] = 1.0 * (1.0 / $val);
        }

        // 2. Hitung geometric mean tiap baris
        $geometricMeans = [];
        for ($i = 0; $i < $n; $i++) {
            $product = 1.0;
            for ($j = 0; $j < $n; $j++) {
                $product *= $matrix[$i][$j];
            }
            $geometricMeans[$i] = pow($product, 1.0 / $n);
        }

        // 3. Normalisasi -> bobot
        $sumGM = array_sum($geometricMeans);
        if ($sumGM == 0.0) {
            throw new \RuntimeException('Geometric mean semua baris adalah 0, cek input perbandingan.');
        }

        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $weights[$i] = $geometricMeans[$i] / $sumGM;
        }

        // 4. Hitung λmax, CI, CR
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

        // RI standar AHP
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
        $cr = $ri == 0.0 ? 0.0 : $ci / $ri;

        // 5. Simpan bobot ke tabel ahp_criteria
        foreach ($criteria as $index => $c) {
            $c->bobot = $weights[$index];
            $c->save();
        }

        // kembalikan info buat ditampilkan di UI
        return [
            'lambda_max' => $lambdaMax,
            'ci'         => $ci,
            'cr'         => $cr,
            'criteria'   => $criteria->map(function ($c, $idx) use ($weights) {
                return [
                    'id'    => $c->id,
                    'kode'  => $c->kode,
                    'nama'  => $c->nama,
                    'bobot' => $weights[$idx],
                ];
            })->values()->all(),
        ];
    }
}
