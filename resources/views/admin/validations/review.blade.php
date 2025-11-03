{{--
    View untuk Review/Validasi Proyek oleh Admin
--}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.validations.dashboard') }}">Dashboard Validasi</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.validations.pending') }}">Daftar Usulan</a>
                    </li>
                    <li class="breadcrumb-item active">Review Usulan</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Review Usulan {{ ucfirst($project->jenis) }}</h2>

            {{-- Status Badge --}}
            <div class="mb-3">
                @php
                    $statusBadge = [
                        'draft' => 'bg-secondary',
                        'submitted' => 'bg-warning',
                        'under_review' => 'bg-info',
                        'revision_needed' => 'bg-danger',
                        'approved' => 'bg-success',
                        'rejected' => 'bg-dark',
                    ];

                    $statusText = [
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'under_review' => 'Sedang Direview',
                        'revision_needed' => 'Perlu Revisi',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ];
                @endphp

                <span class="badge {{ $statusBadge[$project->validation_status] ?? 'bg-secondary' }} fs-6">
                    Status: {{ $statusText[$project->validation_status] ?? $project->validation_status }}
                </span>

                @if($project->submitted_at)
                    <span class="badge bg-light text-dark fs-6">
                        Diajukan: {{ $project->submitted_at->format('d/m/Y H:i') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Project Details --}}
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail Usulan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Judul</th>
                            <td>{{ $project->judul }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kegiatan</th>
                            <td>{{ ucfirst($project->jenis) }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $project->kategori_kegiatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Skema</th>
                            <td>{{ $project->skema ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bidang Ilmu</th>
                            <td>{{ $project->bidang_ilmu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Usulan</th>
                            <td>{{ $project->tahun_usulan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Periode Pelaksanaan</th>
                            <td>
                                {{ $project->mulai ? $project->mulai->format('d/m/Y') : '-' }}
                                s.d.
                                {{ $project->selesai ? $project->selesai->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Sumber Dana</th>
                            <td>{{ $project->sumber_dana ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Total Biaya</th>
                            <td>Rp {{ number_format($project->biaya ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Mitra/Instansi</th>
                            <td>{{ $project->mitra_nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $project->lokasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>TKT (Tingkat Kesiapterapan Teknologi)</th>
                            <td>{{ $project->tkt ?? '-' }}</td>
                        </tr>
                    </table>

                    <h6 class="mt-4">Abstrak</h6>
                    <div class="border p-3 bg-light">
                        {{ $project->abstrak ?? 'Tidak ada abstrak' }}
                    </div>

                    @if($project->keywords)
                        <h6 class="mt-3">Keywords</h6>
                        <p>{{ $project->keywords }}</p>
                    @endif

                    @if($project->target_luaran && count($project->target_luaran) > 0)
                        <h6 class="mt-3">Target Luaran</h6>
                        <ul>
                            @foreach($project->target_luaran as $luaran)
                                <li>{{ $luaran }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Tim Pelaksana --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Tim Pelaksana</h5>
                </div>
                <div class="card-body">
                    <h6>Ketua:</h6>
                    <p>
                        <strong>{{ $project->ketua->name ?? '-' }}</strong>
                        @if($project->ketua && $project->ketua->nidn)
                            (NIDN: {{ $project->ketua->nidn }})
                        @endif
                    </p>

                    <h6>Anggota:</h6>
                    @if($project->members && $project->members->count() > 0)
                        <ul>
                            @foreach($project->members as $member)
                                @if($member->pivot && $member->pivot->peran === 'anggota')
                                    <li>
                                        {{ $member->name }}
                                        @if($member->nidn)
                                            (NIDN: {{ $member->nidn }})
                                        @endif
                                        @if($member->nim)
                                            (NIM: {{ $member->nim }})
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada anggota</p>
                    @endif
                </div>
            </div>

            {{-- Dokumentasi --}}
            @if($project->images && $project->images->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Dokumentasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($project->images as $image)
                                <div class="col-md-3 mb-3">
                                    <a href="{{ asset('storage/' . $image->path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $image->path) }}"
                                             class="img-thumbnail"
                                             style="height: 150px; object-fit: cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Admin Actions & History --}}
        <div class="col-md-4">
            {{-- Documents --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($project->surat_proposal)
                            <a href="{{ route('admin.validations.downloadProposal', $project->id) }}"
                               class="btn btn-outline-primary">
                                <i class="fas fa-file-pdf"></i> Download Proposal
                            </a>
                        @else
                            <button class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-file-pdf"></i> Proposal Tidak Tersedia
                            </button>
                        @endif

                        @if($project->approval_letter)
                            <a href="{{ asset('storage/' . $project->approval_letter) }}"
                               target="_blank"
                               class="btn btn-outline-success">
                                <i class="fas fa-file-pdf"></i> Surat Persetujuan
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Admin Actions --}}
            @if(in_array($project->validation_status, ['submitted', 'under_review', 'revision_needed']))
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Aksi Validasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            {{-- Approve Button --}}
                            <button type="button"
                                    class="btn btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                <i class="fas fa-check"></i> Setujui Usulan
                            </button>

                            {{-- Request Revision Button --}}
                            <button type="button"
                                    class="btn btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#revisionModal">
                                <i class="fas fa-edit"></i> Minta Revisi
                            </button>

                            {{-- Reject Button --}}
                            <button type="button"
                                    class="btn btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                <i class="fas fa-times"></i> Tolak Usulan
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Validation History --}}
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Riwayat Validasi</h5>
                </div>
                <div class="card-body">
                    @if($validationHistory && $validationHistory->count() > 0)
                        <div class="timeline">
                            @foreach($validationHistory as $history)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">
                                            {{ $history->validated_at->format('d/m/Y H:i') }}
                                        </small>
                                        <span class="badge {{ $history->statusBadgeClass }}">
                                            {{ $history->formattedStatus }}
                                        </span>
                                    </div>
                                    <p class="mb-1">
                                        <strong>{{ $history->validator->name ?? 'System' }}</strong>
                                    </p>
                                    @if($history->notes)
                                        <small class="text-muted">{{ $history->notes }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Belum ada riwayat validasi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.validations.approve', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Setujui Usulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Anda akan menyetujui usulan ini. Pastikan semua dokumen telah diperiksa dengan teliti.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Surat Persetujuan <span class="text-danger">*</span></label>
                        <input type="file" name="approval_letter" class="form-control" accept=".pdf" required>
                        <small class="text-muted">Upload file PDF surat persetujuan (maks. 5MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_surat" class="form-control" required
                               placeholder="Contoh: 001/LPPM-TI/XI/2025">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" class="form-control" required
                               value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Catatan tambahan untuk dosen..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Setujui & Terbitkan Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Revision Modal --}}
<div class="modal fade" id="revisionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.validations.requestRevision', $project->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Minta Revisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Dosen akan diminta untuk merevisi usulan berdasarkan catatan yang Anda berikan.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Umum <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="3" required
                                  placeholder="Jelaskan secara umum mengapa revisi diperlukan..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Poin-poin yang Perlu Direvisi <span class="text-danger">*</span></label>
                        <div id="revision-points">
                            <div class="input-group mb-2">
                                <span class="input-group-text">1.</span>
                                <input type="text" name="revision_points[]" class="form-control" required
                                       placeholder="Poin revisi pertama...">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="add-revision-point">
                            <i class="fas fa-plus"></i> Tambah Poin
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.validations.reject', $project->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Usulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <strong>Perhatian!</strong>
                        Penolakan bersifat final. Dosen harus mengajukan usulan baru jika ingin melanjutkan.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="5" required
                                  placeholder="Jelaskan alasan penolakan dengan jelas dan konstruktif..."></textarea>
                        <small class="text-muted">Minimal 20 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Yakin ingin menolak usulan ini?')">
                        <i class="fas fa-times"></i> Tolak Usulan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Add revision point dynamically
    let pointCounter = 1;
    document.getElementById('add-revision-point').addEventListener('click', function() {
        pointCounter++;
        const container = document.getElementById('revision-points');
        const newPoint = document.createElement('div');
        newPoint.className = 'input-group mb-2';
        newPoint.innerHTML = `
            <span class="input-group-text">${pointCounter}.</span>
            <input type="text" name="revision_points[]" class="form-control" required
                   placeholder="Poin revisi ${pointCounter}...">
            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newPoint);
    });
</script>
@endsection
