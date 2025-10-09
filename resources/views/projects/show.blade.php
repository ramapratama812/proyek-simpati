@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>{{ $project->judul }}</h4>
    <span class="badge text-bg-secondary">{{ ucfirst($project->jenis) }}</span>
  </div>
  <div class="row g-3">
    <div class="col-md-8">
      <div class="card mb-3">
        <div class="card-body">
          <div class="mb-2"><strong>Skema:</strong> {{ $project->skema ?? '—' }}</div>
          <div class="mb-2"><strong>Bidang Ilmu:</strong> {{ $project->bidang_ilmu ?? '—' }}</div>
          <div class="mb-2"><strong>Periode:</strong> {{ $project->mulai?->format('d M Y') ?? '—' }} — {{ $project->selesai?->format('d M Y') ?? '—' }}</div>
          <div class="mb-2"><strong>Sumber Dana:</strong> {{ $project->sumber_dana ?? '—' }} | <strong>Biaya:</strong> {{ $project->biaya ?? '—' }}</div>
          <div class="mb-2"><strong>Abstrak:</strong><br>{{ $project->abstrak ?? '—' }}</div>
        </div>
      </div>
      <div class="row g-2">
        @foreach($project->images as $img)
          <div class="col-md-4">
            <img src="{{ asset('storage/'.$img->path) }}" class="img-fluid rounded border">
          </div>
        @endforeach
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">Aksi</div>
        <div class="card-body">
          <a href="{{ route('publications.index') }}" class="btn btn-outline-primary w-100 mb-2">Lihat Publikasi</a>
          <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-100">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
