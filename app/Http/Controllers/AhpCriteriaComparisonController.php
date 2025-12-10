<?php

namespace App\Http\Controllers;

use App\Models\AhpCriteria;
use Illuminate\Http\Request;
use App\Services\AhpCriteriaService;
use App\Models\AhpCriteriaComparison;

class AhpCriteriaComparisonController extends Controller
{
    /**
     * Tampilkan form input perbandingan kriteria (pairwise).
     */
    public function edit()
    {
        $criteria = AhpCriteria::orderBy('id')->get();
        $n = $criteria->count();

        // ambil semua perbandingan yang sudah ada
        $existing = AhpCriteriaComparison::all()->keyBy(function ($c) {
            return $c->row_criteria_id . '-' . $c->col_criteria_id;
        });

        $pairs = [];

        // generate semua pasangan (i < j)
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $row = $criteria[$i];
                $col = $criteria[$j];

                $key = $row->id . '-' . $col->id;
                $comp = $existing->get($key);

                $direction = 'row'; // default: row lebih penting
                $scale = 1;

                if ($comp) {
                    $val = (float) $comp->value;

                    if ($val >= 1.0) {
                        $direction = 'row';
                        $scale = round($val, 4);
                    } else {
                        $direction = 'col';
                        $scale = $val != 0.0 ? round(1.0 / $val, 4) : 1;
                    }
                }

                $pairs[] = [
                    'row'       => $row,
                    'col'       => $col,
                    'direction' => $direction,
                    'scale'     => $scale,
                ];
            }
        }

        return view('ahp.criteria_comparisons_edit', [
            'pairs'    => $pairs,
            'criteria' => $criteria,
        ]);
    }

    /**
     * Simpan perbandingan kriteria dari form.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'pairs'                      => 'required|array',
            'pairs.*.row_id'             => 'required|integer|exists:ahp_criteria,id',
            'pairs.*.col_id'             => 'required|integer|exists:ahp_criteria,id|different:pairs.*.row_id',
            'pairs.*.direction'          => 'required|in:row,col',
            'pairs.*.scale'              => 'required|numeric|min:1|max:9',
        ]);

        foreach ($data['pairs'] as $pair) {
            $rowId = (int) $pair['row_id'];
            $colId = (int) $pair['col_id'];
            $direction = $pair['direction'];
            $scale = (float) $pair['scale'];

            // kalau sama penting (scale = 1), direction sebenarnya nggak relevan
            if ($scale <= 0) {
                $scale = 1;
            }

            // value yang disimpan di DB: nilai perbandingan row terhadap col
            // jika row lebih penting => value = scale
            // jika col lebih penting => value = 1/scale
            $value = $direction === 'row'
                ? $scale
                : (1.0 / $scale);

            AhpCriteriaComparison::updateOrCreate(
                [
                    'row_criteria_id' => $rowId,
                    'col_criteria_id' => $colId,
                ],
                [
                    'value' => $value,
                ]
            );
        }

        return redirect()
            ->route('ahp.criteria_comparisons.edit')
            ->with('status', 'Perbandingan kriteria berhasil disimpan.');
    }

    public function calculateWeights(AhpCriteriaService $service)
    {
        try {
            $result = $service->calculateAndSaveWeights();
        } catch (\Throwable $e) {
            return redirect()
                ->route('ahp.criteria_comparisons.edit')
                ->with('error', 'Gagal menghitung bobot AHP: ' . $e->getMessage());
        }

        $cr = $result['cr'] ?? null;

        $message = 'Bobot AHP berhasil dihitung dan disimpan.';
        if (!is_null($cr)) {
            $message .= ' CR = ' . number_format($cr, 4);
            if ($cr > 0.1) {
                $message .= ' (TIDAK konsisten, periksa ulang perbandingan kriteria).';
            } else {
                $message .= ' (konsisten).';
            }
        }

        return redirect()
            ->route('ahp.criteria_comparisons.edit')
            ->with('status', $message)
            ->with('ahp_result', [
                'lambda_max' => $result['lambda_max'],
                'ci'         => $result['ci'],
                'cr'         => $result['cr'],
            ]);
    }
}
