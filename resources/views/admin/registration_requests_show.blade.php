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

        .detail-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .detail-card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }

        .detail-card-title {
            font-weight: 700;
            margin-bottom: 0;
            color: #0a58ca;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }

        .detail-card-body {
            padding: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 500;
            color: #212529;
            font-size: 1rem;
        }

        .action-card {
            background: #fff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 2rem;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('admin.registration-requests.index') }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="fw-bold mb-2">{{ $req->name }}</h1>
                        <div class="d-flex align-items-center gap-2 text-white-50">
                            <span><i class="bi bi-envelope me-1"></i> {{ $req->email }}</span>
                            <span class="mx-1">•</span>
                            <span><i class="bi bi-clock me-1"></i> Diajukan:
                                {{ $req->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    @php
                        $status = $req->status ?? 'pending';
                        $badgeClass = match ($status) {
                            'approved' => 'bg-success',
                            'pending' => 'bg-warning text-dark',
                            'rejected' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill shadow-sm">
                        {{ ucfirst($status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row g-4">
                {{-- Left Column: Details --}}
                <div class="col-lg-8">
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-person-lines-fill me-2"></i> Detail Pemohon</h5>
                        </div>
                        <div class="detail-card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="info-label">Nama Lengkap</div>
                                    <div class="info-value">{{ $req->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $req->email }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Role yang Diajukan</div>
                                    <div class="info-value text-capitalize">{{ $req->role }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Identitas (NIM / NIDN / NIP)</div>
                                    <div class="info-value">{{ $req->identity ?? '-' }}</div>
                                </div>
                                @if ($req->role === 'dosen')
                                    <div class="col-md-6">
                                        <div class="info-label">SINTA ID</div>
                                        <div class="info-value">{{ $req->sinta_id ?? '-' }}</div>
                                    </div>
                                @endif

                                @if ($req->note)
                                    <div class="col-12">
                                        <div class="info-label mb-2">Catatan Admin</div>
                                        <div class="p-3 bg-light rounded border">
                                            {{ $req->note }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Actions --}}
                <div class="col-lg-4">
                    <div class="card action-card">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-primary"></i> Proses Permohonan
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($req->status === 'pending')
                                <div class="d-grid gap-3">
                                    {{-- Approve Form --}}
                                    <form action="{{ route('admin.registration-requests.approve', $req) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Catatan Persetujuan
                                                (Opsional)</label>
                                            <textarea name="note" rows="2" class="form-control" placeholder="Contoh: Data valid, akun disetujui."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm"
                                            onclick="return confirm('Setujui permohonan ini? Akun akan dibuat otomatis.')">
                                            <i class="bi bi-check-circle-fill me-2"></i> Setujui Permohonan
                                        </button>
                                    </form>

                                    <hr class="my-1 text-muted opacity-25">

                                    {{-- Reject Form --}}
                                    <form action="{{ route('admin.registration-requests.reject', $req) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Alasan Penolakan
                                                (Opsional)</label>
                                            <textarea name="note" rows="2" class="form-control" placeholder="Contoh: NIM tidak ditemukan."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2"
                                            onclick="return confirm('Tolak permohonan ini?')">
                                            <i class="bi bi-x-circle-fill me-2"></i> Tolak Permohonan
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Permohonan ini telah diproses dengan status
                                    <strong>{{ ucfirst($req->status) }}</strong>.
                                </div>
                                <div class="mt-3 d-grid">
                                    <a href="{{ route('admin.registration-requests.index') }}"
                                        class="btn btn-outline-secondary">
                                        Kembali ke Daftar
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
