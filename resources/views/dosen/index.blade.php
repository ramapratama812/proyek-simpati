@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">

    {{-- Header Utama dan Stat Cards DIGABUNGKAN DALAM SATU CARD --}}
    <div class="card header-main-card mb-5 shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0 bg-primary-gradient text-white position-relative">
            
            {{-- Kontainer Judul di bagian atas Card --}}
            <div class="header-title-area py-4 px-4">
                <h1 class="fs-4 mb-1 fw-bold text-uppercase position-relative z-1"><i class="bi bi-mortarboard-fill me-2"></i> DATA DOSEN TEKNOLOGI INFORMASI</h1>
                <p class="mb-0 small opacity-75 position-relative z-1 header-subtitle-text">Manajemen dan informasi seluruh data dosen.</p>
                
                {{-- Elemen dekorasi --}}
                <div class="decor-circle large"></div>
                <div class="decor-circle small"></div>
            </div>
            
            {{-- 📊 Stat Cards (Diintegrasikan di bagian bawah Header Card) --}}
            <div class="row m-0 stat-row-integrated px-4 pb-4">
                
                {{-- Total Dosen Terdaftar --}}
                <div class="col-md-4 col-sm-6 p-2">
                    <div class="stat-box-integrated shadow-sm text-primary-dark rounded-3 p-3 text-center">
                        <i class="bi bi-people-fill fs-3 mb-1 text-primary-dark"></i>
                        <h5 class="fw-bold mb-0">{{ $dosens->total() ?? 0 }}</h5>
                        <small class="text-muted">Total Dosen</small>
                    </div>
                </div>

                {{-- Dosen Aktif Mengajar --}}
                <div class="col-md-4 col-sm-6 p-2">
                    <div class="stat-box-integrated shadow-sm text-primary-dark rounded-3 p-3 text-center">
                        <i class="bi bi-person-check-fill fs-3 mb-1 text-success"></i>
                        <h5 class="fw-bold mb-0">{{ $totalDosenAktif ?? 0 }}</h5>
                        <small class="text-muted">Dosen Aktif</small>
                    </div>
                </div>

                {{-- Dosen Sedang Cuti/Tugas --}}
                <div class="col-md-4 col-sm-6 p-2">
                    <div class="stat-box-integrated shadow-sm text-primary-dark rounded-3 p-3 text-center">
                        <i class="bi bi-person-lines-fill fs-3 mb-1 text-warning"></i>
                        <h5 class="fw-bold mb-0">{{ $totalDosenCuti ?? 0 }}</h5>
                        <small class="text-muted">Dosen Cuti</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 🔹 Search & Filter Card (Tetap Modern) --}}
    <div class="card p-4 mb-4 shadow-md border-0 rounded-4 filter-card">
        <h6 class="fw-bold text-primary-dark mb-3 border-bottom pb-2"><i class="bi bi-funnel-fill me-2"></i> PENCARIAN & FILTER DATA</h6>
        <form action="{{ route('dosen.index') }}" method="GET"
              class="d-flex flex-wrap align-items-end gap-3">

            {{-- Search Bar --}}
            <div class="flex-grow-1" style="min-width: 250px; max-width: 450px;">
                <label for="search_input" class="form-label mb-1 text-muted small">Cari Nama atau NIDN/NIP</label>
                <div class="input-group search-input-group rounded-pill overflow-hidden border border-2 border-light-subtle">
                    <input
                        id="search_input"
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        class="form-control ps-4 border-0"
                        placeholder="Masukkan kata kunci...">
                    <button class="btn btn-primary search-btn px-4" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            {{-- Filter Urut Nama --}}
            <div style="width: 180px;">
                <label for="sort_select" class="form-label mb-1 text-muted small">Urutkan Berdasarkan Nama</label>
                <select id="sort_select" name="sort" class="form-select filter-select shadow-sm" onchange="this.form.submit()">
                    <option value="">Default (A - Z)</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A - Z</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z - A</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div style="width: 180px;">
                <label for="status_select" class="form-label mb-1 text-muted small">Filter Status Aktivitas</label>
                <select id="status_select" name="status" class="form-select filter-select shadow-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
            </div>

            {{-- Reset Button --}}
            @if(request()->hasAny(['search', 'sort', 'status']))
                <div class="ms-md-auto">
                    <a href="{{ route('dosen.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- Tabel Dosen Card (Tetap Modern) --}}
    <div class="card table-container shadow-lg rounded-4 border-0">
        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-primary-dark mb-1">Daftar Dosen</h5>
                <p class="text-muted small mb-0">Menampilkan {{ $dosens->count() }} hasil dari total {{ $dosens->total() ?? 0 }} data.</p>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="table-header">
                            <th>Nama</th>
                            <th>NIDN / NIP</th>
                            <th>Status Aktivitas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dosens as $dosen)
                            <tr class="table-row">
                                <td class="fw-bold text-uppercase text-primary-dark">{{ $dosen->nama }}</td>
                                <td>{{ $dosen->nidn ?? $dosen->nip ?? '-' }}</td>

                                <td>
                                    @if ($dosen->status_aktivitas == 'Aktif')
                                        <span class="badge status-aktif"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                    @elseif ($dosen->status_aktivitas == 'Tidak Aktif')
                                        <span class="badge status-tidak-aktif"><i class="bi bi-x-circle-fill me-1"></i> Non Aktif</span>
                                    @elseif ($dosen->status_aktivitas == 'Cuti')
                                        <span class="badge status-cuti"><i class="bi bi-pause-circle-fill me-1"></i> Cuti</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">-</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('dosen.show', $dosen->id) }}" class="btn btn-sm btn-detail fw-semibold">
                                        Detail <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-x-octagon-fill fs-5 me-2"></i> Data dosen tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if (method_exists($dosens, 'links'))
            <div class="card-footer bg-white border-0 pt-3 pb-4 d-flex justify-content-center">
                {{ $dosens->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ===== CSS Kustom Baru (Final) ===== --}}
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

.text-primary-dark {
    color: var(--blue-dark) !important;
}
.text-success { color: var(--success-color) !important; }
.text-warning { color: var(--warning-color) !important; }

/* 1. Header Card UTAMA (Digabungkan) */
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
    text-align: left;
    padding-bottom: 20px !important; /* Padding cukup untuk judul */
}
.header-title-area h1 {
    font-size: 1.5rem !important;
}
.header-subtitle-text {
    font-size: 0.75rem !important;
}

/* Dekorasi di Header */
.decor-circle {
    position: absolute;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    z-index: 0;
}
.decor-circle.large {
    width: 250px;
    height: 250px;
    top: -100px;
    right: -50px;
    opacity: 0.6;
}
.decor-circle.small {
    width: 100px;
    height: 100px;
    bottom: -30px;
    left: 20%;
    opacity: 0.8;
}

/* 2. Stat Card Row (Diintegrasikan di bawah Judul) */
.stat-row-integrated {
    /* Margin bawah card agar ada jarak ke konten di bawahnya */
    margin-bottom: 0 !important;
    
    /* Mengatur padding dan margin untuk meniru tampilan card menjorok */
    padding-left: 10px; 
    padding-right: 10px; 
    padding-bottom: 20px;
    position: relative;
    z-index: 10;
}

.stat-box-integrated {
    background: #fff;
    border: 1px solid #e0e5ee;
    border-radius: 12px;
    padding: 15px;
    height: 100%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important;
    
    /* Tampilan Rata Kiri di dalam card */
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
}
.stat-box-integrated .fs-5 { /* Icon */
    margin-bottom: 5px;
}
.stat-box-integrated h5 {
    font-size: 1.1rem;
    line-height: 1.2;
}

/* Filter Card Styling */
.filter-card {
    border: 1px solid #dcdcdc;
    background: #fff;
}
.filter-card .border-bottom {
    border-color: #f0f4f8 !important;
}

/* Form Elements */
.search-input-group .form-control {
    padding-left: 20px;
}
.search-input-group .search-btn {
    background: var(--blue-medium);
    border: none;
    transition: background-color 0.2s ease-in-out, box-shadow 0.2s;
    width: 60px;
}
.search-input-group .search-btn:hover {
    background: var(--blue-dark);
    box-shadow: 0 0 10px rgba(0, 80, 160, 0.4);
}

.filter-select {
    border-radius: 50px !important;
    border: 1px solid #ccd9f3;
}
.filter-select:focus {
    box-shadow: 0 0 0 0.1rem var(--blue-light) !important;
}

/* Tabel Kontainer */
.table-container {
    background: #fff;
}
.table-container .card-header {
    border-bottom: 1px solid #f0f4f8;
}
.table-container .card-footer {
    border-top: 1px solid #f0f4f8;
}

/* Tabel Header */
.table-header th {
    background: var(--blue-light);
    color: var(--blue-dark);
    font-weight: 700;
    padding: 16px 20px;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

/* Tabel Baris */
.table-row td {
    padding: 18px 20px;
    font-size: 0.95rem;
    border-bottom: 1px solid #f0f4f8;
    transition: background-color 0.3s ease;
}

.table-row:hover {
    background: #f0f6ff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

/* Status Badges */
.badge {
    padding: 0.6em 1em;
    font-size: 0.75em;
    font-weight: 600;
    border-radius: 50px;
    text-transform: uppercase;
}
.status-aktif { background-color: var(--success-color); color: #fff; }
.status-tidak-aktif { background-color: #6c757d; color: #fff; }
.status-cuti { background-color: var(--warning-color); color: #333; }

/* Tombol Detail */
.btn-detail {
    color: var(--blue-medium);
    background-color: var(--blue-light);
    border-radius: 12px;
    padding: 6px 15px;
    transition: all 0.2s ease-in-out;
    font-size: 0.85em;
}

.btn-detail:hover {
    color: #fff;
    background-color: var(--blue-medium);
    box-shadow: 0 4px 10px rgba(0, 80, 160, 0.3);
}
</style>
@endsection
