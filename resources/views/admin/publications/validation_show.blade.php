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

        .validation-form-card {
            background: #fff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 2rem;
        }

        .status-option {
            cursor: pointer;
            transition: all 0.2s;
        }

        .status-option:hover {
            transform: translateY(-2px);
        }

        .btn-check:checked+.btn-outline-success {
            background-color: #198754;
            color: white;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }

        .btn-check:checked+.btn-outline-warning {
            background-color: #ffc107;
            color: #000;
            box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
        }

        .btn-check:checked+.btn-outline-danger {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('admin.publications.validation.index') }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="fw-bold mb-2">{{ $publication->judul }}</h1>
                        <div class="d-flex align-items-center flex-wrap gap-2 text-white-50">
                            <span
                                class="badge bg-white text-primary fw-bold px-3">{{ $publication->jenis ?? 'Artikel' }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i> {{ $publication->tahun ?? 'N/A' }}</span>
                            @if ($publication->doi)
                                <span class="mx-1">•</span>
                                <a href="https://doi.org/{{ $publication->doi }}" target="_blank"
                                    class="text-white-50 text-decoration-none hover-white">
                                    <i class="bi bi-link-45deg me-1"></i> DOI: {{ $publication->doi }}
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    @php
                        $status = $publication->validation_status ?? 'draft';
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
                {{-- Main Content: Details --}}
                <div class="col-lg-8">
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-journal-text me-2"></i> Detail Publikasi</h5>
                        </div>
                        <div class="detail-card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="info-label">Jurnal / Prosiding</div>
                                    <div class="info-value fs-5">{{ $publication->jurnal ?? '—' }}</div>
                                    @if ($publication->volume || $publication->nomor)
                                        <div class="text-muted small mt-1">
                                            @if ($publication->volume)
                                                Vol. {{ $publication->volume }}
                                            @endif
                                            @if ($publication->nomor)
                                                No. {{ $publication->nomor }}
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Penulis</div>
                                    <div class="info-value">
                                        @if (isset($publication->penulis) && is_array($publication->penulis))
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($publication->penulis as $penulis)
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
                                            <span class="fw-medium">{{ $publication->jumlah_halaman ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-muted small d-block">Pengunggah</span>
                                            <span class="fw-medium">{{ $publication->owner->name ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-label mb-2">Abstrak</div>
                                    <div class="p-3 bg-light rounded-3 text-secondary" style="text-align: justify;">
                                        {{ $publication->abstrak ?? 'Tidak ada abstrak.' }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-label mb-2">File Artikel</div>
                                    @if (!empty($publication->file))
                                        <div class="d-flex gap-2">
                                            <a href="{{ asset('storage/' . $publication->file) }}" target="_blank"
                                                class="btn btn-outline-primary">
                                                <i class="bi bi-eye me-2"></i> Pratinjau PDF
                                            </a>
                                            <a href="{{ asset('storage/' . $publication->file) }}" download
                                                class="btn btn-primary">
                                                <i class="bi bi-download me-2"></i> Download PDF
                                            </a>
                                        </div>
                                        <div class="mt-3">
                                            <div class="ratio ratio-16x9 border rounded-3 overflow-hidden bg-light">
                                                <iframe src="{{ asset('storage/' . $publication->file) }}"
                                                    title="PDF Preview"></iframe>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-secondary d-flex align-items-center">
                                            <i class="bi bi-exclamation-circle me-2"></i> File artikel belum diunggah.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar: Validation Form --}}
                <div class="col-lg-4">
                    <div class="card validation-form-card">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-primary"></i> Form Validasi</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($publication->validation_status !== 'pending')
                                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Publikasi ini sudah diproses dengan status
                                    <strong>{{ str_replace('_', ' ', ucfirst($publication->validation_status)) }}</strong>.
                                </div>

                                @if ($publication->validation_note)
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted">Catatan Admin</label>
                                        <div class="p-3 bg-light rounded border">
                                            {{ $publication->validation_note }}
                                        </div>
                                    </div>
                                @endif

                                <div class="d-grid">
                                    <a href="{{ route('admin.publications.validation.index') }}"
                                        class="btn btn-outline-secondary">
                                        Kembali ke Daftar
                                    </a>
                                </div>
                            @else
                                <form method="POST"
                                    action="{{ route('admin.publications.validation.update', $publication) }}">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="form-label fw-bold mb-3">Keputusan Validasi</label>
                                        <div class="d-grid gap-2">
                                            <div class="status-option">
                                                <input type="radio" class="btn-check" name="validation_status"
                                                    id="status-approved" value="approved" @checked(old('validation_status') === 'approved')>
                                                <label class="btn btn-outline-success w-100 py-2 text-start px-3"
                                                    for="status-approved">
                                                    <i class="bi bi-check-circle-fill me-2"></i> Setujui (Approve)
                                                </label>
                                            </div>

                                            <div class="status-option">
                                                <input type="radio" class="btn-check" name="validation_status"
                                                    id="status-revision" value="revision_requested"
                                                    @checked(old('validation_status') === 'revision_requested')>
                                                <label class="btn btn-outline-warning w-100 py-2 text-start px-3 text-dark"
                                                    for="status-revision">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Minta Revisi
                                                </label>
                                            </div>

                                            <div class="status-option">
                                                <input type="radio" class="btn-check" name="validation_status"
                                                    id="status-rejected" value="rejected" @checked(old('validation_status') === 'rejected')>
                                                <label class="btn btn-outline-danger w-100 py-2 text-start px-3"
                                                    for="status-rejected">
                                                    <i class="bi bi-x-circle-fill me-2"></i> Tolak (Reject)
                                                </label>
                                            </div>
                                        </div>
                                        @error('validation_status')
                                            <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>
                                                {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                                        <textarea name="validation_note" class="form-control" rows="4"
                                            placeholder="Tambahkan catatan untuk pengusul...">{{ old('validation_note') }}</textarea>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                            <i class="bi bi-send me-2"></i> Simpan Keputusan
                                        </button>
                                        <a href="{{ route('admin.publications.validation.index') }}"
                                            class="btn btn-light text-muted">
                                            Batal
                                        </a>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
