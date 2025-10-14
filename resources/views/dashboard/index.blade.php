@extends('layouts.app')

@section('content')
<div class="container">

  {{-- Notifikasi sekali tampil --}}
  @if(isset($notifications) && $notifications->count())
    @foreach($notifications as $n)
        <div class="alert alert-info alert-dismissible fade show mt-2" role="alert">
        {{ $n->message }}
        @if($n->project_id)
            <a href="{{ route('projects.show', $n->project_id) }}" class="ms-2">Lihat</a>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
  @endif

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="h6 text-muted">Total Kegiatan (Penelitian + Pengabdian)</div>
          <div class="display-6">{{ $projectCount }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="h6 text-muted">Total Publikasi</div>
          <div class="display-6">{{ $pubCount ?? $publicationCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
        <div class="card">
            <div class="card-header">Daftar Kegiatan</div>
            <ul class="list-group list-group-flush">
            @forelse($projects as $project)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                <a href="{{ route('projects.show', $project) }}" class="fw-semibold">{{ $project->judul }}</a>
                <span class="badge text-bg-light ms-2">{{ ucfirst($project->jenis) }}</span>
                @if($isAdmin && $project->ketua)
                <span class="badge bg-secondary ms-2">Ketua: {{ $project->ketua->name }}</span>
                @endif
                </div>
            </li>
            @empty
            <li class="list-group-item">Belum ada data.</li>
            @endforelse
            </ul>
        </div>
        </div>
    <div class="col-md-6">

    <div class="col-md-12">
      <div class="card">
        <div class="card-header">Daftar Publikasi</div>
        <ul class="list-group list-group-flush">
          @forelse($pubs as $x)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <a href="{{ route('publications.show',$x) }}" class="fw-semibold">{{ $x->judul }}</a>
                @if($isAdmin && isset($x->owner))
                  <span class="badge bg-secondary ms-2">Oleh: {{ $x->owner->name }}</span>
                @endif
              </div>
              @if($x->tahun)
                <span class="badge text-bg-light">{{ $x->tahun }}</span>
              @endif
            </li>
          @empty
            <li class="list-group-item">Belum ada data.</li>
          @endforelse
        </ul>
      </div>
    </div>

</div>

<div>
    <hr>
</div>

{{-- Kegiatan yang saya ikuti sebagai anggota --}}
<div>
  <div class="col-12">
    <div class="card">
      <div class="card-header">Kegiatan yang Saya Ikuti</div>
      <ul class="list-group list-group-flush">
        @forelse($memberProjects as $x)
          <li class="list-group-item">
            <a href="{{ route('projects.show',$x) }}" class="fw-semibold">{{ $x->judul }}</a>
            @if($x->ketua)<span class="badge text-bg-light ms-2">Ketua: {{ $x->ketua->name }}</span>@endif
            <span class="badge bg-secondary ms-2 text-capitalize">{{ $x->jenis }}</span>
          </li>
        @empty
          <li class="list-group-item">Belum ada data.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

@endsection
