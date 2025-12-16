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
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .action-header {
            padding: 1.25rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .action-body {
            padding: 1.5rem;
        }

        .validation-section {
            border-left: 4px solid #dee2e6;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }

        .validation-section.approve {
            border-color: #198754;
        }

        .validation-section.revision {
            border-color: #ffc107;
        }

        .validation-section.reject {
            border-color: #dc3545;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('projects.validation.index') }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="fw-bold mb-2">Validasi Kegiatan</h1>
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="badge bg-white text-primary px-3 py-1 rounded-pill fw-bold text-uppercase small">{{ $project->jenis }}</span>
                            @include('projects._validation_badge', ['project' => $project])
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row g-4">
                {{-- Left Column: Details --}}
                <div class="col-lg-8">

                    {{-- Main Info --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-info-circle me-2"></i> Detail Kegiatan</h5>
                        </div>
                        <div class="detail-card-body">
                            <h4 class="fw-bold text-dark mb-4">{{ $project->judul }}</h4>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="info-label">Ketua Pengusul</div>
                                    <div class="info-value d-flex align-items-center">
                                        <div class="avatar-circle bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2"
                                            style="width: 32px; height: 32px;">
                                            {{ substr(optional($project->ketua)->name ?? '?', 0, 1) }}
                                        </div>
                                        {{ optional($project->ketua)->name }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Skema</div>
                                    <div class="info-value">{{ $project->skema }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Bidang Ilmu</div>
                                    <div class="info-value">{{ $project->bidang_ilmu }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Kategori Kegiatan</div>
                                    <div class="info-value">{{ $project->kategori_kegiatan }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Tahun Usulan</div>
                                    <div class="info-value">{{ $project->tahun_usulan }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Tahun Pelaksanaan</div>
                                    <div class="info-value">{{ $project->tahun_pelaksanaan }}</div>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25">

                            <div class="mb-4">
                                <div class="info-label mb-2">Abstrak</div>
                                <div class="bg-light p-3 rounded text-secondary" style="line-height: 1.6;">
                                    {{ $project->abstrak }}
                                </div>
                            </div>

                            @if ($project->validation_note)
                                <div class="alert alert-warning border-0 shadow-sm d-flex">
                                    <i class="bi bi-exclamation-circle-fill fs-4 me-3 text-warning"></i>
                                    <div>
                                        <div class="fw-bold text-dark">Catatan Validasi Terakhir</div>
                                        <div class="text-secondary">{{ $project->validation_note }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Publications --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-journal-text me-2"></i> Publikasi Terkait</h5>
                        </div>
                        <div class="detail-card-body p-0">
                            @if ($project->publications->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-1 mb-2 d-block"></i>
                                    Belum ada publikasi yang dikaitkan.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3">Judul Publikasi</th>
                                                <th>Tahun</th>
                                                <th>Jenis</th>
                                                <th class="text-end pe-4">Tautan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($project->publications as $pub)
                                                <tr>
                                                    <td class="ps-4 fw-medium">
                                                        {{ $pub->title ?? ($pub->judul ?? 'Tanpa judul') }}</td>
                                                    <td>{{ $pub->year ?? ($pub->tahun ?? '-') }}</td>
                                                    <td><span
                                                            class="badge bg-light text-dark border">{{ $pub->type ?? ($pub->jenis ?? '-') }}</span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        @php
                                                            $url =
                                                                $pub->url ??
                                                                ($pub->tautan ?? ($pub->link ?? ($pub->doi ?? null)));
                                                        @endphp
                                                        @if ($url)
                                                            <a href="{{ route('publications.show', $pub) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-box-arrow-up-right"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right Column: Actions --}}
                <div class="col-lg-4">

                    {{-- Files --}}
                    <div class="card action-card mb-4">
                        <div class="action-header bg-light text-dark">
                            <i class="bi bi-folder2-open me-2"></i> Berkas Pendukung
                        </div>
                        <div class="action-body">
                            @if ($project->surat_proposal)
                                <a href="{{ asset('storage/' . $project->surat_proposal) }}" target="_blank"
                                    class="btn btn-outline-primary w-100 mb-2 text-start d-flex align-items-center p-3 border-2">
                                    <i class="bi bi-file-earmark-pdf fs-3 me-3"></i>
                                    <div class="lh-sm">
                                        <div class="fw-bold">Surat Proposal</div>
                                        <div class="small text-muted">Klik untuk melihat</div>
                                    </div>
                                </a>
                            @else
                                <div class="alert alert-secondary mb-2 small"><i class="bi bi-info-circle me-1"></i>
                                    Proposal belum diunggah</div>
                            @endif

                            @if ($project->surat_persetujuan)
                                <a href="{{ asset('storage/' . $project->surat_persetujuan) }}" target="_blank"
                                    class="btn btn-outline-success w-100 mb-2 text-start d-flex align-items-center p-3 border-2">
                                    <i class="bi bi-file-earmark-check fs-3 me-3"></i>
                                    <div class="lh-sm">
                                        <div class="fw-bold">Surat Persetujuan</div>
                                        <div class="small text-muted">Klik untuk melihat</div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Validation Actions --}}
                    <div class="card action-card">
                        <div class="action-header bg-primary text-white">
                            <i class="bi bi-shield-check me-2"></i> Aksi Validasi
                        </div>
                        <div class="action-body">

                            {{-- Approve --}}
                            <div class="validation-section approve">
                                <h6 class="fw-bold text-success mb-2"><i class="bi bi-check-circle-fill me-1"></i> Setujui
                                    Kegiatan</h6>
                                <form method="POST" action="{{ route('projects.validation.approve', $project) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Catatan (opsional)</label>
                                        <textarea name="note" class="form-control form-control-sm" rows="2" placeholder="Tambahkan catatan...">{{ old('note') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Surat Persetujuan P3M (PDF) <span
                                                class="text-danger">*</span></label>
                                        <input type="file" name="surat_persetujuan"
                                            class="form-control form-control-sm" accept="application/pdf" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm"
                                        onclick="return confirm('Setujui usulan kegiatan ini?');">
                                        Setujui Usulan
                                    </button>
                                </form>
                            </div>

                            <hr class="my-4">

                            {{-- Revision --}}
                            <div class="validation-section revision">
                                <h6 class="fw-bold text-warning mb-2"><i class="bi bi-arrow-counterclockwise me-1"></i>
                                    Minta Revisi</h6>
                                <form method="POST" action="{{ route('projects.validation.revision', $project) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark">Catatan Revisi <span
                                                class="text-danger">*</span></label>
                                        <textarea name="note" class="form-control form-control-sm" rows="2" required
                                            placeholder="Jelaskan bagian yang perlu direvisi...">{{ old('note') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100 fw-bold text-dark shadow-sm"
                                        onclick="return confirm('Kirim permintaan revisi ke dosen?');">
                                        Kirim Permintaan Revisi
                                    </button>
                                </form>
                            </div>

                            <hr class="my-4">

                            {{-- Reject --}}
                            <div class="validation-section reject mb-0">
                                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-x-circle-fill me-1"></i> Tolak Usulan
                                </h6>
                                <form method="POST" action="{{ route('projects.validation.reject', $project) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark">Alasan Penolakan <span
                                                class="text-danger">*</span></label>
                                        <textarea name="note" class="form-control form-control-sm" rows="2" required
                                            placeholder="Jelaskan alasan penolakan...">{{ old('note') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm"
                                        onclick="return confirm('Tolak usulan kegiatan ini?');">
                                        Tolak Usulan
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
