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

        .table-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #495057;
            margin-right: 0.75rem;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-white text-primary px-3 py-1 rounded-pill fw-bold small text-uppercase">Admin
                        Panel</span>
                </div>
                <h1 class="fw-bold mb-0">Validasi Kegiatan</h1>
                <p class="text-white-50 mb-0 mt-2">Kelola persetujuan usulan kegiatan penelitian dan pengabdian.</p>
            </div>
        </div>

        <div class="container pb-5">
            <div class="card table-card">
                <div class="table-card-header">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-check-circle me-2"></i>Daftar Usulan</h5>

                    <form method="GET" class="d-flex align-items-center">
                        <label class="me-2 small fw-bold text-muted">Status:</label>
                        <select name="status" class="form-select form-select-sm border-secondary-subtle"
                            style="width: 180px;" onchange="this.form.submit()">
                            @php
                                $opts = [
                                    'pending' => 'Menunggu Validasi',
                                    'revision_requested' => 'Perlu Revisi',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    'draft' => 'Draft',
                                ];
                            @endphp
                            @foreach ($opts as $val => $label)
                                <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Judul Kegiatan</th>
                                <th>Pengusul</th>
                                <th>Jenis</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $p)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark mb-1">{{ Str::limit($p->judul, 60) }}</div>
                                        <div class="small text-muted">ID: #{{ $p->id }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle">
                                                {{ substr(optional($p->ketua)->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold small">{{ optional($p->ketua)->name }}</div>
                                                <div class="text-muted small" style="font-size: 0.75rem;">Ketua</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($p->jenis == 'penelitian')
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">Penelitian</span>
                                        @else
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Pengabdian</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $p->created_at?->format('d M Y') }}
                                    </td>
                                    <td>
                                        @include('projects._validation_badge', ['project' => $p])
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('projects.validation.show', $p) }}"
                                            class="btn btn-sm btn-outline-primary fw-bold shadow-sm">
                                            <i class="bi bi-search me-1"></i> Periksa
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="bi bi-inbox fs-1"></i></div>
                                        <h6 class="fw-bold text-secondary">Tidak ada data ditemukan</h6>
                                        <p class="small text-muted mb-0">Belum ada usulan kegiatan dengan status ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($projects->hasPages())
                    <div class="card-footer bg-white border-top-0 py-3">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
