@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Kelola Publikasi Saya</h4>

  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif
  @if(session('err'))
    <div class="alert alert-danger">{{ session('err') }}</div>
  @endif

  <div class="card mb-3 p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Pencarian</label>
        <input type="text" name="q" class="form-control"
               value="{{ $filterQ }}" placeholder="Cari judul / jurnal / penerbit">
      </div>
      <div class="col-md-2">
        <label class="form-label">Jenis</label>
        <select name="jenis" class="form-select">
          <option value="">Semua</option>
          <option value="jurnal" {{ $filterJenis === 'jurnal' ? 'selected' : '' }}>Jurnal</option>
          <option value="prosiding" {{ $filterJenis === 'prosiding' ? 'selected' : '' }}>Prosiding</option>
          <option value="buku" {{ $filterJenis === 'buku' ? 'selected' : '' }}>Buku</option>
          <option value="lainnya" {{ $filterJenis === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Tahun</label>
        <select name="tahun" class="form-select">
          <option value="">Semua</option>
          @foreach($tahunOptions as $tahun)
            <option value="{{ $tahun }}" {{ (string)$filterTahun === (string)$tahun ? 'selected' : '' }}>
              {{ $tahun }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Status Validasi</label>
        <select name="status" class="form-select">
          <option value="">Semua</option>
          <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft</option>
          <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
          <option value="revision_requested" {{ $filterStatus === 'revision_requested' ? 'selected' : '' }}>Perlu Revisi</option>
          <option value="approved" {{ $filterStatus === 'approved' ? 'selected' : '' }}>Disetujui</option>
          <option value="rejected" {{ $filterStatus === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        <a href="{{ route('publications.my') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>

  <div class="mb-3 text-end">
    <a href="{{ route('publications.create') }}" class="btn btn-success">
      Tambah Publikasi
    </a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0 align-middle">
        <thead>
        <tr>
            <th style="width: 40px;">#</th>
            <th>Judul</th>
            <th style="width: 130px;">Jenis</th>
            <th style="width: 80px;">Tahun</th>
            <th>Jurnal / Penerbit</th>
            <th style="width: 140px;">Status Validasi</th>
            <th style="width: 140px;">Aksi</th>
        </tr>
        </thead>

        <tbody>
                @forelse($publications as $p)
                @php
                    $status = $p->validation_status ?? 'draft';
                    [$badgeClass, $label] = match ($status) {
                        'approved'           => ['bg-success', 'Disetujui'],
                        'pending'            => ['bg-secondary', 'Pending'],
                        'revision_requested' => ['bg-warning text-dark', 'Perlu Revisi'],
                        'rejected'           => ['bg-danger', 'Ditolak'],
                        'draft'              => ['bg-light text-dark', 'Draft'],
                        default              => ['bg-light text-muted', ucfirst($status)],
                    };
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($publications->currentPage()-1)*$publications->perPage() }}</td>
                    <td>
                        <a href="{{ route('publications.show', $p) }}">
                        {{ $p->judul }}
                        </a>
                    </td>
                    <td>{{ $p->jenis ?? '-' }}</td>
                    <td>{{ $p->tahun ?? '-' }}</td>
                    <td>{{ $p->jurnal ?? '-' }}</td>

                    {{-- Kolom status validasi --}}
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                    </td>

                    {{-- Kolom aksi --}}
                    <td>
                        <div class="d-flex gap-1">
                        <a href="{{ route('publications.edit', $p) }}"
                            class="btn btn-sm btn-outline-warning"
                            @if($status === 'approved') disabled @endif>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('publications.destroy', $p) }}"
                                onsubmit="return confirm('Hapus publikasi ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                            Hapus
                            </button>
                        </form>
                        </div>
                    </td>
                    </tr>
                @empty
                    <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada publikasi.</td>
                </tr>
            @endforelse
        </tbody>
      </table>
    </div>
    @if($publications->hasPages())
      <div class="card-footer">
        {{ $publications->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
