@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Edit Publikasi</h4>

  <form method="POST" action="{{ route('publications.update', $publication) }}" class="card p-4 shadow-sm"
        onsubmit="return confirm('Simpan perubahan publikasi?');">
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
        <input name="tahun" type="number" class="form-control" value="{{ old('tahun', $publication->tahun) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">DOI</label>
        <input name="doi" class="form-control" value="{{ old('doi', $publication->doi) }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Volume</label>
        <input type="number" name="volume" class="form-control" min="1" value="{{ old('volume', $publication->volume) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Nomor</label>
        <input type="number" name="nomor" class="form-control" min="1" value="{{ old('nomor', $publication->nomor) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Jumlah Halaman</label>
        <input name="jumlah_halaman" type="number" class="form-control" min="1" value="{{ old('jumlah_halaman', $publication->jumlah_halaman) }}">
      </div>

      <div class="col-12">
        <label class="form-label">Penulis</label>
        <textarea name="penulis" class="form-control" rows="3" placeholder="Masukkan nama penulis, pisahkan dengan koma atau baris baru">{{ old('penulis', is_array($publication->penulis) ? implode(', ', $publication->penulis) : '') }}</textarea>
        <div class="form-text">Contoh: John Doe, Jane Smith</div>
      </div>

      <div class="col-12">
        <label class="form-label">Abstrak</label>
        <textarea name="abstrak" class="form-control" rows="4">{{ old('abstrak', $publication->abstrak) }}</textarea>
      </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
      <a href="{{ route('publications.show', $publication) }}" class="btn btn-outline-secondary">Batal</a>
      <button class="btn btn-primary">Usulkan Perubahan</button>
    </div>
  </form>
</div>
@endsection
