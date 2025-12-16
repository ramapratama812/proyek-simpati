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
            <div class="container">
                <h1 class="fw-bold mb-0">Kelola Kegiatan Saya</h1>
                <p class="text-white-50 mb-0 mt-2">Daftar penelitian dan pengabdian masyarakat yang Anda kelola.</p>
            </div>
        </div>

        <div class="container pb-5">
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
                                    value="{{ $filterQ }}" placeholder="Judul, skema, atau bidang ilmu...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Jenis</label>
                            <select name="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="penelitian" {{ $filterJenis === 'penelitian' ? 'selected' : '' }}>Penelitian
                                </option>
                                <option value="pengabdian" {{ $filterJenis === 'pengabdian' ? 'selected' : '' }}>Pengabdian
                                </option>
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
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold flex-fill">
                                <i class="bi bi-funnel-fill me-1"></i> Terapkan
                            </button>
                            <a href="{{ route('projects.my') }}" class="btn btn-light text-secondary border fw-bold">
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
                                    <th>Judul Kegiatan</th>
                                    <th style="width: 120px;">Jenis</th>
                                    <th style="width: 100px;">Tahun</th>
                                    <th style="width: 150px;">Status</th>
                                    <th style="width: 100px;">Peran</th>
                                    <th class="text-end pe-4" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $p)
                                    @php
                                        $role = '';
                                        if ($p->ketua_id == auth()->id()) {
                                            $role = 'Ketua';
                                        } elseif ($p->created_by == auth()->id()) {
                                            $role = 'Pengusul';
                                        } elseif ($p->members->contains('id', auth()->id())) {
                                            $role = 'Anggota';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-4 text-muted">
                                            {{ $loop->iteration + ($projects->currentPage() - 1) * $projects->perPage() }}</td>
                                        <td>
                                            <a href="{{ route('projects.show', $p) }}"
                                                class="fw-bold text-decoration-none text-dark">
                                                {{ $p->judul }}
                                            </a>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-diagram-2 me-1"></i> Skema: {{ $p->skema ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border text-capitalize">
                                                {{ $p->jenis }}
                                            </span>
                                        </td>
                                        <td>{{ $p->tahun_pelaksanaan ?? ($p->tahun_usulan ?? '-') }}</td>
                                        <td>@include('projects._validation_badge', ['project' => $p])</td>
                                        <td>
                                            <span
                                                class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                                {{ $role }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <a href="{{ route('projects.show', $p) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if ($p->ketua_id == auth()->id() && $p->validation_status !== 'approved')
                                                    <a href="{{ route('projects.edit', $p) }}"
                                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1 opacity-25"></i>
                                                <p class="mt-2">Belum ada kegiatan yang ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
