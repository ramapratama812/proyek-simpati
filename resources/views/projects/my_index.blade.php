@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Kelola Kegiatan Saya</h4>

  <div class="card mb-3 p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Pencarian</label>
        <input type="text" name="q" class="form-control"
               value="{{ $filterQ }}" placeholder="Cari judul / skema / bidang ilmu">
      </div>
      <div class="col-md-2">
        <label class="form-label">Jenis</label>
        <select name="jenis" class="form-select">
          <option value="">Semua</option>
          <option value="penelitian" {{ $filterJenis === 'penelitian' ? 'selected' : '' }}>Penelitian</option>
          <option value="pengabdian" {{ $filterJenis === 'pengabdian' ? 'selected' : '' }}>Pengabdian</option>
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
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        <a href="{{ route('projects.my') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0 align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Judul</th>
            <th>Jenis</th>
            <th>Tahun</th>
            <th>Status Validasi</th>
            <th>Peran Saya</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($projects as $p)
          @php
            $role = '';
            if($p->ketua_id == auth()->id()) {
              $role = 'Ketua';
            } elseif($p->created_by == auth()->id()) {
              $role = 'Pengusul';
            } elseif($p->members->contains('id', auth()->id())) {
              $role = 'Anggota';
            }
          @endphp
          <tr>
            <td>{{ $loop->iteration + ($projects->currentPage()-1)*$projects->perPage() }}</td>
            <td>
              <a href="{{ route('projects.show', $p) }}">{{ $p->judul }}</a>
              <div class="small text-muted">
                Skema: {{ $p->skema ?? '-' }}
              </div>
            </td>
            <td class="text-capitalize">{{ $p->jenis }}</td>
            <td>{{ $p->tahun_pelaksanaan ?? $p->tahun_usulan ?? '-' }}</td>
            <td>@include('projects._validation_badge', ['project' => $p])</td>
            <td>{{ $role }}</td>
            <td>
              <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('projects.show', $p) }}" class="btn btn-outline-primary">Detail</a>
                @if($p->ketua_id == auth()->id() && $p->validation_status !== 'approved')
                  <a href="{{ route('projects.edit', $p) }}" class="btn btn-outline-warning">Edit</a>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-3">Belum ada kegiatan.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if($projects->hasPages())
      <div class="card-footer">
        {{ $projects->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
