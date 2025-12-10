@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Ranking Dosen Berprestasi (Metode AHP)</h4>

    @if(!is_null($cr))
        <div class="alert {{ $cr <= 0.1 ? 'alert-success' : 'alert-danger' }}">
            Rasio konsistensi AHP (CR):
            <strong>{{ number_format($cr, 4) }}</strong>
            @if($cr <= 0.1)
                &mdash; perbandingan kriteria konsisten.
            @else
                &mdash; tidak konsisten (&gt; 0.10). Perbaiki perbandingan kriteria sebelum menggunakan ranking.
            @endif
        </div>
    @endif

    {{-- Filter tahun + tombol sync --}}
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <form action="{{ route('tpk.dosen_berprestasi.index') }}" method="GET" class="d-flex gap-2">
                <div class="flex-grow-1">
                    <label for="tahun" class="form-label mb-1">Tahun</label>
                    <input type="number"
                           name="tahun"
                           id="tahun"
                           class="form-control form-control-sm"
                           value="{{ $tahun }}"
                           min="2000" max="2100">
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn btn-sm btn-primary mt-1">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        @if($role === 'admin')
        <div class="col-md-9 text-md-end mt-3 mt-md-0">
            {{-- Sync data internal --}}
            <form action="{{ route('tpk.dosen_berprestasi.sync_internal') }}"
                  method="POST"
                  class="d-inline">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    Sync Data Internal
                </button>
            </form>

            {{-- Sync data SINTA --}}
            <form action="{{ route('tpk.dosen_berprestasi.sync_sinta') }}"
                  method="POST"
                  class="d-inline ms-1">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    Sync Data SINTA
                </button>
            </form>
        </div>
        @endif
    </div>

    @if($error)
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @endif

    @if($ranking->isEmpty() && ! $error)
        <div class="alert alert-info">
            Belum ada data kinerja dosen untuk tahun {{ $tahun }}.
            Jalankan "Sync Data Internal" dan/atau "Sync Data SINTA" (bagi admin) terlebih dahulu.
        </div>
    @endif

    @if($ranking->isNotEmpty())
        <div class="card">
            <div class="card-header">
                Hasil Ranking Tahun {{ $tahun }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dosen</th>
                                <th>SINTA Score</th>
                                <th>SINTA Score 3yr</th>
                                <th>Jml Hibah</th>
                                <th>Scholar 1th</th>
                                <th>Jml Penelitian</th>
                                <th>Jml P3M</th>
                                <th>Jml Publikasi</th>
                                <th>Skor Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ranking as $row)
                                <tr>
                                    <td>{{ $row->peringkat }}</td>
                                    <td>
                                        {{ optional($row->dosen)->nama ?? optional($row->user)->name ?? 'N/A' }}
                                    </td>
                                    <td>{{ $row->sinta_score }}</td>
                                    <td>{{ $row->sinta_score_3yr }}</td>
                                    <td>{{ $row->jumlah_hibah }}</td>
                                    <td>{{ $row->publikasi_scholar_1th }}</td>
                                    <td>{{ $row->jumlah_penelitian }}</td>
                                    <td>{{ $row->jumlah_p3m }}</td>
                                    <td>{{ $row->jumlah_publikasi }}</td>
                                    <td>{{ number_format($row->skor_akhir, 6) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection


{{--

Ini buat backupan cek role (kalau perlu)

@php
    $currentRole = strtolower(auth()->user()->role ?? '');
@endphp

@if($currentRole === 'admin')
    ...
@endif

@if($currentRole === 'dosen')
    ...
@endif

@if($currentRole === 'mahasiswa')
    ...
@endif

--}}
