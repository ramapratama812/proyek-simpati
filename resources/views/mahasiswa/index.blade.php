@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ================= 1. HEADER CARD (GAYA DOSEN) ================= --}}
    <div class="card header-main-card mb-5 shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0 bg-primary-gradient text-white position-relative">
            
            {{-- Kontainer Judul --}}
            <div class="header-title-area py-4 px-4 d-flex justify-content-between align-items-center flex-wrap">
                <div class="position-relative z-1">
                    <h1 class="fs-4 mb-1 fw-bold text-uppercase">
                        <i class="bi bi-mortarboard-fill me-2"></i> DATA MAHASISWA
                    </h1>
                    <p class="mb-0 small opacity-75 header-subtitle-text">
                        Manajemen dan informasi seluruh data mahasiswa.
                    </p>
                </div>

                {{-- Tombol Tambah (Khusus Admin) --}}
                @if(Auth::user()->role === 'admin')
                    <div class="position-relative z-1 mt-2 mt-md-0">
                        <a href="{{ route('mahasiswa.create') }}" class="btn btn-light text-primary fw-bold px-4 rounded-pill shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Data
                        </a>
                    </div>
                @endif
                
                {{-- Elemen Dekorasi --}}
                <div class="decor-circle large"></div>
                <div class="decor-circle small"></div>
            </div>
            
            {{-- 📊 Stat Cards (Diintegrasikan di dalam Header) --}}
            <div class="row m-0 stat-row-integrated px-4 pb-4">
                
                {{-- Total Mahasiswa --}}
                <div class="col-md-4 col-sm-6 p-2">
                    <div class="stat-box-integrated hover-smooth-shadow shadow-sm text-primary-dark rounded-3 p-3 text-center">
                        <i class="bi bi-people-fill fs-3 mb-1 text-primary-dark"></i>
                        <h5 class="fw-bold mb-0">{{ $totalMahasiswa ?? 0 }}</h5>
                        <small class="text-muted">Total Mahasiswa</small>
                    </div>
                </div>

                {{-- Mahasiswa Aktif --}}
                <div class="col-md-4 col-sm-6 p-2">
                    <div class="stat-box-integrated hover-smooth-shadow shadow-sm text-primary-dark rounded-3 p-3 text-center">
                        <i class="bi bi-person-check-fill fs-3 mb-1 text-success"></i>
                        <h5 class="fw-bold mb-0">{{ $mahasiswaAktif ?? 0 }}</h5>
                        <small class="text-muted">Mahasiswa Aktif</small>
                    </div>
                </div>

                {{-- Mahasiswa Cuti --}}
                <div class="col-md-4 col-sm-6 p-2">
                    <div class="stat-box-integrated hover-smooth-shadow shadow-sm text-primary-dark rounded-3 p-3 text-center">
                        <i class="bi bi-person-x-fill fs-3 mb-1 text-warning"></i>
                        <h5 class="fw-bold mb-0">{{ $mahasiswaCuti ?? 0 }}</h5>
                        <small class="text-muted">Mahasiswa Cuti</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ================= 2. PENCARIAN DATA (FILTER DIHILANGKAN) ================= --}}
    <div class="card p-4 mb-4 shadow-md border-0 rounded-4 filter-card">
        <h6 class="fw-bold text-primary-dark mb-3 border-bottom pb-2">
            <i class="bi bi-search me-2"></i> PENCARIAN DATA
        </h6>

        <form action="{{ route('mahasiswa.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3">

            {{-- Search Bar (Full Width) --}}
            <div class="flex-grow-1">
                <label class="form-label mb-1 text-muted small">Cari Nama atau NIM</label>
                <div class="input-group search-input-group rounded-pill overflow-hidden border border-2 border-light-subtle">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control ps-4 border-0" 
                           placeholder="Masukkan kata kunci...">
                    <button class="btn btn-primary search-btn px-4" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            {{-- Reset Button (Hanya muncul jika ada pencarian) --}}
            @if(request()->has('search'))
                <div>
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- ================= 3. TABEL DATA ================= --}}
    <div class="card table-container shadow-lg rounded-4 border-0">
        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-primary-dark mb-1">Daftar Mahasiswa</h5>
                <p class="text-muted small mb-0">
                    Menampilkan {{ $mahasiswas->count() }} hasil dari total {{ $mahasiswas->total() ?? 0 }} data.
                </p>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="table-header">
                            <th style="width: 50px;"></th> {{-- Avatar --}}
                            <th>NAMA</th>
                            <th>NIM</th>
                            <th>STATUS AKTIVITAS</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $mhs)
                            <tr class="table-row">
                                {{-- Kolom Avatar --}}
                                <td class="ps-4">
                                    @if ($mhs->user && $mhs->user->foto) 
                                        <img src="{{ asset($mhs->user->foto) }}" 
                                             alt="{{ $mhs->nama }}" 
                                             class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ddd;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                             style="width: 40px; height: 40px; font-size: 1.1rem; background-color: var(--blue-light); color: var(--blue-medium);">
                                            {{ strtoupper(substr($mhs->nama, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Nama --}}
                                <td class="fw-bold text-uppercase text-primary-dark">
                                    {{ $mhs->nama }}
                                </td>

                                {{-- NIM --}}
                                <td>{{ $mhs->nim }}</td>

                                {{-- Status --}}
                                <td>
                                    @if ($mhs->status_aktivitas == 'Aktif')
                                        <span class="badge status-aktif"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                    @elseif ($mhs->status_aktivitas == 'Cuti')
                                        <span class="badge status-cuti"><i class="bi bi-pause-circle-fill me-1"></i> Cuti</span>
                                    @elseif ($mhs->status_aktivitas == 'Lulus')
                                        <span class="badge bg-info text-white"><i class="bi bi-mortarboard-fill me-1"></i> Lulus</span>
                                    @else
                                        <span class="badge status-tidak-aktif"><i class="bi bi-x-circle-fill me-1"></i> {{ $mhs->status_aktivitas ?? 'Non Aktif' }}</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center pe-4">
                                    <a href="{{ route('mahasiswa.show', $mhs->id) }}" class="btn btn-sm btn-detail fw-semibold">
                                        Detail <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-x-octagon-fill fs-5 me-2"></i> Data mahasiswa tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($mahasiswas->hasPages())
            <div class="card-footer bg-white border-0 pt-3 pb-4 d-flex justify-content-center">
                {{ $mahasiswas->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ===== CSS SAMA PERSIS DENGAN DOSEN ===== --}}
<style>
/* Font dan Background */
body {
    background: #f4f7fc;
    font-family: 'Poppins', sans-serif;
}

/* Palet Warna */
:root {
    --blue-dark: #002b6d;
    --blue-medium: #0050a0;
    --blue-light: #e8f0ff;
    --success-color: #28a745;
    --warning-color: #ffc107;
}

.text-primary-dark { color: var(--blue-dark) !important; }
.text-success { color: var(--success-color) !important; }
.text-warning { color: var(--warning-color) !important; }

/* 1. Header Card UTAMA */
.header-main-card {
    border: none;
    background: var(--blue-dark);
    box-shadow: 0 10px 30px rgba(0, 43, 109, 0.6); 
}

.bg-primary-gradient {
    background: linear-gradient(135deg, #003a8f, var(--blue-dark));
}

.header-title-area {
    position: relative;
    z-index: 10;
    padding-bottom: 20px !important;
}
.header-title-area h1 { font-size: 1.5rem !important; }
.header-subtitle-text { font-size: 0.75rem !important; }

/* Dekorasi di Header */
.decor-circle {
    position: absolute;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    z-index: 0;
}
.decor-circle.large { width: 250px; height: 250px; top: -100px; right: -50px; opacity: 0.6; }
.decor-circle.small { width: 100px; height: 100px; bottom: -30px; left: 20%; opacity: 0.8; }

/* 2. Stat Card Row (Integrated) */
.stat-row-integrated {
    margin-bottom: 0 !important;
    padding-left: 10px; 
    padding-right: 10px; 
    padding-bottom: 20px;
    position: relative;
    z-index: 10;
}

/* Stat Box Styling */
.stat-box-integrated {
    background: #fff;
    border: 1px solid #e0e5ee;
    border-radius: 12px;
    padding: 15px;
    height: 100%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
}
.stat-box-integrated:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 43, 109, 0.2) !important;
}
.stat-box-integrated .fs-3 { margin-bottom: 5px; }
.stat-box-integrated h5 { font-size: 1.1rem; line-height: 1.2; }

/* Filter Card Styling */
.filter-card { border: 1px solid #dcdcdc; background: #fff; }
.filter-card .border-bottom { border-color: #f0f4f8 !important; }

/* Form Elements */
.search-input-group .form-control { padding-left: 20px; }
.search-input-group .search-btn {
    background: var(--blue-medium); border: none;
    transition: background-color 0.2s ease-in-out, box-shadow 0.2s; width: 60px;
}
.search-input-group .search-btn:hover {
    background: var(--blue-dark); box-shadow: 0 0 10px rgba(0, 80, 160, 0.4);
}

/* Tabel Styling */
.table-container { background: #fff; }
.table-container .card-header { border-bottom: 1px solid #f0f4f8; }
.table-container .card-footer { border-top: 1px solid #f0f4f8; }

.table-header th {
    background: var(--blue-light); color: var(--blue-dark);
    font-weight: 700; padding: 16px 20px;
    text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;
}

.table-row td { padding: 18px 20px; font-size: 0.95rem; border-bottom: 1px solid #f0f4f8; }
.table-row { transition: background-color 0.3s ease, transform 0.1s ease; }
.table-row:hover {
    background: #f0f6ff; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    transform: translateX(2px); cursor: pointer;
}

/* Badges */
.badge { padding: 0.6em 1em; font-size: 0.75em; font-weight: 600; border-radius: 50px; text-transform: uppercase; }
.status-aktif { background-color: var(--success-color); color: #fff; }
.status-tidak-aktif { background-color: #6c757d; color: #fff; }
.status-cuti { background-color: var(--warning-color); color: #333; }

/* Tombol Detail */
.btn-detail {
    color: var(--blue-medium); background-color: var(--blue-light);
    border-radius: 12px; padding: 6px 15px;
    transition: all 0.3s ease-in-out; font-size: 0.85em;
}
.btn-detail:hover {
    color: #fff; background-color: var(--blue-medium);
    box-shadow: 0 4px 10px rgba(0, 80, 160, 0.3); transform: translateY(-1px);
}
</style>
@endsection