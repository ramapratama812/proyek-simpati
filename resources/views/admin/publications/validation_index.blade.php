@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Validasi Publikasi</h4>

  {{-- Filter & Urutan --}}
  <form method="GET" class="row g-2 align-items-end mb-3">
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="">Semua</option>
        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
        <option value="approved" @selected(request('status') === 'approved')>Approved</option>
        <option value="revision_requested" @selected(request('status') === 'revision_requested')>Revision requested</option>
        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Urutkan</label>
      <select name="sort" class="form-select">
        <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru (tanggal dibuat)</option>
        <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
        <option value="title_asc" @selected(request('sort') === 'title_asc')>Judul A → Z</option>
        <option value="title_desc" @selected(request('sort') === 'title_desc')>Judul Z → A</option>
      </select>
    </div>

    <div class="col-md-2">
      <label class="form-label">&nbsp;</label>
      <button class="btn btn-primary w-100">Terapkan</button>
    </div>
  </form>

  <div class="card">
    <div class="list-group list-group-flush">
      @forelse($pubs as $p)
        @php
          $status = $p->validation_status ?? 'draft';
          $badgeClass = match ($status) {
              'approved'           => 'bg-success',
              'pending'            => 'bg-secondary',
              'revision_requested' => 'bg-warning text-dark',
              'rejected'           => 'bg-danger',
              'draft'              => 'bg-light text-dark',
              default              => 'bg-light text-muted',
          };
        @endphp

        <a href="{{ route('admin.publications.validation.show', $p) }}"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold text-uppercase mb-1">
              {{ $p->judul }}
            </div>
            <div class="text-muted small">
              {{ $p->jurnal ?? '—' }}
              @if($p->tahun)
                — {{ $p->tahun }}
              @endif
            </div>
            @if($p->owner)
              <div class="small text-muted mt-1">
                Pengunggah: {{ $p->owner->name }}
              </div>
            @endif
          </div>

          <div class="text-end">
            <span class="badge {{ $badgeClass }} mb-2">
              {{ str_replace('_', ' ', ucfirst($status)) }}
            </span>
            <div class="small text-muted">
              {{ $p->created_at?->format('d M Y') }}
            </div>
          </div>
        </a>
      @empty
        <div class="list-group-item text-center text-muted">
          Tidak ada publikasi untuk divalidasi.
        </div>
      @endforelse
    </div>

    <div class="card-footer">
      {{ $pubs->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
