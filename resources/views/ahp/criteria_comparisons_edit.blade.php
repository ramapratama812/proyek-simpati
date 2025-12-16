@extends('layouts.app')

@section('content')
    <style>
        .page-header-gradient {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        }

        .form-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .guide-card {
            background: #e7f1ff;
            border: 1px dashed #0d6efd;
            border-radius: 1rem;
        }

        .comparison-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .comparison-row:hover {
            background-color: #f8f9fa;
        }

        .result-card {
            background: #fff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .metric-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <h1 class="fw-bold mb-0">Perbandingan Kriteria (AHP)</h1>
                <p class="text-white-50 mb-0 mt-2">Tentukan prioritas antar kriteria untuk perhitungan bobot.</p>
            </div>
        </div>

        <div class="container pb-5">
            {{-- Guide Section --}}
            <div class="card guide-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="fs-1 text-primary"><i class="bi bi-lightbulb"></i></div>
                        <div>
                            <h5 class="fw-bold text-primary">Panduan Pengisian Skala Saaty (1-9)</h5>
                            <p class="mb-2 text-secondary">Isi perbandingan berpasangan antar kriteria. Pilih kriteria mana
                                yang lebih penting dan seberapa besar tingkat kepentingannya.</p>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <span class="badge bg-white text-dark border">1 = Sama Penting</span>
                                <span class="badge bg-white text-dark border">3 = Sedikit Lebih Penting</span>
                                <span class="badge bg-white text-dark border">5 = Lebih Penting</span>
                                <span class="badge bg-white text-dark border">7 = Sangat Lebih Penting</span>
                                <span class="badge bg-white text-dark border">9 = Mutlak Lebih Penting</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Left Column: Comparison Form --}}
                <div class="col-lg-8">
                    <div class="card form-card">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h5 class="fw-bold mb-0"><i class="bi bi-sliders me-2 text-primary"></i> Form Perbandingan</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('ahp.criteria_comparisons.update') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle comparison-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Kriteria A</th>
                                                <th style="width: 30%;">Kriteria B</th>
                                                <th style="width: 25%;">Lebih Penting</th>
                                                <th style="width: 15%;">Nilai (1-9)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pairs as $index => $pair)
                                                <tr class="comparison-row">
                                                    <td class="fw-medium text-primary">{{ $pair['row']->nama }}</td>
                                                    <td class="fw-medium text-secondary">{{ $pair['col']->nama }}</td>
                                                    <td>
                                                        <input type="hidden" name="pairs[{{ $index }}][row_id]"
                                                            value="{{ $pair['row']->id }}">
                                                        <input type="hidden" name="pairs[{{ $index }}][col_id]"
                                                            value="{{ $pair['col']->id }}">

                                                        <select name="pairs[{{ $index }}][direction]"
                                                            class="form-select form-select-sm border-primary-subtle">
                                                            <option value="row"
                                                                {{ $pair['direction'] === 'row' ? 'selected' : '' }}>
                                                                {{ $pair['row']->nama }} (Kiri)
                                                            </option>
                                                            <option value="col"
                                                                {{ $pair['direction'] === 'col' ? 'selected' : '' }}>
                                                                {{ $pair['col']->nama }} (Kanan)
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="pairs[{{ $index }}][scale]"
                                                            class="form-select form-select-sm text-center fw-bold">
                                                            @for ($s = 1; $s <= 9; $s++)
                                                                <option value="{{ $s }}"
                                                                    {{ (int) $pair['scale'] === $s ? 'selected' : '' }}>
                                                                    {{ $s }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                                        <i class="bi bi-save me-2"></i> Simpan Perbandingan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Results & Actions --}}
                <div class="col-lg-4">
                    {{-- Calculate Button --}}
                    <div class="card form-card mb-4 bg-primary text-white"
                        style="background: linear-gradient(45deg, #0d6efd, #0a58ca);">
                        <div class="card-body p-4 text-center">
                            <h5 class="fw-bold mb-3">Hitung Bobot</h5>
                            <p class="small text-white-50 mb-4">Klik tombol di bawah untuk memproses bobot AHP berdasarkan
                                perbandingan di samping.</p>
                            <form action="{{ route('ahp.criteria_comparisons.calculate') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-light text-primary fw-bold w-100 py-2 shadow-sm">
                                    <i class="bi bi-calculator me-2"></i> Hitung Sekarang
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Results --}}
                    <div class="card result-card">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h5 class="fw-bold mb-0">Hasil Analisis</h5>
                        </div>
                        <div class="card-body p-4">
                            @php $ahpResult = session('ahp_result'); @endphp

                            @if ($ahpResult)
                                <div class="text-center mb-4">
                                    <div class="metric-label mb-2">Rasio Konsistensi (CR)</div>
                                    <div
                                        class="metric-value {{ $ahpResult['cr'] <= 0.1 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($ahpResult['cr'], 4) }}
                                    </div>
                                    <div
                                        class="badge {{ $ahpResult['cr'] <= 0.1 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} mt-2 px-3 py-2 rounded-pill">
                                        {{ $ahpResult['cr'] <= 0.1 ? 'Konsisten' : 'Tidak Konsisten' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Lambda Max</span>
                                        <span class="fw-bold">{{ number_format($ahpResult['lambda_max'], 4) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Consistency Index (CI)</span>
                                        <span class="fw-bold">{{ number_format($ahpResult['ci'], 4) }}</span>
                                    </div>
                                </div>

                                <h6 class="fw-bold border-bottom pb-2 mb-3">Bobot Kriteria</h6>
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($criteria as $c)
                                        <div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="small fw-bold">{{ $c->nama }}</span>
                                                <span
                                                    class="small text-primary fw-bold">{{ $c->bobot !== null ? number_format($c->bobot * 100, 2) . '%' : '-' }}</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ ($c->bobot ?? 0) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-bar-chart-steps fs-1 opacity-25 mb-2"></i>
                                    <p class="small">Belum ada hasil perhitungan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
