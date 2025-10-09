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
    </div>

    <div class="mt-4 d-flex justify-content-between">
      <a href="{{ route('publications.show', $publication) }}" class="btn btn-outline-secondary">Batal</a>
      <button class="btn btn-primary">Simpan Perubahan</button>
    </div>
  </form>
</div>
@endsection
