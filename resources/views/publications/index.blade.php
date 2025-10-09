@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Publikasi</h4>
    <a class="btn btn-primary" href="{{ route('publications.create') }}">Tambah</a>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-2" method="POST" action="{{ route('import.crossref') }}">
        @csrf
        <div class="col-md-8">
          <input name="doi" class="form-control" placeholder="Masukkan DOI untuk impor dari Crossref" required>
        </div>
        <div class="col-md-4">
          <button class="btn btn-outline-primary w-100">Impor DOI</button>
        </div>
      </form>
      <hr>
      <form class="row g-2" method="POST" action="{{ route('import.bibtex') }}" enctype="multipart/form-data">
        @csrf
        <div class="col-md-8">
          <input type="file" name="file" class="form-control" accept=".bib,.txt" required>
        </div>
        <div class="col-md-4">
          <button class="btn btn-outline-secondary w-100">Impor BibTeX</button>
        </div>
      </form>
      <hr>
      <form class="row g-2" method="POST" action="{{ route('import.oai') }}">
        @csrf
        <div class="col-md-5">
          <input name="base_url" class="form-control" placeholder="OAI Base URL (mis. https://journal.xxx/oai)" required>
        </div>
        <div class="col-md-3">
          <input name="set" class="form-control" placeholder="Set (opsional)">
        </div>
        <div class="col-md-2">
          <input name="author_like" class="form-control" placeholder="Filter penulis (ops)">
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-success w-100">Harvest OAI</button>
        </div>
      </form>
    </div>
  </div>

  <div class="list-group">
    @forelse($rows as $r)
      <a class="list-group-item list-group-item-action" href="{{ route('publications.show',$r) }}">
        <div class="d-flex justify-content-between">
          <div>
            <div class="fw-semibold">{{ $r->judul }}</div>
            <small class="text-muted">{{ $r->jurnal ?? '—' }}</small>
          </div>
          <span class="badge text-bg-light">{{ $r->tahun ?? '—' }}</span>
        </div>
      </a>
    @empty
      <div class="list-group-item">Belum ada data.</div>
    @endforelse
  </div>

  <div class="mt-3">{{ $rows->withQueryString()->links() }}</div>
</div>
@endsection
