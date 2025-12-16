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
            transition: transform 0.2s;
        }

        .publication-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            background: #fff;
            margin-bottom: 1rem;
            border-left: 4px solid transparent;
        }

        .publication-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left-color: #0d6efd;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .import-card {
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 1rem;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fw-bold mb-0">Publikasi Ilmiah</h1>
                        <p class="text-white-50 mb-0 mt-2">Kumpulan publikasi, jurnal, dan prosiding.</p>
                    </div>
                    @if (strtolower(auth()->user()->role ?? '') !== 'mahasiswa')
                        <a href="{{ route('publications.create') }}" class="btn btn-light text-primary fw-bold shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Publikasi
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="container pb-5">

            @if (strtolower(auth()->user()->role ?? '') !== 'mahasiswa')
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card filter-card">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cloud-upload me-2"></i>Impor Cepat
                                </h6>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="import-card p-3 h-100">
                                            <label class="form-label fw-semibold small text-muted">Impor via DOI
                                                (Crossref)</label>
                                            <form class="row g-2" method="POST" action="{{ route('import.crossref') }}">
                                                @csrf
                                                <div class="col-8">
                                                    <input name="doi" class="form-control form-control-sm"
                                                        placeholder="Contoh: 10.1038/..." required>
                                                </div>
                                                <div class="col-4">
                                                    <button class="btn btn-primary btn-sm w-100"><i
                                                            class="bi bi-download me-1"></i> Impor</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="import-card p-3 h-100">
                                            <label class="form-label fw-semibold small text-muted">Impor via BibTeX</label>
                                            <form class="row g-2" method="POST" action="{{ route('import.bibtex') }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="col-8">
                                                    <input type="file" name="file"
                                                        class="form-control form-control-sm" accept=".bib,.txt" required>
                                                </div>
                                                <div class="col-4">
                                                    <button class="btn btn-secondary btn-sm w-100"><i
                                                            class="bi bi-file-earmark-code me-1"></i> Upload</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                {{-- Left Column: Chart & Filter --}}
                <div class="col-lg-4">
                    {{-- Chart Section --}}
                    <div class="card filter-card mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3 bg-primary-subtle text-primary">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold">Statistik</h5>
                            </div>
                            <div style="height: 200px;">
                                <canvas id="chartPublikasi"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Section --}}
                    <div class="card filter-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3 bg-primary-subtle text-primary">
                                    <i class="bi bi-funnel-fill"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold">Filter & Pencarian</h5>
                            </div>

                            <form method="GET">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Kata Kunci</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="bi bi-search text-muted"></i></span>
                                        <input name="q" value="{{ request('q') }}"
                                            class="form-control border-start-0 ps-0" placeholder="Judul, Jurnal...">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Tahun Publikasi</label>
                                    <select name="year" class="form-select">
                                        <option value="">Semua Tahun</option>
                                        @foreach ($years as $y)
                                            <option value="{{ $y }}" @selected(request('year') == $y)>
                                                {{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Urutkan</label>
                                    <select name="sort" class="form-select">
                                        <option value="latest" @selected(request('sort') == 'latest')>Terbaru Ditambahkan</option>
                                        <option value="year_desc"@selected(request('sort') == 'year_desc')>Tahun (Terbaru)</option>
                                        <option value="year_asc" @selected(request('sort') == 'year_asc')>Tahun (Terlama)</option>
                                        <option value="name" @selected(request('sort') == 'name')>Judul (A-Z)</option>
                                    </select>
                                </div>

                                <button class="btn btn-primary w-100 fw-bold">Terapkan Filter</button>
                                @if (request()->anyFilled(['q', 'year', 'sort']))
                                    <a href="{{ route('publications.index') }}"
                                        class="btn btn-link w-100 text-decoration-none mt-2 text-muted small">Reset
                                        Filter</a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Column: List --}}
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-secondary mb-0">Daftar Artikel</h5>
                        <span class="badge bg-light text-dark border">{{ $pubs->total() }} ditemukan</span>
                    </div>

                    <div class="list-container">
                        @forelse($pubs as $r)
                            <div class="card publication-card p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1 pe-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <span
                                                class="badge bg-primary-subtle text-primary me-2">{{ $r->tahun ?? 'N/A' }}</span>
                                            <small class="text-muted text-uppercase fw-bold"
                                                style="font-size: 0.7rem;">{{ $r->jenis ?? 'Artikel' }}</small>
                                        </div>
                                        <h5 class="fw-bold mb-2">
                                            <a href="{{ route('publications.show', $r) }}"
                                                class="text-decoration-none text-dark stretched-link">
                                                {{ $r->judul }}
                                            </a>
                                        </h5>
                                        <div class="text-muted small mb-2">
                                            <i class="bi bi-journal-bookmark me-1"></i>
                                            {{ $r->jurnal ?? 'Jurnal tidak diketahui' }}
                                        </div>
                                        @if (isset($r->penulis) && is_array($r->penulis))
                                            <div class="text-muted small">
                                                <i class="bi bi-people me-1"></i>
                                                {{ Str::limit(implode(', ', $r->penulis), 80) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end" style="z-index: 2; position: relative;">
                                        <a href="{{ route('publications.show', $r) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Detail <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-journal-x text-muted opacity-25" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-muted">Belum ada publikasi ditemukan</h5>
                                <p class="text-muted small">Coba ubah filter atau kata kunci pencarian Anda.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $pubs->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (() => {
                const rows = @json($chart);
                const labels = rows.map(r => r.y);
                const data = rows.map(r => r.c);

                const el = document.getElementById('chartPublikasi');
                if (!el) return;

                // Modern Chart Config
                Chart.defaults.font.family = "'Poppins', sans-serif";
                Chart.defaults.color = '#6c757d';

                new Chart(el, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah Publikasi',
                            data,
                            backgroundColor: 'rgba(13, 110, 253, 0.7)',
                            borderColor: '#0d6efd',
                            borderWidth: 0,
                            borderRadius: 4,
                            hoverBackgroundColor: '#0a58ca'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#001F4D',
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [2, 4],
                                    color: '#e9ecef'
                                },
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endsection
