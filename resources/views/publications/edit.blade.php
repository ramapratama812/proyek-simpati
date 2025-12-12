@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="mb-3">Edit Publikasi</h4>

        <form method="POST" action="{{ route('publications.update', $publication) }}" class="card p-4 shadow-sm"
            enctype="multipart/form-data" onsubmit="return confirm('Simpan perubahan publikasi?');">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Judul</label>
                    <input name="judul" class="form-control" required value="{{ old('judul', $publication->judul) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis</label>
                    <input name="jenis" class="form-control" value="{{ old('jenis', $publication->jenis) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jurnal/Prosiding</label>
                    <input name="jurnal" class="form-control" value="{{ old('jurnal', $publication->jurnal) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <input name="tahun" type="number" class="form-control"
                        value="{{ old('tahun', $publication->tahun) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">DOI</label>
                    <input name="doi" class="form-control" value="{{ old('doi', $publication->doi) }}">
                </div>

                {{-- opsi tambah pdf artikel --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">File Artikel (PDF)</label>

                    @if (!empty($publication->file))
                        <div class="mb-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/' . $publication->file) }}"
                                target="_blank">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat File Saat Ini
                            </a>

                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" id="remove_file"
                                    name="remove_file">
                                <label class="form-check-label" for="remove_file">
                                    Hapus file yang sekarang
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="text-muted small mb-2">Belum ada file PDF.</div>
                    @endif

                    <input type="file" name="file" id="file" accept="application/pdf"
                        class="form-control @error('file') is-invalid @enderror">

                    <div class="form-text">Upload file baru untuk mengganti (PDF, maks 10MB).</div>

                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Volume</label>
                    <input type="number" name="volume" class="form-control" min="1"
                        value="{{ old('volume', $publication->volume) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor</label>
                    <input type="number" name="nomor" class="form-control" min="1"
                        value="{{ old('nomor', $publication->nomor) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Halaman</label>
                    <input name="jumlah_halaman" type="number" class="form-control" min="1"
                        value="{{ old('jumlah_halaman', $publication->jumlah_halaman) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Penulis</label>
                    <textarea name="penulis" class="form-control" rows="3"
                        placeholder="Masukkan nama penulis, pisahkan dengan koma atau baris baru">{{ old('penulis', is_array($publication->penulis) ? implode(', ', $publication->penulis) : '') }}</textarea>
                    <div class="form-text">Contoh: John Doe, Jane Smith</div>
                </div>

                <div class="col-12">
                    <label class="form-label">Abstrak</label>
                    <textarea name="abstrak" class="form-control" rows="4">{{ old('abstrak', $publication->abstrak) }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('publications.show', $publication) }}" class="btn btn-outline-secondary">Batal</a>
                <button class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
