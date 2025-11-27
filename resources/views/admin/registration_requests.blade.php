@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Permohonan Pendaftaran Akun</h4>

    @if(session('ok'))    <div class="alert alert-success">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    {{-- Filter --}}
    <form method="GET"
          action="{{ route('admin.registration-requests.index') }}"
          class="row g-2 align-items-end mb-3">

        <div class="col-md-3">
            <label class="form-label mb-1">Filter Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="all"     {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved"{{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected"{{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label mb-1">Urutkan</label>
            <select name="sort" class="form-select form-select-sm">
                <option value="latest"   {{ ($sort ?? 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest"   {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="name_asc" {{ ($sort ?? '') === 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                <option value="name_desc"{{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm mt-3">
                Terapkan Filter
            </button>

            <a href="{{ route('admin.registration-requests.index') }}"
               class="btn btn-outline-secondary btn-sm mt-3">
                Reset
            </a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="white-space:nowrap;">NIM / NIDN-NIP</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th style="white-space:nowrap;">Diajukan</th>
                    <th style="min-width:260px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($requests as $req)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $req->name }}</td>
                    <td>{{ $req->email }}</td>
                    <td>{{ $req->identity }}</td>
                    <td>{{ ucfirst($req->role) }}</td>
                    <td>{{ strtoupper($req->status) }}</td>
                    <td style="max-width:280px;">
                        <div class="small text-muted">
                            {{ $req->note ?: '-' }}
                        </div>
                    </td>
                    <td>{{ $req->created_at->format('d-m-Y H:i') }}</td>

                    <td>
                        @if($req->status === 'pending')
                            <div class="d-flex flex-column gap-2">

                                {{-- Form SETUJUI --}}
                                <form action="{{ route('admin.registration-requests.approve', $req) }}"
                                      method="POST"
                                      class="border rounded p-2 bg-light">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm mb-1 small">
                                            Catatan (opsional, saat menyetujui)
                                        </label>
                                        <textarea name="note"
                                                  rows="2"
                                                  class="form-control form-control-sm"
                                                  placeholder="Contoh: disetujui oleh admin"></textarea>
                                    </div>
                                    <button type="submit"
                                            class="btn btn-success btn-sm w-100"
                                            onclick="return confirm('Setujui permohonan ini?')">
                                        Setujui
                                    </button>
                                </form>

                                {{-- Form TOLAK --}}
                                <form action="{{ route('admin.registration-requests.reject', $req) }}"
                                      method="POST"
                                      class="border rounded p-2 bg-light">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm mb-1 small">
                                            Catatan (opsional, saat menolak)
                                        </label>
                                        <textarea name="note"
                                                  rows="2"
                                                  class="form-control form-control-sm"
                                                  placeholder="Contoh: NIM/NIDN belum sesuai"></textarea>
                                    </div>
                                    <button type="submit"
                                            class="btn btn-danger btn-sm w-100"
                                            onclick="return confirm('Tolak permohonan ini?')">
                                        Tolak
                                    </button>
                                </form>

                            </div>
                        @else
                            <span class="text-muted">Tidak ada aksi</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada permohonan.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $requests->links() }}
</div>
@endsection
