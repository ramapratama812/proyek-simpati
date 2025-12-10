<?php

namespace App\Http\Controllers;

use App\Services\AhpCriteriaService;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    public function hitungKriteria(AhpCriteriaService $service)
    {
        $result = $service->calculateAndSaveWeights();

        // Terserah mau balikin view atau JSON
        return view('ahp.kriteria_hasil', [
            'lambda_max' => $result['lambda_max'],
            'ci'         => $result['ci'],
            'cr'         => $result['cr'],
            'criteria'   => $result['criteria'],
        ]);
    }
}
