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

        .filter-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .podium-section {
            background-color: #ffffff;
            /* Sunburst/Ray Background Effect */
            background-image: repeating-conic-gradient(from 0deg at 50% 70%,
                    #ffffff 0deg 10deg,
                    #f1f5f9 10deg 20deg);
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .podium-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .podium-col {
            width: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .podium-base {
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        /* Rank 1 (Gold) */
        .podium-1 .podium-base {
            height: 200px;
            background: linear-gradient(to bottom, #FFD700, #FDB931);
            box-shadow: 0 10px 20px rgba(253, 185, 49, 0.3);
            z-index: 3;
        }

        /* Rank 2 (Silver) */
        .podium-2 .podium-base {
            height: 160px;
            background: linear-gradient(to bottom, #C0C0C0, #A9A9A9);
            box-shadow: 0 10px 20px rgba(169, 169, 169, 0.3);
            z-index: 2;
        }

        /* Rank 3 (Bronze) */
        .podium-3 .podium-base {
            height: 130px;
            background: linear-gradient(to bottom, #CD7F32, #8B4513);
            box-shadow: 0 10px 20px rgba(139, 69, 19, 0.3);
            z-index: 2;
        }

        /* Rank 4 */
        .podium-4 .podium-base {
            height: 100px;
            background: linear-gradient(to bottom, #6c757d, #495057);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
            z-index: 1;
        }

        /* Rank 5 */
        .podium-5 .podium-base {
            height: 80px;
            background: linear-gradient(to bottom, #adb5bd, #6c757d);
            box-shadow: 0 10px 20px rgba(173, 181, 189, 0.3);
            z-index: 1;
        }

        .podium-card {
            transform: translateY(10px);
            transition: transform 0.3s ease;
            width: 100%;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .podium-col:hover .podium-card {
            transform: translateY(0);
        }

        .avatar-circle {
            width: 50px;
            height: 50px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 0 auto -25px;
            position: relative;
            z-index: 2;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 3px solid #fff;
        }

        .podium-1 .avatar-circle {
            width: 70px;
            height: 70px;
            font-size: 2rem;
            margin-bottom: -35px;
        }

        .ranking-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .ranking-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #e9ecef;
        }

        .ranking-table tbody tr {
            transition: background-color 0.2s;
        }

        .ranking-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .current-user-row {
            background-color: #e7f1ff !important;
        }

        .achievement-card {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.2);
        }

        @media (max-width: 768px) {
            .podium-wrapper {
                flex-wrap: wrap;
            }

            .podium-col {
                width: 30%;
                flex-grow: 1;
            }

            .podium-1 {
                order: 1;
                width: 100%;
            }

            .podium-2 {
                order: 2;
            }

            .podium-3 {
                order: 3;
            }

            .podium-4 {
                order: 4;
            }

            .podium-5 {
                order: 5;
            }

            .podium-base {
                height: 80px !important;
            }

            .podium-1 .podium-base {
                height: 120px !important;
            }
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <h1 class="fw-bold mb-0">Ranking Dosen Berprestasi</h1>
                <p class="text-white-50 mb-0 mt-2">Peringkat kinerja dosen berdasarkan metode AHP dan data SINTA.</p>
            </div>
        </div>

        <div class="container pb-5">

            @if (!is_null($cr))
                @if ($cr > 0.1)
                    <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Perhatian:</strong> Rasio konsistensi AHP (CR) adalah
                            <strong>{{ number_format($cr, 4) }}</strong> (> 0.10).
                            <br>Hasil ranking mungkin tidak akurat. Silakan perbaiki perbandingan kriteria terlebih dahulu.
                        </div>
                    </div>
                @endif
            @endif

            @php
                // Siapkan data ranking
                $items = $ranking ?? collect();
                if (is_array($items)) {
                    $items = collect($items);
                }

                $totalDosen = $items->count();
                $top5 = $items->sortBy('peringkat')->take(5);

                // Ambil dosen yang terkait dengan user yang sedang login
                $currentUser = auth()->user();
                $currentDosen = $currentUser ? \App\Models\Dosen::where('user_id', $currentUser->id)->first() : null;
                $currentDosenId = $currentDosen?->id;

                // Cari baris ranking untuk dosen ini
                $userRow = null;
                if ($currentDosenId) {
                    $userRow = $items->first(function ($row) use ($currentDosenId) {
                        if (isset($row->user_id) && (int) $row->user_id === (int) $currentDosenId) {
                            return true;
                        }
                        if (isset($row->dosen) && $row->dosen && (int) $row->dosen->id === (int) $currentDosenId) {
                            return true;
                        }
                        return false;
                    });
                }

                $userRank = null;
                $userTopPercent = null;

                if ($userRow && $totalDosen > 0) {
                    if (isset($userRow->peringkat)) {
                        $userRank = (int) $userRow->peringkat;
                    } else {
                        $sorted = $items->sortByDesc('skor_akhir')->values();
                        $userRank =
                            $sorted->search(function ($row) use ($currentDosenId) {
                                if (isset($row->user_id) && (int) $row->user_id === (int) $currentDosenId) {
                                    return true;
                                }
                                if (
                                    isset($row->dosen) &&
                                    $row->dosen &&
                                    (int) $row->dosen->id === (int) $currentDosenId
                                ) {
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

            {{-- Filter & Actions --}}
            <div class="card filter-card">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <form action="{{ route('tpk.dosen_berprestasi.index') }}" method="GET">
                                <label for="tahun" class="form-label fw-bold small text-muted">Tahun Penilaian</label>
                                <div class="input-group">
                                    <input type="number" name="tahun" id="tahun" class="form-control"
                                        value="{{ $tahun }}" min="2000" max="2100">
                                    <button type="submit" class="btn btn-primary fw-bold px-4">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-8 text-md-end">
                            <div class="d-flex justify-content-md-end gap-2">
                                <form action="{{ route('tpk.dosen_berprestasi.sync_internal') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <button type="submit" class="btn btn-light text-secondary border fw-bold">
                                        <i class="bi bi-arrow-repeat me-2"></i> Sync Internal
                                    </button>
                                </form>

                                <form action="{{ route('tpk.dosen_berprestasi.sync_sinta') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <button type="submit" class="btn btn-outline-primary fw-bold">
                                        <i class="bi bi-cloud-download me-2"></i> Sync SINTA
                                    </button>
                                </form>

                                <a href="{{ route('tpk.dosen_berprestasi.export', ['tahun' => $tahun]) }}"
                                    class="btn btn-success fw-bold text-white">
                                    <i class="bi bi-file-earmark-excel me-2"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($error)
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-circle me-2"></i> {{ $error }}
                </div>
            @endif

            @if ($ranking->isEmpty() && !$error)
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-trophy text-muted opacity-25" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted">Belum ada data ranking</h5>
                    <p class="text-muted small">Silakan lakukan sinkronisasi data terlebih dahulu.</p>
                </div>
            @endif

            {{-- Podium Section (Top 5) --}}
            @if ($ranking->isNotEmpty())
                <div class="podium-section text-center">
                    <h4 class="fw-bold mb-2">Top 5 Dosen Berprestasi</h4>
                    <p class="text-muted mb-5">Apresiasi tertinggi untuk dosen dengan kinerja terbaik tahun
                        {{ $tahun }}</p>

                    <div class="podium-wrapper">
                        @php
                            // Get Top 5 items
                            $p0 = $top5->where('peringkat', 1)->first();
                            $p1 = $top5->where('peringkat', 2)->first();
                            $p2 = $top5->where('peringkat', 3)->first();
                            $p3 = $top5->where('peringkat', 4)->first();
                            $p4 = $top5->where('peringkat', 5)->first();

                            // Helper to get name
                            $getName = function ($row) {
                                if (!$row) {
                                    return 'N/A';
                                }
                                return optional($row->dosen)->nama ?? (optional($row->user)->name ?? 'N/A');
                            };
                        @endphp

                        {{-- Rank 4 --}}
                        @if ($p3)
                            <div class="podium-col podium-4">
                                <div class="podium-card">
                                    <div class="avatar-circle text-secondary border-secondary">4</div>
                                    <div class="fw-bold mt-4 text-truncate px-2 small" title="{{ $getName($p3) }}">
                                        {{ Str::limit($getName($p3), 12) }}
                                    </div>
                                    <div class="badge bg-light text-secondary border mt-1">
                                        {{ number_format($p3->skor_akhir, 4) }}</div>
                                </div>
                                <div class="podium-base"></div>
                            </div>
                        @endif

                        {{-- Rank 2 --}}
                        @if ($p1)
                            <div class="podium-col podium-2">
                                <div class="podium-card">
                                    <div class="avatar-circle text-secondary border-secondary">2</div>
                                    <div class="fw-bold mt-4 text-truncate px-2" title="{{ $getName($p1) }}">
                                        {{ Str::limit($getName($p1), 15) }}
                                    </div>
                                    <div class="badge bg-light text-secondary border mt-1">
                                        {{ number_format($p1->skor_akhir, 4) }}</div>
                                </div>
                                <div class="podium-base">
                                    <div class="h-100 d-flex align-items-end justify-content-center pb-3">
                                        <i class="bi bi-trophy-fill text-white fs-2 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Rank 1 --}}
                        @if ($p0)
                            <div class="podium-col podium-1">
                                <div class="podium-card">
                                    <div class="avatar-circle text-warning border-warning">1</div>
                                    <div class="fw-bold mt-5 text-truncate px-2 fs-5" title="{{ $getName($p0) }}">
                                        {{ Str::limit($getName($p0), 15) }}
                                    </div>
                                    <div class="badge bg-warning text-dark mt-1 px-3">
                                        {{ number_format($p0->skor_akhir, 4) }}</div>
                                </div>
                                <div class="podium-base">
                                    <div class="h-100 d-flex align-items-end justify-content-center pb-3">
                                        <i class="bi bi-trophy-fill text-white fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Rank 3 --}}
                        @if ($p2)
                            <div class="podium-col podium-3">
                                <div class="podium-card">
                                    <div class="avatar-circle text-danger border-danger"
                                        style="color: #8B4513 !important; border-color: #8B4513 !important;">3</div>
                                    <div class="fw-bold mt-4 text-truncate px-2" title="{{ $getName($p2) }}">
                                        {{ Str::limit($getName($p2), 15) }}
                                    </div>
                                    <div class="badge bg-light text-secondary border mt-1">
                                        {{ number_format($p2->skor_akhir, 4) }}</div>
                                </div>
                                <div class="podium-base">
                                    <div class="h-100 d-flex align-items-end justify-content-center pb-3">
                                        <i class="bi bi-trophy-fill text-white fs-2 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Rank 5 --}}
                        @if ($p4)
                            <div class="podium-col podium-5">
                                <div class="podium-card">
                                    <div class="avatar-circle text-secondary border-secondary">5</div>
                                    <div class="fw-bold mt-4 text-truncate px-2 small" title="{{ $getName($p4) }}">
                                        {{ Str::limit($getName($p4), 12) }}
                                    </div>
                                    <div class="badge bg-light text-secondary border mt-1">
                                        {{ number_format($p4->skor_akhir, 4) }}</div>
                                </div>
                                <div class="podium-base"></div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- User Achievement --}}
                @if ($userRow && $userRank && $userTopPercent !== null)
                    <div class="achievement-card mb-4 d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="bi bi-award-fill fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Selamat! Anda berada di Top {{ number_format($userTopPercent, 0) }}%
                            </h5>
                            <p class="mb-0 opacity-75">
                                Anda menempati peringkat <strong>#{{ $userRank }}</strong> dari {{ $totalDosen }}
                                dosen dengan skor akhir <strong>{{ number_format($userRow->skor_akhir, 6) }}</strong>.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Ranking Table --}}
                <div class="card ranking-card">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-list-ol me-2 text-primary"></i> Detail Peringkat Lengkap
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table ranking-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Dosen</th>
                                        <th class="text-center">SINTA Score</th>
                                        <th class="text-center">SINTA 3Yr</th>
                                        <th class="text-center">Hibah</th>
                                        <th class="text-center">Scholar</th>
                                        <th class="text-center">Penelitian</th>
                                        <th class="text-center">P3M</th>
                                        <th class="text-center">Publikasi</th>
                                        <th class="pe-4 text-end">Skor Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ranking as $row)
                                        @php
                                            $isCurrent = false;
                                            if ($currentDosenId) {
                                                if (
                                                    isset($row->user_id) &&
                                                    (int) $row->user_id === (int) $currentDosenId
                                                ) {
                                                    $isCurrent = true;
                                                } elseif (
                                                    isset($row->dosen) &&
                                                    $row->dosen &&
                                                    (int) $row->dosen->id === (int) $currentDosenId
                                                ) {
                                                    $isCurrent = true;
                                                }
                                            }
                                        @endphp
                                        <tr class="{{ $isCurrent ? 'current-user-row' : '' }}">
                                            <td
                                                class="ps-4 fw-bold {{ $row->peringkat <= 3 ? 'text-primary' : 'text-muted' }}">
                                                {{ $row->peringkat }}</td>
                                            <td>
                                                <div class="fw-bold">
                                                    {{ optional($row->dosen)->nama ?? (optional($row->user)->name ?? 'N/A') }}
                                                </div>
                                                @if ($isCurrent)
                                                    <span class="badge bg-primary py-0"
                                                        style="font-size: 0.6rem;">ANDA</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $row->sinta_score }}</td>
                                            <td class="text-center">{{ $row->sinta_score_3yr }}</td>
                                            <td class="text-center">{{ $row->jumlah_hibah }}</td>
                                            <td class="text-center">{{ $row->publikasi_scholar_1th }}</td>
                                            <td class="text-center">{{ $row->jumlah_penelitian }}</td>
                                            <td class="text-center">{{ $row->jumlah_p3m }}</td>
                                            <td class="text-center">{{ $row->jumlah_publikasi }}</td>
                                            <td class="pe-4 text-end fw-bold text-primary">
                                                {{ number_format($row->skor_akhir, 6) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
