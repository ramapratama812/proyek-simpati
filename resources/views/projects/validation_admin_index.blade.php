@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Validasi Kegiatan (Admin)</h4>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
      <select name="status" class="form-select" onchange="this.form.submit()">
        @php
          $opts = [
            'pending'            => 'Menunggu Validasi',
            'revision_requested' => 'Perlu Revisi',
            'approved'           => 'Disetujui',
            'rejected'           => 'Ditolak',
            'draft'              => 'Draft',
          ];
        @endphp
        @foreach($opts as $val => $label)
          <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
    </div>
  </form>

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Judul</th>
            <th>Jenis</th>
            <th>Ketua</th>
            <th>Status Validasi</th>
            <th>Dibuat</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @forelse($projects as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->judul }}</td>
            <td class="text-capitalize">{{ $p->jenis }}</td>
            <td>{{ optional($p->ketua)->name }}</td>
            <td>
              @include('projects._validation_badge', ['project' => $p])
            </td>
            <td>{{ $p->created_at?->format('d-m-Y') }}</td>
            <td>
              <a href="{{ route('projects.validation.show', $p) }}" class="btn btn-sm btn-primary">
                Periksa
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-3">Tidak ada data.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $projects->links() }}
    </div>
  </div>
</div>
@endsection
