@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Header Biru --}}
    <div class="header-box text-center text-white fw-bold mb-4 py-3 rounded-4 shadow-lg">
       Daftar Mahasiswa
    </div>

    {{-- Search Bar --}}
    <form action="{{ route('mahasiswa.index') }}" method="GET"
          class="d-flex align-items-center gap-3 mb-4"
          style="margin-left: 0; background: none; box-shadow: none;">

        <div style="max-width: 400px; flex-shrink: 0;">
            <div class="input-group rounded-pill overflow-hidden shadow-sm">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="form-control ps-3 border-0"
                    placeholder="Cari nama atau NIM mahasiswa...">
                <button class="btn btn-primary rounded-end-pill px-4" type="submit">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </div>

    </form>

    {{-- Tabel Mahasiswa --}}
    <div class="table-responsive">
        <table class="table align-middle table-borderless shadow-sm">
            <thead>
                <tr class="table-header">
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Status Aktivitas</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mahasiswas as $mhs)
                    <tr class="table-row">
                        <td class="fw-bold text-uppercase">{{ $mhs->nama }}</td>
                        <td>{{ $mhs->nim }}</td>
                        <td>
                            @if ($mhs->status_aktivitas == 'Aktif')
                                <span class="badge bg-success px-3 py-2 rounded-pill">Aktif</span>
                            @elseif($mhs->status_aktivitas == 'Tidak Aktif')
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">Tidak Aktif</span>
                            @elseif ($mhs->status_aktivitas == 'Cuti')
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Cuti</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('mahasiswa.show', $mhs->id) }}" class="text-detail fw-semibold">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Tidak ada mahasiswa ditemukan.</td>
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

/* Search */
.input-group .btn {
    border-radius: 50px !important;
}
.form-control,
.form-select {
    border-radius: 50px !important;
}
.btn-primary {
    background: linear-gradient(135deg, #0050a0, #002b6d);
    border: none;
    transition: 0.2s ease-in-out;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #003f8a, #001f52);
    transform: scale(1.03);
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

/* Link Aksi */
.text-detail {
    color: #0050a0;
    text-decoration: none;
}
.text-detail:hover {
    color: #002b6d;
    text-decoration: underline;
}
</style>
@endsection
