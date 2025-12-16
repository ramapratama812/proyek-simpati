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
            margin-bottom: 2rem;
        }

        .validation-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            background: #fff;
            margin-bottom: 1rem;
            border-left: 4px solid transparent;
        }

        .validation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .validation-card.status-pending {
            border-left-color: #ffc107;
        }

        .validation-card.status-approved {
            border-left-color: #198754;
        }

        .validation-card.status-rejected {
            border-left-color: #dc3545;
        }

        .validation-card.status-revision_requested {
            border-left-color: #fd7e14;
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
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <h1 class="fw-bold mb-0">Validasi Publikasi</h1>
                <p class="text-white-50 mb-0 mt-2">Kelola dan validasi publikasi yang diajukan oleh dosen.</p>
            </div>
        </div>

        <div class="container pb-5">
            {{-- Filter Section --}}
            <div class="card filter-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3 bg-primary-subtle text-primary">
                            <i class="bi bi-funnel-fill"></i>
                        </div>
                        <h5 class="card-title mb-0 fw-bold">Filter & Urutan</h5>
                    </div>

                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">Status Validasi</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending (Menunggu)</option>
                                <option value="approved" @selected(request('status') === 'approved')>Approved (Disetujui)</option>
                                <option value="revision_requested" @selected(request('status') === 'revision_requested')>Perlu Revisi</option>
                                <option value="rejected" @selected(request('status') === 'rejected')>Rejected (Ditolak)</option>
                                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">Urutkan Berdasarkan</label>
                            <select name="sort" class="form-select">
                                <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru (Tanggal Dibuat)</option>
                                <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                                <option value="title_asc" @selected(request('sort') === 'title_asc')>Judul (A - Z)</option>
                                <option value="title_desc" @selected(request('sort') === 'title_desc')>Judul (Z - A)</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100 fw-bold">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- List Section --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-secondary mb-0">Daftar Pengajuan</h5>
                <span class="badge bg-light text-dark border">{{ $pubs->total() }} item</span>
            </div>

            <div class="list-container">
                @forelse($pubs as $p)
                    @php
                        $status = $p->validation_status ?? 'draft';
                        $badgeClass = match ($status) {
                            'approved' => 'bg-success-subtle text-success border border-success-subtle',
                            'pending' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                            'revision_requested'
                                => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                            'rejected' => 'bg-danger-subtle text-danger border border-danger-subtle',
                            default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                        };
                        $statusLabel = match ($status) {
                            'approved' => 'Disetujui',
                            'pending' => 'Menunggu Validasi',
                            'revision_requested' => 'Perlu Revisi',
                            'rejected' => 'Ditolak',
                            default => 'Draft',
                        };
                        $cardStatusClass = 'status-' . $status;
                    @endphp

                    <div class="card validation-card {{ $cardStatusClass }} p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1 pe-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3 me-2">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                        {{ $statusLabel }}
                                    </span>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>
                                        {{ $p->created_at?->format('d M Y, H:i') }}</small>
                                </div>
                                <h5 class="fw-bold mb-1">
                                    <a href="{{ route('admin.publications.validation.show', $p) }}"
                                        class="text-decoration-none text-dark stretched-link">
                                        {{ $p->judul }}
                                    </a>
                                </h5>
                                <div class="text-muted small">
                                    <span class="me-3"><i class="bi bi-journal-text me-1"></i>
                                        {{ $p->jurnal ?? '—' }}</span>
                                    @if ($p->owner)
                                        <span><i class="bi bi-person me-1"></i> {{ $p->owner->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end" style="z-index: 2; position: relative;">
                                <a href="{{ route('admin.publications.validation.show', $p) }}"
                                    class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    Proses <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-clipboard-check text-muted opacity-25" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Tidak ada publikasi untuk divalidasi</h5>
                        <p class="text-muted small">Semua pengajuan telah diproses atau belum ada pengajuan baru.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $pubs->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
