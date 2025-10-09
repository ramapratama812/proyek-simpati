@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Daftar Kegiatan</h4>
    <a class="btn btn-primary" href="{{ route('projects.create') }}">Tambah</a>
  </div>
  <form class="row g-2 mb-3">
    <div class="col-auto">
      <input name="q" value="{{ $q }}" class="form-control" placeholder="Cari judul...">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-secondary">Cari</button>
    </div>
  </form>
  <div class="list-group">
    @forelse($rows as $r)
      <a class="list-group-item list-group-item-action" href="{{ route('projects.show',$r) }}">
        <div class="d-flex justify-content-between">
          <div>
            <div class="fw-semibold">{{ $r->judul }}</div>
            <small class="text-muted">{{ ucfirst($r->jenis) }} · {{ $r->skema ?? '—' }}</small>
          </div>
          <span class="badge text-bg-secondary">{{ $r->mulai?->format('Y') ?? '—' }}</span>
        </div>
      </a>
    @empty
      <div class="list-group-item">Belum ada data.</div>
    @endforelse
  </div>
  <div class="mt-3">{{ $rows->withQueryString()->links() }}</div>
</div>
@endsection
