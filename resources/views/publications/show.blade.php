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
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 1rem;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('publications.index') }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="fw-bold mb-2">{{ $pub->judul }}</h1>
                        <div class="d-flex align-items-center flex-wrap gap-2 text-white-50">
                            <span class="badge bg-white text-primary fw-bold px-3">{{ $pub->jenis ?? 'Artikel' }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i> {{ $pub->tahun ?? 'N/A' }}</span>
                            @if ($pub->doi)
                                <span class="mx-1">•</span>
                                <a href="https://doi.org/{{ $pub->doi }}" target="_blank"
                                    class="text-white-50 text-decoration-none hover-white">
                                    <i class="bi bi-link-45deg me-1"></i> DOI: {{ $pub->doi }}
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    @php
                        $status = $pub->validation_status ?? 'draft';
                        $badgeClass = match ($status) {
                            'approved' => 'bg-success',
                            'pending' => 'bg-warning text-dark',
                            'revision_requested' => 'bg-warning text-dark',
                            'rejected' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        $statusLabel = match ($status) {
                            'approved' => 'Terverifikasi',
                            'pending' => 'Menunggu Validasi',
                            'revision_requested' => 'Perlu Revisi',
                            'rejected' => 'Ditolak',
                            default => 'Draft',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill shadow-sm">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row g-4">
                {{-- Main Content --}}
                <div class="col-lg-8">
                    {{-- Detail Publikasi --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-journal-text me-2"></i> Detail Publikasi</h5>
                        </div>
                        <div class="detail-card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="info-label">Jurnal / Prosiding</div>
                                    <div class="info-value fs-5">{{ $pub->jurnal ?? '—' }}</div>
                                    @if ($pub->volume || $pub->nomor)
                                        <div class="text-muted small mt-1">
                                            @if ($pub->volume)
                                                Vol. {{ $pub->volume }}
                                            @endif
                                            @if ($pub->nomor)
                                                No. {{ $pub->nomor }}
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Penulis</div>
                                    <div class="info-value">
                                        @if (isset($pub->penulis) && is_array($pub->penulis))
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($pub->penulis as $penulis)
                                                    <li class="mb-1"><i class="bi bi-person me-2 text-primary"></i>
                                                        {{ $penulis }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Informasi Tambahan</div>
                                    <div class="d-flex flex-column gap-2">
                                        <div>
                                            <span class="text-muted small d-block">Jumlah Halaman</span>
                                            <span class="fw-medium">{{ $pub->jumlah_halaman ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-muted small d-block">Pengunggah</span>
                                            <span class="fw-medium">{{ $pub->owner->name ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-label mb-2">Abstrak</div>
                                    <div class="p-3 bg-light rounded-3 text-secondary" style="text-align: justify;">
                                        {{ $pub->abstrak ?? 'Tidak ada abstrak.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- File Section --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-file-earmark-pdf me-2"></i> File Artikel</h5>
                        </div>
                        <div class="detail-card-body">
                            @if (!empty($pub->file))
                                <div class="d-grid gap-2">
                                    <a href="{{ asset('storage/' . $pub->file) }}" target="_blank"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-2"></i> Pratinjau PDF
                                    </a>
                                    <a href="{{ asset('storage/' . $pub->file) }}" download class="btn btn-primary">
                                        <i class="bi bi-download me-2"></i> Download PDF
                                    </a>
                                </div>
                                <div class="mt-3">
                                    <div class="ratio ratio-16x9 border rounded-3 overflow-hidden bg-light">
                                        <iframe src="{{ asset('storage/' . $pub->file) }}" title="PDF Preview"></iframe>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2 opacity-25"></i>
                                    <small>File artikel belum diunggah.</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions Section --}}
                    @php
                        $user = auth()->user();
                        $isAdmin = strtolower($user->role ?? '') === 'admin';
                        $canManage = \Illuminate\Support\Facades\Schema::hasColumn('publications', 'owner_id')
                            ? $isAdmin || $pub->owner_id === ($user->id ?? null)
                            : $isAdmin;
                    @endphp

                    @if ($canManage)
                        <div class="card detail-card">
                            <div class="detail-card-header">
                                <h5 class="detail-card-title"><i class="bi bi-gear me-2"></i> Aksi</h5>
                            </div>
                            <div class="detail-card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('publications.edit', $pub) }}" class="btn btn-warning text-white">
                                        <i class="bi bi-pencil-square me-2"></i> Edit Publikasi
                                    </a>

                                    @if ($pub->validation_status === 'draft' || $pub->validation_status === 'revision_requested')
                                        <form method="POST" action="{{ route('publications.submit', $pub->id) }}">
                                            @csrf
                                            <button class="btn btn-success w-100">
                                                <i class="bi bi-check-circle me-2"></i> Ajukan Validasi
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('publications.destroy', $pub) }}"
                                        onsubmit="return confirm('Hapus publikasi ini? Tindakan tidak bisa dibatalkan.');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger w-100">
                                            <i class="bi bi-trash me-2"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                                @if ($pub->validation_status === 'approved')
                                    <div class="alert alert-success mt-3 mb-0 small">
                                        <i class="bi bi-check-circle-fill me-1"></i> Publikasi terverifikasi.
                                    </div>
                                @elseif($pub->validation_status === 'revision_requested')
                                    <div class="alert alert-warning mt-3 mb-0 small">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu revisi.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
