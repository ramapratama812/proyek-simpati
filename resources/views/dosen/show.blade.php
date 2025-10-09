@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">
        
        {{-- Header --}}
        <div class="card-header text-center text-white py-4"
            style="background-color: #001F4D;">
            <h2 class="fw-bold mb-0">Biodata Dosen</h2>
        </div>

        {{-- Isi --}}
        <div class="card-body px-5 py-5" style="background-color: #fbfbff;">
            <div class="row">
                {{-- Kolom kiri --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <p class="text-muted mb-1">Nama</p>
                        <p class="text-dark mb-0">{{ $dosen->nama }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Email</p>
                        <p class="text-dark mb-0">{{ $dosen->email }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Nomor HP</p>
                        <p class="text-dark mb-0">{{ $dosen->nomor_hp ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">NIDN / NIP</p>
                        <p class="text-dark mb-0">{{ $dosen->nidn ?? $dosen->nip ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Perguruan Tinggi</p>
                        <p class="text-dark mb-0">{{ $dosen->perguruan_tinggi }}</p>
                    </div>
                </div>

                {{-- Kolom kanan --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <p class="text-muted mb-1">Program Studi</p>
                        <p class="text-dark mb-0">{{ $dosen->program_studi }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Status Ikatan Kerja</p>
                        <p class="text-dark mb-0">{{ $dosen->status_ikatan_kerja }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Jenis Kelamin</p>
                        <p class="text-dark mb-0">{{ $dosen->jenis_kelamin }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Pendidikan Terakhir</p>
                        <p class="text-dark mb-0">{{ $dosen->pendidikan_terakhir }}</p>
                    </div>

                    {{-- Status Aktivitas --}}
                    <div class="mb-3">
                        <p class="text-muted mb-1">Status Aktivitas</p>
                        @if(strtolower($dosen->status_aktivitas) == 'aktif')
                            <span class="status-badge bg-success">Aktif</span>
                        @elseif(strtolower($dosen->status_aktivitas) == 'cuti')
                            <span class="status-badge bg-warning text-dark">Cuti</span>
                        @else
                            <span class="status-badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tombol kembali --}}
            <div class="mt-4 d-flex justify-content-start">
                <a href="{{ route('dosen.index') }}" class="btn-kembali">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</div>

{{-- STYLE TAMBAHAN --}}
<style>
    body {
        background-color: #f3f4ff;
        font-family: 'Poppins', sans-serif;
    }

    .card {
        border-radius: 1.5rem !important;
    }

    .card-header {
        border: none;
    }

    .text-muted {
        font-size: 0.9rem;
        color: #6b7280 !important;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .bg-success { background-color: #15803d !important; }
    .bg-warning { background-color: #facc15 !important; color: #000; }
    .bg-secondary { background-color: #6b7280 !important; }

    /* Tombol kembali putih dengan teks biru, hover biru muda */
    .btn-kembali {
        display: inline-block;
        background-color: #ffffff;
        color: #007bff;
        font-weight: 600;
        padding: 10px 26px;
        border-radius: 9999px;
        text-decoration: none;
        border: 2px solid #007bff;
        transition: all 0.3s ease;
        box-shadow: 0 3px 8px rgba(0, 123, 255, 0.15);
    }

    .btn-kembali:hover {
        background-color: #007bff;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        text-decoration: none;
    }
</style>
@endsection
