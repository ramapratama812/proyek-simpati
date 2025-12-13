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

        .stat-card {
            border: none;
            border-radius: 1rem;
            transition: transform 0.2s;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .list-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .notification-alert {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .list-group-item {
            border-left: none;
            border-right: none;
            padding: 1rem 1.25rem;
            transition: background-color 0.2s;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .scrollable-list {
            max-height: 300px;
            /* Approx 3-4 rows */
            overflow-y: auto;
        }

        /* Custom Scrollbar for cleaner look */
        .scrollable-list::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .scrollable-list::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .scrollable-list::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <h1 class="fw-bold mb-0">Selamat Datang, {{ auth()->user()->name }}!</h1>
                <p class="text-white-50 mb-0 mt-2">
                    Anda masuk sebagai <span
                        class="badge bg-white text-primary fw-bold text-uppercase">{{ auth()->user()->role }}</span>
                </p>
            </div>
        </div>

        <div class="container pb-5">
            @if ($needsProfile)
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Profil Belum Lengkap!</strong>
                        <br>
                        Silakan lengkapi data profil Anda di menu <a href="{{ route('profile.edit') }}"
                            class="alert-link fw-bold">Edit Profil</a> agar semua fitur SIMPATI dapat digunakan secara
                        optimal.
                    </div>
                </div>
            @endif

            {{-- Notifications Section --}}
            @if ($pendingValidation > 0 || $needRevision > 0)
                <div
                    class="alert alert-warning notification-alert d-flex justify-content-between align-items-center mb-4 bg-warning bg-opacity-10 text-dark">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-dark rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-bell-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Notifikasi Kegiatan</h6>
                            <div class="small">
                                @if ($pendingValidation > 0)
                                    <span class="me-3"><i class="bi bi-hourglass-split me-1"></i>
                                        <strong>{{ $pendingValidation }}</strong> menunggu validasi</span>
                                @endif
                                @if ($needRevision > 0)
                                    <span><i class="bi bi-pencil-square me-1"></i> <strong>{{ $needRevision }}</strong>
                                        perlu revisi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($pendingValidation > 0)
                            <a href="{{ route('projects.my', ['status' => 'pending']) }}"
                                class="btn btn-sm btn-warning fw-bold">Lihat Pending</a>
                        @endif
                        @if ($needRevision > 0)
                            <a href="{{ route('projects.my', ['status' => 'revision_requested']) }}"
                                class="btn btn-sm btn-outline-dark fw-bold">Lihat Revisi</a>
                        @endif
                    </div>
                </div>
            @endif

            @if ($pubPending > 0 || $pubNeedRevision > 0)
                <div
                    class="alert alert-info notification-alert d-flex justify-content-between align-items-center mb-4 bg-info bg-opacity-10 text-dark">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-journal-text fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Notifikasi Publikasi</h6>
                            <div class="small">
                                @if ($pubPending > 0)
                                    <span class="me-3"><i class="bi bi-hourglass-split me-1"></i>
                                        <strong>{{ $pubPending }}</strong> menunggu validasi</span>
                                @endif
                                @if ($pubNeedRevision > 0)
                                    <span><i class="bi bi-pencil-square me-1"></i> <strong>{{ $pubNeedRevision }}</strong>
                                        perlu revisi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($pubPending > 0)
                            <a href="{{ route('publications.my', ['status' => 'pending']) }}"
                                class="btn btn-sm btn-info text-white fw-bold">Lihat Pending</a>
                        @endif
                        @if ($pubNeedRevision > 0)
                            <a href="{{ route('publications.my', ['status' => 'revision_requested']) }}"
                                class="btn btn-sm btn-outline-dark fw-bold">Lihat Revisi</a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Stats Cards (Non-Student) --}}
            @if (strtolower(auth()->user()->role ?? '') !== 'mahasiswa')
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card stat-card h-100 bg-white">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                        <i class="bi bi-briefcase-fill fs-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Total Kegiatan</h6>
                                        <h2 class="mb-0 fw-bold">{{ $totalKegiatan }}</h2>
                                        <small class="text-muted">Penelitian & Pengabdian</small>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="small fw-bold text-muted"><i class="bi bi-graph-up me-1"></i> Tren
                                            Pertahun</span>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-primary filter-btn active"
                                                data-filter="all" data-bs-toggle="tooltip" title="Semua">
                                                <i class="bi bi-grid-fill"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary filter-btn"
                                                data-filter="penelitian" data-bs-toggle="tooltip" title="Penelitian">
                                                <i class="bi bi-search"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary filter-btn"
                                                data-filter="pengabdian" data-bs-toggle="tooltip" title="Pengabdian">
                                                <i class="bi bi-people-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div style="height: 180px;">
                                        <canvas id="chartKegiatan"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card h-100 bg-white">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                                        <i class="bi bi-journal-check fs-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Total Publikasi</h6>
                                        <h2 class="mb-0 fw-bold">{{ $totalPublikasi }}</h2>
                                        <small class="text-muted">Jurnal, Prosiding, Buku, dll.</small>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="small fw-bold text-muted"><i class="bi bi-graph-up me-1"></i> Tren
                                            Pertahun</span>
                                    </div>
                                    <div style="height: 180px;">
                                        <canvas id="chartPublikasi"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lists Section --}}
                <div class="row g-4 mb-4">
                    {{-- Kegiatan Ketua --}}
                    <div class="col-lg-6">
                        <div class="card list-card">
                            <div
                                class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold mb-1"><i class="bi bi-star-fill text-warning me-2"></i>Kegiatan Saya
                                        (Ketua)</h5>
                                    <div class="btn-group mt-2" role="group">
                                        <button type="button" class="btn btn-xs btn-primary list-filter-btn active"
                                            data-target="list-ketua" data-filter="all" title="Semua">
                                            Semua
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-primary list-filter-btn"
                                            data-target="list-ketua" data-filter="penelitian" title="Penelitian">
                                            Penelitian
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-primary list-filter-btn"
                                            data-target="list-ketua" data-filter="pengabdian" title="Pengabdian">
                                            Pengabdian
                                        </button>
                                    </div>
                                </div>
                                <a href="{{ route('projects.my') }}"
                                    class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3">
                                    Kelola <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body p-0">
                                @if ($kegiatanSayaKetua->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 opacity-25"></i>
                                        <p class="mt-2 small">Belum ada data kegiatan.</p>
                                    </div>
                                @else
                                    <div class="list-group list-group-flush scrollable-list" id="list-ketua">
                                        @foreach ($kegiatanSayaKetua as $p)
                                            <div class="list-group-item" data-jenis="{{ strtolower($p->jenis) }}">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <a href="{{ route('projects.show', $p) }}"
                                                        class="fw-bold text-decoration-none text-dark stretched-link">
                                                        {{ Str::limit($p->judul, 60) }}
                                                    </a>
                                                    @include('projects._validation_badge', [
                                                        'project' => $p,
                                                    ])
                                                </div>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <span
                                                        class="badge bg-light text-secondary border me-2">{{ ucfirst($p->jenis) }}</span>
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $p->tahun_pelaksanaan ?? ($p->tahun_usulan ?? '-') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Publikasi --}}
                    <div class="col-lg-6">
                        <div class="card list-card">
                            <div
                                class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0"><i
                                        class="bi bi-file-earmark-text-fill text-info me-2"></i>Publikasi Terbaru</h5>
                                <a href="{{ route('publications.my') }}"
                                    class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3">
                                    Kelola <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body p-0">
                                @if ($publikasiSaya->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x fs-1 opacity-25"></i>
                                        <p class="mt-2 small">Belum ada data publikasi.</p>
                                    </div>
                                @else
                                    <div class="list-group list-group-flush scrollable-list">
                                        @foreach ($publikasiSaya as $pub)
                                            @php
                                                $status = $pub->validation_status ?? 'draft';
                                                [$badgeClass, $label] = match ($status) {
                                                    'approved' => ['bg-success', 'Disetujui'],
                                                    'pending' => ['bg-secondary', 'Pending'],
                                                    'revision_requested' => ['bg-warning text-dark', 'Perlu Revisi'],
                                                    'rejected' => ['bg-danger', 'Ditolak'],
                                                    'draft' => ['bg-light text-dark', 'Draft'],
                                                    default => ['bg-light text-muted', ucfirst($status)],
                                                };
                                            @endphp
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <a href="{{ route('publications.show', $pub) }}"
                                                        class="fw-bold text-decoration-none text-dark stretched-link">
                                                        {{ Str::limit($pub->judul, 60) }}
                                                    </a>
                                                    <span class="badge {{ $badgeClass }} rounded-pill"
                                                        style="font-size: 0.7rem;">{{ $label }}</span>
                                                </div>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <span
                                                        class="badge bg-light text-secondary border me-2">{{ ucfirst($pub->jenis) }}</span>
                                                    <i class="bi bi-calendar-check me-1"></i> {{ $pub->tahun ?? '-' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Kegiatan Anggota --}}
            <div class="row">
                <div class="col-12">
                    <div class="card list-card">
                        <div
                            class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1"><i class="bi bi-people-fill text-secondary me-2"></i>Kegiatan
                                    Sebagai
                                    Anggota</h5>
                                <div class="btn-group mt-2" role="group">
                                    <button type="button" class="btn btn-xs btn-primary list-filter-btn active"
                                        data-target="list-anggota" data-filter="all" title="Semua">
                                        Semua
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-primary list-filter-btn"
                                        data-target="list-anggota" data-filter="penelitian" title="Penelitian">
                                        Penelitian
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-primary list-filter-btn"
                                        data-target="list-anggota" data-filter="pengabdian" title="Pengabdian">
                                        Pengabdian
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if ($kegiatanSebagaiAnggota->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 opacity-25"></i>
                                    <p class="mt-2 small">Belum ada data keikutsertaan.</p>
                                </div>
                            @else
                                <div class="list-group list-group-flush scrollable-list" id="list-anggota">
                                    @foreach ($kegiatanSebagaiAnggota as $p)
                                        <div class="list-group-item" data-jenis="{{ strtolower($p->jenis) }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="{{ route('projects.show', $p) }}"
                                                        class="fw-bold text-decoration-none text-dark">
                                                        {{ $p->judul }}
                                                    </a>
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-person-circle me-1"></i> Ketua:
                                                        {{ optional($p->ketua)->name }}
                                                        <span class="mx-2">•</span>
                                                        <span
                                                            class="badge bg-light text-secondary border">{{ ucfirst($p->jenis) }}</span>
                                                    </div>
                                                </div>
                                                @include('projects._validation_badge', ['project' => $p])
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const rawKegiatan = @json($activityByYear);
        const rawPublikasi = @json($publicationByYear);

        // Helper: Process Activity Data
        function processActivityData(filterType) {
            const grouped = {};
            rawKegiatan.forEach(item => {
                if (filterType === 'all' || item.jenis === filterType) {
                    grouped[item.tahun] = (grouped[item.tahun] || 0) + item.total;
                }
            });

            // Ensure we have sorted labels
            const labels = Object.keys(grouped).sort();
            const data = labels.map(year => grouped[year]);

            return {
                labels,
                data
            };
        }

        // Init Activity Chart
        let activityChart;
        if (document.getElementById('chartKegiatan')) {
            const ctx = document.getElementById('chartKegiatan').getContext('2d');
            const {
                labels,
                data
            } = processActivityData('all');

            activityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Kegiatan',
                        data: data,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderRadius: 4,
                        barThickness: 20
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
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                borderDash: [2, 4],
                                color: '#f0f0f0'
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

            // Filter Listener (Buttons)
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update Active State
                    document.querySelectorAll('.filter-btn').forEach(b => {
                        b.classList.remove('active', 'btn-primary');
                        b.classList.add('btn-outline-primary');
                    });
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('active', 'btn-primary');

                    // Update Chart
                    const filterValue = this.getAttribute('data-filter');
                    const {
                        labels,
                        data
                    } = processActivityData(filterValue);
                    activityChart.data.labels = labels;
                    activityChart.data.datasets[0].data = data;
                    activityChart.update();
                });
            });

            // Init Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }

        // Init Publication Chart
        if (document.getElementById('chartPublikasi')) {
            const labels = rawPublikasi.map(x => x.tahun);
            const data = rawPublikasi.map(x => x.total);

            new Chart(document.getElementById('chartPublikasi').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Publikasi',
                        data: data,
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        borderRadius: 4,
                        barThickness: 20
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
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                borderDash: [2, 4],
                                color: '#f0f0f0'
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
        }

        // List Filtering Logic
        document.querySelectorAll('.list-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const filterValue = this.getAttribute('data-filter');
                const container = document.getElementById(targetId);

                // Update Buttons
                const group = this.closest('.btn-group');
                group.querySelectorAll('.list-filter-btn').forEach(b => {
                    b.classList.remove('active', 'btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('active', 'btn-primary');

                // Filter Items
                if (container) {
                    const items = container.querySelectorAll('.list-group-item');
                    items.forEach(item => {
                        if (filterValue === 'all' || item.getAttribute('data-jenis') ===
                            filterValue) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            });
        });
    </script>
@endpush
