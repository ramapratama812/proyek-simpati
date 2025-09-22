@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Tambah Publikasi</h4>
  <form method="POST" action="{{ route('publications.store') }}" class="card p-4">
    @csrf
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label">Judul</label>
        <input name="judul" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Jenis</label>
        <input name="jenis" class="form-control" placeholder="article, proceeding, dst">
      </div>
      <div class="col-md-6">
        <label class="form-label">Jurnal/Prosiding</label>
        <input name="jurnal" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="tahun" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">DOI</label>
        <input name="doi" class="form-control">
      </div>
    </div>
    <div class="mt-3 text-end">
      <button class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
@endsection
