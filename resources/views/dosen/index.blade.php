@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Header Biru --}}
    <div class="header-box text-center text-white fw-bold mb-4 py-3 rounded-4 shadow-lg">
        Daftar Dosen
    </div>

    {{-- 🔹 Search & Filter (lebih membulat) --}}
    <form action="{{ route('dosen.index') }}" method="GET"
          class="d-flex align-items-center gap-3 mb-4"
          style="margin-left: 0; background: none; box-shadow: none;">

        {{-- Search Bar --}}
        <div style="max-width: 400px; flex-shrink: 0;">
            <div class="input-group rounded-pill overflow-hidden shadow-sm">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="form-control ps-3 border-0"
                    placeholder="Cari nama atau NIDN/NIP dosen...">
                <button class="btn btn-primary rounded-end-pill px-4" type="submit">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </div>

        {{-- Filter Urut Nama --}}
        <select name="sort" class="form-select rounded-pill shadow-sm" style="width: 180px;" onchange="this.form.submit()">
            <option value="">Urut Nama</option>
            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A - Z</option>
            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z - A</option>
        </select>

        {{-- Filter Status --}}
        <select name="status" class="form-select rounded-pill shadow-sm" style="width: 180px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
        </select>
    </form>

    {{-- Tabel Dosen --}}
    <div class="table-responsive">
        <table class="table align-middle table-borderless shadow-sm">
            <thead>
                <tr class="table-header">
                    <th>Nama</th>
                    <th>NIDN / NIP</th>
                    <th>Status Aktivitas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dosens as $dosen)
                    <tr class="table-row">
                        <td class="fw-bold text-uppercase">{{ $dosen->nama }}</td>
                        <td>{{ $dosen->nidn ?? $dosen->nip ?? '-' }}</td>

                        <td>
                            @if ($dosen->status_aktivitas == 'Aktif')
                                <span class="badge bg-success px-3 py-2 rounded-pill">Aktif</span>
                            @elseif ($dosen->status_aktivitas == 'Tidak Aktif')
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">Tidak Aktif</span>
                            @elseif ($dosen->status_aktivitas == 'Cuti')
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Cuti</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('dosen.show', $dosen->id) }}" class="text-detail fw-semibold">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Tidak ada dosen ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== CSS ===== --}}
<style>
body {
    background: #f4f7fc;
    font-family: 'Poppins', sans-serif;
}

/* Header */
.header-box {
    background: #002b6d;
    font-size: 1.4rem;
    letter-spacing: 0.5px;
    box-shadow: 0 6px 20px rgba(0, 43, 109, 0.4);
    text-transform: uppercase;
    border-radius: 16px;
}

/* Table */
.table {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    border-collapse: separate;
    border-spacing: 0;
}
.table-header th {
    background: #e8f0ff;
    color: #000;
    font-weight: 700;
    padding: 16px 20px;
    text-align: left;
}
.table-row td {
    padding: 16px 20px;
    font-size: 15px;
}
.table-row:hover {
    background: #f0f6ff;
    transform: scale(1.005);
}

/* Rounded Form Elements */
.form-control,
.form-select,
.input-group .btn {
    border-radius: 50px !important;
}

/* Focus efek */
.form-control:focus {
    box-shadow: none !important;
    border: 1px solid #0050a0;
}

/* Hover dropdown */
.form-select:hover {
    background-color: #f8faff;
}

/* Tombol Search */
.btn-primary {
    background: linear-gradient(135deg, #0050a0, #002b6d);
    border: none;
    transition: 0.2s ease-in-out;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #003f8a, #001f52);
    transform: scale(1.03);
}
</style>
@endsection
