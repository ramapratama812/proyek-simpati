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

        .form-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .form-card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }

        .form-card-title {
            font-weight: 700;
            margin-bottom: 0;
            color: #0a58ca;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }

        .form-card-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .required-asterisk {
            color: #dc3545;
            margin-left: 3px;
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
            border-color: #ced4da;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('publications.show', $publication) }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
                    </a>
                </div>
                <h1 class="fw-bold mb-0">Edit Publikasi</h1>
                <p class="text-white-50 mb-0 mt-2">Perbarui informasi publikasi yang sudah ada.</p>
            </div>
        </div>

        <div class="container pb-5">
            <form method="POST" action="{{ route('publications.update', $publication) }}" enctype="multipart/form-data"
                onsubmit="return confirm('Simpan perubahan publikasi?');">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- Left Column: Main Form --}}
                    <div class="col-lg-8">
                        {{-- Data Utama --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-journal-bookmark me-2"></i> Data Publikasi</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Judul Publikasi<span
                                                class="required-asterisk">*</span></label>
                                        <input name="judul" class="form-control" required
                                            value="{{ old('judul', $publication->judul) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Publikasi</label>
                                        <input name="jenis" class="form-control"
                                            value="{{ old('jenis', $publication->jenis) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Jurnal / Prosiding</label>
                                        <input name="jurnal" class="form-control"
                                            value="{{ old('jurnal', $publication->jurnal) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tahun Terbit</label>
                                        <input name="tahun" type="number" class="form-control"
                                            value="{{ old('tahun', $publication->tahun) }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">DOI</label>
                                        <input name="doi" class="form-control"
                                            value="{{ old('doi', $publication->doi) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Penulis</label>
                                        <textarea name="penulis" class="form-control" rows="3"
                                            placeholder="Masukkan nama penulis, pisahkan dengan koma atau baris baru">{{ old('penulis', is_array($publication->penulis) ? implode(', ', $publication->penulis) : '') }}</textarea>
                                        <div class="form-text">Contoh: John Doe, Jane Smith</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Abstrak</label>
                                        <textarea name="abstrak" class="form-control" rows="5">{{ old('abstrak', $publication->abstrak) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Additional Info --}}
                    <div class="col-lg-4">
                        {{-- Detail Tambahan --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-list-ul me-2"></i> Detail Tambahan</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Volume</label>
                                        <input type="number" name="volume" class="form-control" min="1"
                                            value="{{ old('volume', $publication->volume) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor (Issue)</label>
                                        <input type="number" name="nomor" class="form-control" min="1"
                                            value="{{ old('nomor', $publication->nomor) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Jumlah Halaman</label>
                                        <input name="jumlah_halaman" type="number" class="form-control" min="1"
                                            value="{{ old('jumlah_halaman', $publication->jumlah_halaman) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Upload File --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-file-earmark-pdf me-2"></i> File Artikel</h5>
                            </div>
                            <div class="form-card-body">
                                <label class="form-label">File Saat Ini</label>
                                @if (!empty($publication->file))
                                    <div class="p-3 bg-light rounded-3 mb-3 border">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-file-earmark-pdf text-danger fs-4 me-2"></i>
                                            <span class="text-truncate small fw-bold text-dark">File Tersedia</span>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <a href="{{ asset('storage/' . $publication->file) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i> Lihat
                                            </a>
                                        </div>
                                        <div class="form-check mt-2 pt-2 border-top">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="remove_file" name="remove_file">
                                            <label class="form-check-label small text-danger fw-semibold"
                                                for="remove_file">
                                                Hapus file ini
                                            </label>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-secondary small mb-3">
                                        <i class="bi bi-info-circle me-1"></i> Belum ada file PDF.
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload File Baru</label>
                                    <input type="file" name="file" id="file" accept="application/pdf"
                                        class="form-control @error('file') is-invalid @enderror">
                                    <div class="form-text small text-muted mt-2">
                                        <i class="bi bi-info-circle me-1"></i> Mengganti file lama jika ada. (PDF, Maks
                                        10MB)
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('publications.show', $publication) }}"
                                class="btn btn-outline-secondary py-2">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
