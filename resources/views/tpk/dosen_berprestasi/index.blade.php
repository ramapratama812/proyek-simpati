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

    {{-- ====== Styling kecil untuk podium ====== --}}
    <style>
        .podium-wrapper .podium-col { width: 140px; }
        .podium-base { height: 120px; }
        .podium-1 .podium-base { height: 150px; }
        .podium-3 .podium-base { height: 100px; }
        .podium-badge {
            font-size: 0.75rem;
            padding: 0.1rem 0.35rem;
        }
        @media (max-width: 576px) {
            .podium-wrapper { gap: 0.5rem !important; }
            .podium-wrapper .podium-col { width: 100px; }
            .podium-base { height: 90px; }
            .podium-1 .podium-base { height: 110px; }
            .podium-3 .podium-base { height: 80px; }
        }
    </style>

    @php
        // Siapkan data ranking untuk top 3 / top 5 dan posisi dosen yang login
        $items = $ranking ?? collect();
        if (is_array($items)) {
            $items = collect($items);
        }

        $totalDosen = $items->count();
        $top5       = $items->sortBy('peringkat')->take(5);
        $top3       = $top5->take(3);

        // Ambil dosen yang terkait dengan user yang sedang login
        $currentUser    = auth()->user();
        $currentDosen   = $currentUser
            ? \App\Models\Dosen::where('user_id', $currentUser->id)->first()
            : null;
        $currentDosenId = $currentDosen?->id;

        // Cari baris ranking untuk dosen ini
        $userRow = null;
        if ($currentDosenId) {
            $userRow = $items->first(function ($row) use ($currentDosenId) {
                // Jika ada kolom foreign key langsung
                if (isset($row->user_id) && (int) $row->user_id === (int) $currentDosenId) {
                    return true;
                }
                // Atau lewat relasi 'dosen'
                if (isset($row->dosen) && $row->dosen && (int) $row->dosen->id === (int) $currentDosenId) {
                    return true;
                }
                return false;
            });
        }

        $userRank       = null;
        $userTopPercent = null;

        if ($userRow && $totalDosen > 0) {
            // kalau sudah ada field peringkat, pakai itu
            if (isset($userRow->peringkat)) {
                $userRank = (int) $userRow->peringkat;
            } else {
                // fallback: posisi dalam koleksi (berdasarkan skor_akhir desc, match ke dosen)
                $sorted = $items->sortByDesc('skor_akhir')->values();
                $userRank = $sorted->search(function ($row) use ($currentDosenId) {
                    if (isset($row->user_id) && (int) $row->user_id === (int) $currentDosenId) {
                        return true;
                    }
                    if (isset($row->dosen) && $row->dosen && (int) $row->dosen->id === (int) $currentDosenId) {
                        return true;
                    }
                    return false;
                }) + 1;
            }

            if ($userRank > 0) {
                $userTopPercent = round(($userRank / $totalDosen) * 100, 2);
            }
        }
    @endphp

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

    {{-- ====== Podium & Top 5 (kalau ada ranking) ====== --}}
    @if($ranking->isNotEmpty())
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Top 3 Dosen Berprestasi Tahun {{ $tahun }}</h5>
                    @if($top5->count() > 3)
                        <span class="text-muted small">
                            Menampilkan 5 besar dari {{ $totalDosen }} dosen yang dinilai
                        </span>
                    @endif
                </div>

                <div class="d-flex justify-content-center align-items-end gap-3 podium-wrapper">
                    @php
                        // urutan: peringkat 2 (kiri), 1 (tengah), 3 (kanan)
                        $p1 = $top3->get(1); // rank 2
                        $p0 = $top3->get(0); // rank 1
                        $p2 = $top3->get(2); // rank 3
                    @endphp

                    {{-- Peringkat 2 --}}
                    @if($p1)
                        <div class="podium-col podium-2 text-center">
                            <div class="podium-card bg-white shadow-sm rounded-3 p-2 mb-2">
                                <div class="small text-muted">Peringkat</div>
                                <div class="display-6 fw-bold text-secondary">2</div>
                            </div>
                            <div class="podium-base bg-success-subtle rounded-top-3 d-flex flex-column justify-content-end p-2">
                                <div class="fw-semibold text-truncate">
                                    {{ optional($p1->dosen)->nama ?? optional($p1->user)->name ?? 'N/A' }}
                                </div>
                                <div class="small text-muted">
                                    Skor: {{ number_format($p1->skor_akhir, 6) }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Peringkat 1 --}}
                    @if($p0)
                        <div class="podium-col podium-1 text-center">
                            <div class="podium-card bg-white shadow-sm rounded-3 p-2 mb-2">
                                <div class="small text-muted">Peringkat</div>
                                <div class="display-5 fw-bold text-warning">1</div>
                            </div>
                            <div class="podium-base bg-success text-white rounded-top-3 d-flex flex-column justify-content-end p-2">
                                <div class="fw-semibold text-truncate">
                                    {{ optional($p0->dosen)->nama ?? optional($p0->user)->name ?? 'N/A' }}
                                </div>
                                <div class="small">
                                    Skor: {{ number_format($p0->skor_akhir, 6) }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Peringkat 3 --}}
                    @if($p2)
                        <div class="podium-col podium-3 text-center">
                            <div class="podium-card bg-white shadow-sm rounded-3 p-2 mb-2">
                                <div class="small text-muted">Peringkat</div>
                                <div class="display-6 fw-bold text-secondary">3</div>
                            </div>
                            <div class="podium-base bg-success-subtle rounded-top-3 d-flex flex-column justify-content-end p-2">
                                <div class="fw-semibold text-truncate">
                                    {{ optional($p2->dosen)->nama ?? optional($p2->user)->name ?? 'N/A' }}
                                </div>
                                <div class="small text-muted">
                                    Skor: {{ number_format($p2->skor_akhir, 6) }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- List Top 5 kecil di bawah podium --}}
                @if($top5->count() > 0)
                    <div class="mt-3">
                        <div class="small text-muted mb-1">5 Besar Dosen Berprestasi:</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($top5 as $row)
                                <span class="badge bg-light text-dark border podium-badge">
                                    #{{ $row->peringkat }} &mdash;
                                    {{ optional($row->dosen)->nama ?? optional($row->user)->name ?? 'N/A' }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
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
                                @php
                                    $isCurrent = false;
                                    if ($currentDosenId) {
                                        if (isset($row->user_id) && (int) $row->user_id === (int) $currentDosenId) {
                                            $isCurrent = true;
                                        } elseif (isset($row->dosen) && $row->dosen && (int) $row->dosen->id === (int) $currentDosenId) {
                                            $isCurrent = true;
                                        }
                                    }
                                @endphp
                                <tr class="{{ $isCurrent ? 'table-success' : '' }}">
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

        {{-- Info posisi dosen yang sedang login (ala Edlink) --}}
        @if($userRow && $userRank && $userTopPercent !== null)
            <div class="alert alert-success mt-3 mb-0">
                Kamu berada di
                <strong>{{ number_format($userTopPercent, 2) }}% peringkat teratas</strong>
                Program Studi Teknologi Informasi pada tahun {{ $tahun }}
                dengan skor akhir
                <strong>{{ number_format($userRow->skor_akhir, 6) }}</strong>.
            </div>
        @endif
    @endif
</div>
@endsection
