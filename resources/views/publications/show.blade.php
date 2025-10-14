@extends('layouts.app')

@section('content')
<div class="container">
  {{-- Header judul + tahun --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $pub->judul }}</h4>
    @if($pub->tahun)
      <span class="badge text-bg-light">{{ $pub->tahun }}</span>
    @endif
  </div>

  {{-- Hak edit/hapus hanya untuk owner (atau admin jika diizinkan) --}}
    @php
    $user = auth()->user();
    $isAdmin = strtolower($user->role ?? '') === 'admin';
    $canManage = \Illuminate\Support\Facades\Schema::hasColumn('publications','owner_id')
        ? ($isAdmin || $pub->owner_id === ($user->id ?? null))
        : $isAdmin;
    @endphp

    @if($canManage)
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('publications.edit', $pub) }}" class="btn btn-warning">Edit</a>
        <form method="POST" action="{{ route('publications.destroy', $pub) }}"
            onsubmit="return confirm('Hapus publikasi ini? Tindakan tidak bisa dibatalkan.');">
        @csrf @method('DELETE')
        <button class="btn btn-danger">Hapus</button>
        </form>
    </div>
    @endif

  {{-- Rincian publikasi --}}
  <div class="card">
    <div class="card-body">
      <div class="mb-2"><strong>Jurnal:</strong> {{ $pub->jurnal ?? '—' }}</div>
      <div class="mb-2"><strong>Jenis:</strong> {{ $pub->jenis ?? '—' }}</div>
      <div class="mb-2"><strong>DOI:</strong> {{ $pub->doi ?? '—' }}</div>

      @if(isset($pub->penulis) && is_array($pub->penulis))
        <div class="mb-2"><strong>Penulis:</strong> {{ implode(', ', $pub->penulis) }}</div>
      @else
        <div class="mb-2"><strong>Penulis:</strong> —</div>
      @endif
    </div>
  </div>

  <div class="mt-3">
    <a href="{{ route('publications.index') }}" class="btn btn-outline-secondary">Kembali</a>
  </div>
</div>
@endsection
