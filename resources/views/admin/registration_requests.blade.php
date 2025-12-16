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

        .request-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            background: #fff;
            margin-bottom: 1rem;
            border-left: 4px solid transparent;
        }

        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .request-card.status-pending {
            border-left-color: #ffc107;
        }

        .request-card.status-approved {
            border-left-color: #198754;
        }

        .request-card.status-rejected {
            border-left-color: #dc3545;
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
                <h1 class="fw-bold mb-0">Permohonan Akun</h1>
                <p class="text-white-50 mb-0 mt-2">Kelola pendaftaran akun baru dari dosen dan mahasiswa.</p>
            </div>
        </div>

        <div class="container pb-5">
            {{-- Filter Section --}}
            <div class="card filter-card">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.registration-requests.index') }}"
                        class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Filter Status</label>
                            <select name="status" class="form-select">
                                <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status
                                </option>
                                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Menunggu
                                    (Pending)</option>
                                <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Disetujui
                                    (Approved)</option>
                                <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak
                                    (Rejected)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="latest" {{ ($sort ?? 'latest') === 'latest' ? 'selected' : '' }}>Terbaru
                                </option>
                                <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="name_asc" {{ ($sort ?? '') === 'name_asc' ? 'selected' : '' }}>Nama (A-Z)
                                </option>
                                <option value="name_desc" {{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}>Nama (Z-A)
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary fw-bold w-100">
                                    <i class="bi bi-funnel-fill me-1"></i> Terapkan
                                </button>
                                <a href="{{ route('admin.registration-requests.index') }}"
                                    class="btn btn-outline-secondary w-100">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- List Section --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-secondary mb-0">Daftar Permohonan</h5>
                <span class="badge bg-light text-dark border">{{ $requests->total() }} total</span>
            </div>

            <div class="list-container">
                @forelse($requests as $req)
                    <div class="card request-card p-3 status-{{ $req->status }}">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box bg-light text-secondary">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $req->name }}</h6>
                                    <div class="text-muted small">
                                        <i class="bi bi-envelope me-1"></i> {{ $req->email }}
                                        <span class="mx-2">•</span>
                                        <span
                                            class="text-capitalize badge bg-light text-dark border">{{ $req->role }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 ms-auto">
                                <div class="text-end d-none d-md-block">
                                    <div class="small text-muted mb-1">Diajukan</div>
                                    <div class="fw-medium small">{{ $req->created_at->format('d M Y, H:i') }}</div>
                                </div>

                                @php
                                    $badgeClass = match ($req->status) {
                                        'approved' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span
                                    class="badge {{ $badgeClass }} rounded-pill px-3">{{ ucfirst($req->status) }}</span>

                                <a href="{{ route('admin.registration-requests.show', $req) }}"
                                    class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                    Proses <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-inbox text-muted opacity-25" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Belum ada permohonan</h5>
                        <p class="text-muted small">Belum ada data permohonan pendaftaran akun yang sesuai filter.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection
