@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Validasi Publikasi</h4>

  @php
      $status = $publication->validation_status ?? 'draft';
      $badgeClass = match ($status) {
          'approved'           => 'bg-success',
          'pending'            => 'bg-secondary',
          'revision_requested' => 'bg-warning text-dark',
          'rejected'           => 'bg-danger',
          'draft'              => 'bg-light text-dark',
          default              => 'bg-light text-muted',
      };
  @endphp

  <div class="card mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
          <h5 class="mb-1">{{ $publication->judul }}</h5>
          <div class="small mb-1">
            <strong>{{ $publication->jurnal ?? '—' }}</strong>

            @php
                $info = [];
                if($publication->volume) $info[] = "Vol {$publication->volume}";
                if($publication->nomor) $info[] = "No {$publication->nomor}";
                if($publication->tahun) $info[] = "({$publication->tahun})";
                $display = implode(' ', $info);
            @endphp

            @if($display)
              <strong>— {{ $display }}</strong>
            @endif
          </div>

          @if($publication->doi)
            <div class="small mb-1">
                <strong>DOI:</strong>
                <a href="https://doi.org/{{ $publication->doi }}" target="_blank" rel="noopener noreferrer">
                    {{ $publication->doi }}
                </a>
            </div>
          @endif

          @if($publication->owner)
            <div class="small mb-1">
              Pengunggah: {{ $publication->owner->name }}
            </div>
          @endif

          @if(isset($publication->penulis) && is_array($publication->penulis))
            <div class="small mb-1">
                Penulis: {{ implode(', ', $publication->penulis) }}
            </div>
          @endif

          @if($publication->jenis)
            <div class="small mb-1">
                Jenis: {{ $publication->jenis }}
            </div>
          @endif

          @if($publication->jumlah_halaman)
            <div class="small mb-1">
                Jumlah Halaman: {{ $publication->jumlah_halaman }}
            </div>
          @endif

          @if($publication->abstrak)
            <div class="small mb-3">
                <strong>Abstrak:</strong>
                <p>{{ $publication->abstrak }}</p>
            </div>
          @endif

        </div>
        <span class="badge {{ $badgeClass }} px-3 py-2">
          {{ str_replace('_', ' ', ucfirst($status)) }}
        </span>
      </div>

      @if($publication->validation_note)
        <div class="alert alert-info mt-2 mb-0">
          <strong>Catatan terakhir admin:</strong><br>
          {{ $publication->validation_note }}
        </div>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      Form Validasi
    </div>
    <div class="card-body">
      @if($publication->validation_status !== 'pending')
        <div class="alert alert-info">
          Publikasi ini sudah memiliki status
          <strong>{{ str_replace('_', ' ', $publication->validation_status) }}</strong>.
          Dosen harus mengajukan ulang jika ingin dilakukan validasi lagi.
        </div>
        <a href="{{ route('admin.publications.validation.index') }}" class="btn btn-outline-secondary">
          Kembali ke daftar
        </a>
      @else
        <form method="POST" action="{{ route('admin.publications.validation.update', $publication) }}">
          @csrf

          <div class="mb-3">
            <label class="form-label d-block">Pilih Status</label>

            <div class="btn-group" role="group" aria-label="Status publikasi">
              {{-- Approved --}}
              <input type="radio" class="btn-check" name="validation_status" id="status-approved"
                     autocomplete="off" value="approved"
                     @checked(old('validation_status', $publication->validation_status) === 'approved')>
              <label class="btn btn-outline-success" for="status-approved">Approved</label>

              {{-- Revision requested --}}
              <input type="radio" class="btn-check" name="validation_status" id="status-revision"
                     autocomplete="off" value="revision_requested"
                     @checked(old('validation_status', $publication->validation_status) === 'revision_requested')>
              <label class="btn btn-outline-warning" for="status-revision">Perlu Revisi</label>

              {{-- Rejected --}}
              <input type="radio" class="btn-check" name="validation_status" id="status-rejected"
                     autocomplete="off" value="rejected"
                     @checked(old('validation_status', $publication->validation_status) === 'rejected')>
              <label class="btn btn-outline-danger" for="status-rejected">Rejected</label>
            </div>

            @error('validation_status')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan Admin (opsional)</label>
            <textarea name="validation_note" class="form-control" rows="3">{{ old('validation_note', $publication->validation_note) }}</textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              Simpan Validasi
            </button>
            <a href="{{ route('admin.publications.validation.index') }}" class="btn btn-outline-secondary">
              Kembali
            </a>
          </div>
        </form>
      @endif
    </div>
  </div>
</div>
@endsection
