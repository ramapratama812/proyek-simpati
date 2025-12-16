    @extends('layouts.app')

    @section('content')
        <style>
            /* Custom Styles for Projects Index */
            .page-header-gradient {
                background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
                color: white;
                padding: 2rem 0;
                margin-bottom: 2rem;
                border-radius: 0 0 1rem 1rem;
                box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
            }

            .filter-card {
                background: white;
                border: none;
                border-radius: 1rem;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s;
            }

            .project-card {
                background: white;
                border: none;
                border-radius: 1rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                height: 100%;
                border-left: 5px solid transparent;
            }

            .project-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(13, 110, 253, 0.15);
            }

            .project-card.type-penelitian {
                border-left-color: #0d6efd;
                /* Blue */
            }

            .project-card.type-pengabdian {
                border-left-color: #198754;
                /* Green */
            }

            .badge-soft-primary {
                background-color: rgba(13, 110, 253, 0.1);
                color: #0d6efd;
            }

            .badge-soft-success {
                background-color: rgba(25, 135, 84, 0.1);
                color: #198754;
            }

            .icon-box {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background-color: #f8f9fa;
                color: #0d6efd;
                font-size: 1.2rem;
            }
        </style>

        <div class="container-fluid px-0">
            {{-- Header Section --}}
            <div class="page-header-gradient px-4">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold mb-1">Daftar Kegiatan</h2>
                            <p class="mb-0 opacity-75">Telusuri penelitian dan pengabdian masyarakat terbaru</p>
                        </div>
                        @if (strtolower(auth()->user()->role ?? '') !== 'mahasiswa')
                            <a class="btn btn-light text-primary fw-semibold shadow-sm" href="{{ route('projects.create') }}">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Kegiatan
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="container">
                {{-- Chart Section --}}
                <div class="card filter-card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3 bg-primary-subtle text-primary">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold">Statistik Kegiatan</h5>
                            </div>

                            {{-- Chart Filter Buttons --}}
                            <div class="btn-group" role="group">
                                <a href="{{ request()->fullUrlWithQuery(['chart_filter' => 'all']) }}"
                                    class="btn btn-sm {{ ($chartFilter ?? 'all') == 'all' ? 'btn-primary' : 'btn-outline-primary' }}"
                                    data-bs-toggle="tooltip" title="Semua Kegiatan">
                                    <i class="bi bi-grid-fill"></i>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['chart_filter' => 'penelitian']) }}"
                                    class="btn btn-sm {{ ($chartFilter ?? 'all') == 'penelitian' ? 'btn-primary' : 'btn-outline-primary' }}"
                                    data-bs-toggle="tooltip" title="Penelitian">
                                    <i class="bi bi-journal-bookmark"></i>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['chart_filter' => 'pengabdian']) }}"
                                    class="btn btn-sm {{ ($chartFilter ?? 'all') == 'pengabdian' ? 'btn-primary' : 'btn-outline-primary' }}"
                                    data-bs-toggle="tooltip" title="Pengabdian">
                                    <i class="bi bi-people"></i>
                                </a>
                            </div>
                        </div>
                        <div style="height: 250px;">
                            <canvas id="chartKegiatan"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Filter Section --}}
                <div class="card filter-card mb-4">
                    <div class="card-body p-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-search text-muted"></i></span>
                                    <input name="q" value="{{ old('q', request('q')) }}"
                                        class="form-control border-start-0 bg-light"
                                        placeholder="Judul / Skema kegiatan...">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-bold text-uppercase">Tahun</label>
                                <select name="year" class="form-select bg-light">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-bold text-uppercase">Jenis</label>
                                <select name="type" class="form-select bg-light">
                                    <option value="">Semua Jenis</option>
                                    <option value="Penelitian" @selected(request('type') == 'Penelitian')>Penelitian</option>
                                    <option value="Pengabdian" @selected(request('type') == 'Pengabdian')>Pengabdian</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Urutkan</label>
                                <select name="sort" class="form-select bg-light">
                                    <option value="latest" @selected(request('sort') == 'latest')>Terakhir diposting</option>
                                    <option value="year_desc" @selected(request('sort') == 'year_desc')>Tahun (Terbaru)</option>
                                    <option value="year_asc" @selected(request('sort') == 'year_asc')>Tahun (Terlama)</option>
                                    <option value="name" @selected(request('sort') == 'name')>Nama (A-Z)</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-bold text-uppercase">Tampilan</label>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="btnGrid"
                                        data-bs-toggle="tooltip" title="Tampilan Grid">
                                        <i class="bi bi-grid-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="btnList"
                                        data-bs-toggle="tooltip" title="Tampilan Daftar">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button class="btn btn-primary w-100 fw-semibold"><i class="bi bi-funnel-fill"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Project List (Grid View) --}}
                <div class="row g-4" id="gridView">
                    @forelse($projects as $r)
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route('projects.show', $r) }}" class="text-decoration-none text-dark">
                                <div
                                    class="card project-card h-100 {{ strtolower($r->jenis) == 'penelitian' ? 'type-penelitian' : 'type-pengabdian' }}">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <span
                                                class="badge {{ strtolower($r->jenis) == 'penelitian' ? 'badge-soft-primary' : 'badge-soft-success' }} rounded-pill px-3 py-2">
                                                <i
                                                    class="bi {{ strtolower($r->jenis) == 'penelitian' ? 'bi-journal-bookmark' : 'bi-people' }} me-1"></i>
                                                {{ ucfirst($r->jenis) }}
                                            </span>
                                            <span class="text-muted small fw-semibold bg-light px-2 py-1 rounded">
                                                {{ $r->mulai?->format('Y') ?? '—' }}
                                            </span>
                                        </div>

                                        <h5 class="card-title fw-bold mb-3 text-primary-emphasis"
                                            style="line-height: 1.4; min-height: 3.5em; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $r->judul }}
                                        </h5>

                                        <div class="mt-auto">
                                            <div class="d-flex align-items-center mb-2 text-muted small">
                                                <i class="bi bi-person-circle me-2 text-primary opacity-50"></i>
                                                <span class="text-truncate">{{ $r->ketua->name }}</span>
                                            </div>
                                            @if ($r->skema)
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="bi bi-diagram-2 me-2 text-primary opacity-50"></i>
                                                    <span class="text-truncate">{{ $r->skema }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 mb-3 d-block opacity-25"></i>
                                <p class="mb-0">Belum ada data kegiatan yang ditemukan.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Project List (List View) --}}
                <div class="d-none" id="listView">
                    <div class="list-group">
                        @forelse($projects as $r)
                            <a href="{{ route('projects.show', $r) }}"
                                class="list-group-item list-group-item-action p-4 border-0 shadow-sm mb-3 rounded-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="icon-box me-3 {{ strtolower($r->jenis) == 'penelitian' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }}">
                                            <i
                                                class="bi {{ strtolower($r->jenis) == 'penelitian' ? 'bi-journal-bookmark' : 'bi-people' }}"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold text-primary-emphasis">{{ $r->judul }}</h5>
                                            <div class="text-muted small">
                                                <i class="bi bi-person-circle me-1"></i> {{ $r->ketua->name }}
                                                <span class="mx-2">•</span>
                                                <span
                                                    class="badge {{ strtolower($r->jenis) == 'penelitian' ? 'badge-soft-primary' : 'badge-soft-success' }} rounded-pill">
                                                    {{ ucfirst($r->jenis) }}
                                                </span>
                                                @if ($r->skema)
                                                    <span class="mx-2">•</span>
                                                    {{ $r->skema }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge bg-light text-dark border">{{ $r->mulai?->format('Y') ?? '—' }}</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 mb-3 d-block opacity-25"></i>
                                <p class="mb-0">Belum ada data kegiatan yang ditemukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $projects->withQueryString()->links() }}
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize Tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    })

                    const btnGrid = document.getElementById('btnGrid');
                    const btnList = document.getElementById('btnList');
                    const gridView = document.getElementById('gridView');
                    const listView = document.getElementById('listView');

                    // Load preference
                    const currentView = localStorage.getItem('projectsView') || 'grid';
                    if (currentView === 'list') {
                        showList();
                    } else {
                        showGrid();
                    }

                    btnGrid.addEventListener('click', function() {
                        showGrid();
                        localStorage.setItem('projectsView', 'grid');
                    });

                    btnList.addEventListener('click', function() {
                        showList();
                        localStorage.setItem('projectsView', 'list');
                    });

                    function showGrid() {
                        gridView.classList.remove('d-none');
                        listView.classList.add('d-none');
                        btnGrid.classList.add('active');
                        btnList.classList.remove('active');
                    }

                    function showList() {
                        gridView.classList.add('d-none');
                        listView.classList.remove('d-none');
                        btnGrid.classList.remove('active');
                        btnList.classList.add('active');
                    }
                });

                (() => {
                    const rows = @json($chart);
                    const labels = rows.map(r => r.y);
                    const data = rows.map(r => r.c);

                    const el = document.getElementById('chartKegiatan');
                    if (!el) return;

                    // Modern Chart Config
                    Chart.defaults.font.family = "'Poppins', sans-serif";
                    Chart.defaults.color = '#6c757d';

                    new Chart(el, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Jumlah Kegiatan',
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
                                        borderDash: [5, 5],
                                        color: '#f0f0f0'
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
