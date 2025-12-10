@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Perbandingan Kriteria (AHP)</h4>

    <p class="text-muted">
        Isi perbandingan berpasangan antar kriteria menggunakan skala 1–9.
        Nilai 1 = sama penting, 9 = sangat jauh lebih penting.
    </p>

    {{-- Form untuk menyimpan perbandingan --}}
    <form action="{{ route('ahp.criteria_comparisons.update') }}" method="POST" class="mb-4">
        @csrf

        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width: 25%;">Kriteria A</th>
                    <th style="width: 25%;">Kriteria B</th>
                    <th style="width: 20%;">Lebih penting</th>
                    <th style="width: 20%;">Skala (1–9)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pairs as $index => $pair)
                    <tr>
                        <td>{{ $pair['row']->nama }}</td>
                        <td>{{ $pair['col']->nama }}</td>
                        <td>
                            <input type="hidden" name="pairs[{{ $index }}][row_id]"
                                   value="{{ $pair['row']->id }}">
                            <input type="hidden" name="pairs[{{ $index }}][col_id]"
                                   value="{{ $pair['col']->id }}">

                            <select name="pairs[{{ $index }}][direction]" class="form-select form-select-sm">
                                <option value="row" {{ $pair['direction'] === 'row' ? 'selected' : '' }}>
                                    {{ $pair['row']->nama }}
                                </option>
                                <option value="col" {{ $pair['direction'] === 'col' ? 'selected' : '' }}>
                                    {{ $pair['col']->nama }}
                                </option>
                            </select>

                            @error("pairs.$index.direction")
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <select name="pairs[{{ $index }}][scale]" class="form-select form-select-sm">
                                @for($s = 1; $s <= 9; $s++)
                                    <option value="{{ $s }}" {{ (int)$pair['scale'] === $s ? 'selected' : '' }}>
                                        {{ $s }}
                                    </option>
                                @endfor
                            </select>

                            @error("pairs.$index.scale")
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @error('pairs')
            <div class="alert alert-danger mt-2">
                {{ $message }}
            </div>
        @enderror

        <button type="submit" class="btn btn-secondary">
            Simpan Perbandingan
        </button>
    </form>

    {{-- Tombol untuk menghitung bobot AHP --}}
    <form action="{{ route('ahp.criteria_comparisons.calculate') }}" method="POST" class="mb-4">
        @csrf
        <button type="submit" class="btn btn-primary">
            Hitung Bobot AHP
        </button>
    </form>

    {{-- Panel hasil perhitungan bobot + CR --}}
    <div class="card">
        <div class="card-header">
            Hasil Bobot Kriteria & Konsistensi
        </div>
        <div class="card-body">
            @php
                $ahpResult = session('ahp_result');
            @endphp

            @if($ahpResult)
                <p>
                    <strong>λ max (Lamda Maksimum):</strong> {{ number_format($ahpResult['lambda_max'], 4) }}<br>
                    <strong>CI (Indeks Konsistensi):</strong> {{ number_format($ahpResult['ci'], 4) }}<br>
                    <strong>CR (Rasio Konsistensi):</strong>
                    <span class="{{ $ahpResult['cr'] <= 0.1 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($ahpResult['cr'], 4) }}
                        @if($ahpResult['cr'] <= 0.1)
                            (konsisten)
                        @else
                            (tidak konsisten, sebaiknya periksa ulang perbandingan)
                        @endif
                    </span>
                </p>
            @else
                <p class="text-muted">
                    Belum ada hasil perhitungan bobot AHP pada sesi ini. Tekan tombol "Hitung Bobot AHP"
                    setelah menyimpan perbandingan kriteria.
                </p>
            @endif

            <h6>Bobot Kriteria Saat Ini</h6>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteria as $c)
                        <tr>
                            <td>{{ $c->kode }}</td>
                            <td>{{ $c->nama }}</td>
                            <td>
                                {{ $c->bobot !== null ? number_format($c->bobot, 6) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
