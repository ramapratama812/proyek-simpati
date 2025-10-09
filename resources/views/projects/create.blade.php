@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Tambah Kegiatan</h4>
  <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="card p-4">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Jenis</label>
        <select name="jenis" class="form-select" required>
          <option value="penelitian">Penelitian</option>
          <option value="pengabdian">Pengabdian</option>
        </select>
      </div>
      <div class="col-md-8">
        <label class="form-label">Judul</label>
        <input name="judul" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Kategori Kegiatan</label>
        <input name="kategori_kegiatan" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Bidang Ilmu</label>
        <input name="bidang_ilmu" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Skema</label>
        <input name="skema" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="mulai" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="selesai" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Sumber Dana</label>
        <input name="sumber_dana" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Biaya (Rp)</label>
        <input name="biaya" type="number" step="0.01" class="form-control">
      </div>
      <div class="col-12">
        <label class="form-label">Abstrak</label>
        <textarea name="abstrak" rows="4" class="form-control"></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Dokumentasi (maks 5 gambar)</label>
        <input type="file" name="images[]" accept="image/*" class="form-control" multiple>
      </div>
    </div>
    <div class="mt-3 text-end">
      <button class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
@endsection
