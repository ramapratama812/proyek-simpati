@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>{{ $pub->judul }}</h4>
    @if($pub->tahun)<span class="badge text-bg-light">{{ $pub->tahun }}</span>@endif
  </div>
  <div class="card">
    <div class="card-body">
      <div class="mb-2"><strong>Jurnal:</strong> {{ $pub->jurnal ?? '—' }}</div>
      <div class="mb-2"><strong>Jenis:</strong> {{ $pub->jenis ?? '—' }}</div>
      <div class="mb-2"><strong>DOI:</strong> {{ $pub->doi ?? '—' }}</div>
      <div class="mb-2"><strong>Penulis:</strong> {{ $pub->penulis ? implode(', ', $pub->penulis) : '—' }}</div>
    </div>
  </div>
  <div class="mt-3">
    <a href="{{ route('publications.index') }}" class="btn btn-outline-secondary">Kembali</a>
  </div>
</div>
@endsection
