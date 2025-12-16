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

        .table-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #e9ecef;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold mb-0">Kelola Publikasi Saya</h1>
                    <p class="text-white-50 mb-0 mt-2">Daftar publikasi ilmiah yang telah Anda unggah.</p>
                </div>
                <div>
                    <a href="{{ route('publications.create') }}" class="btn btn-light text-primary fw-bold shadow-sm">
                        <i class="bi bi-plus-lg me-2"></i> Tambah Publikasi
                    </a>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            @if (session('ok'))
                <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>{{ session('ok') }}</div>
                </div>
            @endif
            @if (session('err'))
                <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill fs-4 me-3"></i>
                    <div>{{ session('err') }}</div>
                </div>
            @endif

            {{-- Filter Section --}}
            <div class="card filter-card">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Pencarian</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="bi bi-search text-muted"></i></span>
                                <input type="text" name="q" class="form-control border-start-0 ps-0"
                                    value="{{ $filterQ }}" placeholder="Judul, jurnal, atau penerbit...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Jenis</label>
                            <select name="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="jurnal" {{ $filterJenis === 'jurnal' ? 'selected' : '' }}>Jurnal</option>
                                <option value="prosiding" {{ $filterJenis === 'prosiding' ? 'selected' : '' }}>Prosiding
                                </option>
                                <option value="buku" {{ $filterJenis === 'buku' ? 'selected' : '' }}>Buku</option>
                                <option value="lainnya" {{ $filterJenis === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Tahun</label>
                            <select name="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahunOptions as $tahun)
                                    <option value="{{ $tahun }}"
                                        {{ (string) $filterTahun === (string) $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Status Validasi</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Menunggu
                                    Validasi</option>
                                <option value="revision_requested"
                                    {{ $filterStatus === 'revision_requested' ? 'selected' : '' }}>Perlu Revisi</option>
                                <option value="approved" {{ $filterStatus === 'approved' ? 'selected' : '' }}>Disetujui
                                </option>
                                <option value="rejected" {{ $filterStatus === 'rejected' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold flex-fill">
                                <i class="bi bi-funnel-fill me-1"></i> Terapkan
                            </button>
                            <a href="{{ route('publications.my') }}" class="btn btn-light text-secondary border fw-bold">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="card table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4" style="width: 50px;">#</th>
                                    <th>Judul Publikasi</th>
                                    <th style="width: 120px;">Jenis</th>
                                    <th style="width: 100px;">Tahun</th>
                                    <th>Jurnal / Penerbit</th>
                                    <th style="width: 150px;">Status</th>
                                    <th class="text-end pe-4" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($publications as $p)
                                    @php
                                        $status = $p->validation_status ?? 'draft';
                                        [$badgeClass, $label] = match ($status) {
                                            'approved' => ['bg-success', 'Disetujui'],
                                            'pending' => ['bg-secondary', 'Pending'],
                                            'revision_requested' => ['bg-warning text-dark', 'Perlu Revisi'],
                                            'rejected' => ['bg-danger', 'Ditolak'],
                                            'draft' => ['bg-light text-dark', 'Draft'],
                                            default => ['bg-light text-muted', ucfirst($status)],
                                        };
                                    @endphp
                                    <tr>
                                        <td class="ps-4 text-muted">
                                            {{ $loop->iteration + ($publications->currentPage() - 1) * $publications->perPage() }}
                                        </td>
                                        <td>
                                            <a href="{{ route('publications.show', $p) }}"
                                                class="fw-bold text-decoration-none text-dark">
                                                {{ $p->judul }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border text-capitalize">
                                                {{ $p->jenis ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $p->tahun ?? '-' }}</td>
                                        <td>{{ $p->jurnal ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $badgeClass }} rounded-pill">{{ $label }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <a href="{{ route('publications.edit', $p) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit"
                                                    @if ($status === 'approved') disabled style="pointer-events: none; opacity: 0.5;" @endif>
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('publications.destroy', $p) }}"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus publikasi ini?');"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger rounded-end" type="submit"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-journal-x fs-1 opacity-25"></i>
                                                <p class="mt-2">Belum ada publikasi yang ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($publications->hasPages())
                    <div class="card-footer bg-white border-top-0 py-3">
                        {{ $publications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
