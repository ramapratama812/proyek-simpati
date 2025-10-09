@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="h6 text-muted">Total Kegiatan (Penelitian+Pengabdian)</div>
          <div class="display-6">{{ $projectCount }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="h6 text-muted">Total Publikasi</div>
          <div class="display-6">{{ $publicationCount }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-3">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Kegiatan Terbaru</div>
        <ul class="list-group list-group-flush">
          @forelse($projects as $p)
            <li class="list-group-item">
              <a href="{{ route('projects.show',$p) }}" class="fw-semibold">{{ $p->judul }}</a>
              <span class="badge text-bg-secondary">{{ ucfirst($p->jenis) }}</span>
            </li>
          @empty
            <li class="list-group-item">Belum ada data.</li>
          @endforelse
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Publikasi Terbaru</div>
        <ul class="list-group list-group-flush">
          @forelse($pubs as $x)
            <li class="list-group-item">
              <a href="{{ route('publications.show',$x) }}" class="fw-semibold">{{ $x->judul }}</a>
              @if($x->tahun)<span class="badge text-bg-light">{{ $x->tahun }}</span>@endif
            </li>
          @empty
            <li class="list-group-item">Belum ada data.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
