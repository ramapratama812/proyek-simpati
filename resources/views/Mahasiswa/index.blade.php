@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Header Biru Tua dengan Gradasi --}}
    <div class="header-box text-center text-white fw-bold mb-4 py-3 rounded-4 shadow-lg">
       Daftar Mahasiswa
    </div>

    {{-- Search Bar (tanpa ikon kiri) --}}
    <form action="{{ route('mahasiswa.index') }}" method="GET" class="mb-4 mx-auto" style="max-width: 480px;">
        <div class="input-group search-box shadow-lg">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}" 
                class="form-control border-0 bg-white shadow-none ps-4"
                placeholder="Cari nama atau NIM mahasiswa...">
            <button class="btn btn-search" type="submit">
                <i class="bi bi-search me-1"></i> Cari
            </button>
        </div>
    </form>

    {{-- Tabel Mahasiswa --}}
    <div class="table-responsive">
        <table class="table align-middle table-borderless shadow-sm">
            <thead>
                <tr class="table-header">
                    <th>Nama</th>
                    <th>Perguruan Tinggi</th>
                    <th>Program Studi</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mahasiswas as $mhs)
                    <tr class="table-row">
                        <td class="fw-bold text-uppercase">{{ $mhs->nama }}</td>
                        <td>{{ $mhs->perguruan_tinggi }}</td>
                        <td>{{ $mhs->program_studi }}</td>
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

/* ===== Header ===== */
.header-box {
    background: linear-gradient(90deg, #003b95, #007bff);
    font-size: 1.4rem;
    letter-spacing: 0.5px;
    box-shadow: 0 6px 20px rgba(0, 91, 255, 0.25);
    text-transform: uppercase;
}

/* ===== Search Box (tanpa ikon kiri) ===== */
.search-box {
    border-radius: 50px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}
.search-box:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
.search-box input {
    font-size: 15px;
    padding: 12px 16px;
    border-radius: 50px 0 0 50px;
}
.btn-search {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    font-weight: 600;
    border: none;
    padding: 0 1.4rem;
    border-radius: 0 50px 50px 0;
    transition: all 0.3s ease;
}
.btn-search:hover {
    background: linear-gradient(135deg, #0056b3, #004095);
    box-shadow: 0 0 12px rgba(0, 91, 255, 0.4);
}

/* ===== Table Style ===== */
.table {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.table-header th {
    background: #e8f0ff;
    color: #000;
    font-weight: 700;
    padding: 16px 20px;
    border: none;
    vertical-align: middle;
    text-align: left;
}

.table-row td {
    padding: 16px 20px;
    vertical-align: middle;
    border: none;
    font-size: 15px;
}

.table-row {
    background: #fff;
    transition: all 0.3s ease;
    border-radius: 12px;
    border-bottom: 1px solid #eef3ff;
}
.table-row:hover {
    background: #f0f6ff;
    transform: scale(1.005);
    box-shadow: 0 4px 12px rgba(0, 91, 255, 0.1);
}

/* ===== Link Detail ===== */
.text-detail {
    color: #0069d9;
    text-decoration: none;
    transition: all 0.3s ease;
}
.text-detail:hover {
    color: #004fa3;
    text-decoration: underline;
}

/* ===== Responsif ===== */
@media (max-width: 768px) {
    .table thead {
        display: none;
    }
    .table tbody tr {
        display: block;
        margin-bottom: 1rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        padding: 1rem;
    }
    .table tbody td {
        display: flex;
        justify-content: space-between;
        border: none;
        padding: 6px 0;
    }
    .table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6c757d;
    }
    .text-end {
        text-align: right !important;
    }
}
</style>
@endsection
