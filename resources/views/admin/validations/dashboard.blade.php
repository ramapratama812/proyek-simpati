{{--
    View untuk Dashboard Validasi Admin
--}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Dashboard Validasi Admin</h2>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <i class="fas fa-clock"></i> Menunggu Review
                    </h5>
                    <h2 class="text-center">{{ $stats['pending'] }}</h2>
                    <a href="{{ route('admin.validations.pending') }}" class="btn btn-sm btn-warning w-100">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h5 class="card-title text-info">
                        <i class="fas fa-search"></i> Dalam Review
                    </h5>
                    <h2 class="text-center">{{ $stats['under_review'] }}</h2>
                    <a href="{{ route('admin.validations.pending', ['status' => 'under_review']) }}"
                       class="btn btn-sm btn-info w-100">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-edit"></i> Perlu Revisi
                    </h5>
                    <h2 class="text-center">{{ $stats['revision_needed'] }}</h2>
                    <a href="{{ route('admin.validations.pending', ['status' => 'revision_needed']) }}"
                       class="btn btn-sm btn-danger w-100">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="fas fa-check"></i> Disetujui Bulan Ini
                    </h5>
                    <h2 class="text-center">{{ $stats['approved_this_month'] }}</h2>
                    <a href="{{ route('admin.validations.approved') }}" class="btn btn-sm btn-success w-100">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Submissions --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-upload"></i> Usulan Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentSubmissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Pengusul</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSubmissions as $project)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.validations.review', $project->id) }}">
                                                    {{ Str::limit($project->judul, 30) }}
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    {{ ucfirst($project->jenis) }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ $project->createdBy->name ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $project->submitted_at ? $project->submitted_at->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.validations.review', $project->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Tidak ada usulan baru</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Approvals --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle"></i> Baru Disetujui
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentApprovals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Ketua</th>
                                        <th>Tanggal</th>
                                        <th>Surat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApprovals as $project)
                                        <tr>
                                            <td>
                                                <a href="{{ route('projects.show', $project->id) }}">
                                                    {{ Str::limit($project->judul, 30) }}
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    {{ ucfirst($project->jenis) }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ $project->ketua->name ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $project->approved_at ? $project->approved_at->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                @if($project->approval_letter)
                                                    <a href="{{ asset('storage/' . $project->approval_letter) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Tidak ada persetujuan terbaru</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tools"></i> Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.validations.pending') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> Daftar Usulan
                        </a>
                        <a href="{{ route('admin.validations.criteria') }}" class="btn btn-info">
                            <i class="fas fa-clipboard-check"></i> Kriteria Validasi
                        </a>
                        <a href="{{ route('admin.validations.templates') }}" class="btn btn-success">
                            <i class="fas fa-file-alt"></i> Template Surat
                        </a>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-file-export"></i> Export Laporan
                        </button>
                        <a href="{{ route('admin.validations.history') }}" class="btn btn-secondary">
                            <i class="fas fa-history"></i> Riwayat Validasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Export Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Laporan Validasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.validations.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="all">Semua Status</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Auto refresh dashboard every 60 seconds
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>
@endsection
