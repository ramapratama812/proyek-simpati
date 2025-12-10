@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Publikasi untuk: "{{ $project->judul }}"</h4>

  @php
      $userId           = auth()->id();
      $isKetua          = $userId && $userId == $project->ketua_id;
      $validationStatus = $project->validation_status;

      // Ketua boleh melepas publikasi hanya jika
      // kegiatan BELUM diajukan atau sudah tidak dalam status diajukan:
      // -> tidak boleh saat 'pending' (diajukan) dan 'approved'
      $canDetach = $isKetua && !in_array($validationStatus, ['pending', 'approved'], true);

      // Pengelolaan (tambah/kaitkan) publikasi:
      // masih boleh selama kegiatan belum disetujui final oleh admin
      $canManage = $isKetua && $validationStatus !== 'approved';
  @endphp

  <div class="card p-3 mb-3">
    <h5 class="mb-3">Publikasi terkait</h5>

    {{-- Daftar selalu tampil untuk semua user --}}
    @if($related->isEmpty())
      <div class="alert alert-secondary mb-4">Belum ada publikasi terkait.</div>
    @else
      <ul class="list-group mb-4">
        @foreach($related as $pub)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <a href="{{ route('publications.show', $pub) }}">{{ $pub->judul }}</a>
              @if(!empty($pub->jenis))
                <div class="small text-muted">{{ $pub->jenis }}</div>
              @endif
            </div>

            <div class="d-flex align-items-center gap-2">
              <span class="text-muted">{{ $pub->tahun ?? '' }}</span>

              {{-- Tombol LEPAS hanya untuk ketua, dan hanya jika
                   kegiatan belum diajukan / belum approved --}}
              @if($canDetach)
                <form method="POST"
                      action="{{ route('projects.publications.destroy', [$project, $pub]) }}"
                      onsubmit="return confirm('Lepas publikasi ini dari kegiatan?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    Lepas
                  </button>
                </form>
              @endif
            </div>
          </li>
        @endforeach
      </ul>
    @endif

    {{-- Bagian untuk mengelola publikasi --}}

    {{-- Form hanya tampil untuk ketua yang boleh mengelola --}}
    @if($canManage)
      <div class="row g-4">
        <h5>Kelola Publikasi</h5>

        {{-- Opsi 1: Unggah publikasi baru --}}
        <div class="col-lg-6">
          <div class="card h-100 shadow-sm border-light-subtle">
            <div class="card-header bg-white py-3">
              <h5 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-cloud-arrow-up-fill me-2 text-primary"></i>
                Unggah Publikasi Baru
              </h5>
            </div>
            <div class="card-body d-flex flex-column">
              <p class="text-muted">
                Publikasi baru yang Anda unggah akan otomatis dikaitkan dengan kegiatan ini.
              </p>
              <div class="mt-auto">
                <form method="GET" action="{{ route('publications.create') }}">
                  <input type="hidden" name="project_id" value="{{ $project->id }}">
                  <button class="btn btn-primary w-100 fw-semibold py-2">
                    <i class="bi bi-plus-circle me-2"></i>
                    Buat & Unggah Publikasi
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

        {{-- Opsi 2: Kaitkan publikasi yang sudah ada --}}
        <div class="col-lg-6">
          <div class="card h-100 shadow-sm border-light-subtle">
            <div class="card-header bg-white py-3">
              <h5 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-link-45deg me-2 text-success"></i>
                Kaitkan dari Publikasi Saya
              </h5>
            </div>
            <div class="card-body d-flex flex-column">
              <p class="text-muted">
                Pilih dari daftar publikasi yang sudah pernah Anda buat sebelumnya.
              </p>
              <form method="POST" action="{{ route('projects.publications.attach', $project) }}" class="mt-auto">
                @csrf
                <label for="publication-select" class="form-label visually-hidden">Pilih Publikasi</label>
                <div class="input-group">
                  <select name="publication_id" id="publication-select" class="form-select" aria-label="Pilih publikasi yang ada">
                    <option selected disabled>Pilih publikasi...</option>
                    @forelse($myPubs as $p)
                      <option value="{{ $p->id }}">{{ \Illuminate\Support\Str::limit($p->judul, 70) }}</option>
                    @empty
                      <option disabled>Anda belum memiliki publikasi</option>
                    @endforelse
                  </select>
                  <button class="btn btn-success fw-semibold" type="submit" @if($myPubs->isEmpty()) disabled @endif>
                    <i class="bi bi-arrow-right-circle me-1"></i> Kaitkan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    @else
      {{-- Pesan peringatan untuk pengguna atau ketua yang sudah tidak bisa kelola --}}
      <div class="alert alert-info d-flex align-items-center shadow-sm" role="alert">
        <i class="bi bi-info-circle-fill me-3" style="font-size: 1.5rem;"></i>
        <div>
          Anda hanya bisa <strong>melihat</strong> publikasi yang sudah terkait
          @if($isKetua && $validationStatus === 'pending')
            karena kegiatan sudah diajukan untuk validasi.
          @elseif($isKetua && $validationStatus === 'approved')
            karena kegiatan sudah disetujui oleh admin.
          @endif
        </div>
      </div>
    @endif

  </div>

  <div class="mt-3">
    <a href="{{ route('projects.show',$project) }}" class="btn btn-outline-secondary">Kembali</a>
  </div>
</div>
@endsection
