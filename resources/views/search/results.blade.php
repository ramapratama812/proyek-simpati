@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">{{ $title ?? 'Hasil' }}</h4>

  @if(isset($dosen) && $dosen->count())
    <div class="card mb-3"><div class="card-header">Dosen</div>
      <ul class="list-group list-group-flush">
        @foreach($dosen as $u)
          <li class="list-group-item">{{ $u->name }} ({{ $u->username }})</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(isset($mhs) && $mhs->count())
    <div class="card mb-3"><div class="card-header">Mahasiswa</div>
      <ul class="list-group list-group-flush">
        @foreach($mhs as $u)
          <li class="list-group-item">{{ $u->name }} ({{ $u->username }})</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(isset($proj) && $proj->count())
    <div class="card mb-3"><div class="card-header">Kegiatan</div>
      <ul class="list-group list-group-flush">
        @foreach($proj as $p)
          <li class="list-group-item">{{ $p->judul }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(isset($pubs) && $pubs->count())
    <div class="card mb-3"><div class="card-header">Publikasi</div>
      <ul class="list-group list-group-flush">
        @foreach($pubs as $p)
          <li class="list-group-item">{{ $p->judul }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</div>
@endsection
