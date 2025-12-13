@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">

        {{-- ================= HEADER ================= --}}
        <div class="card-header text-center text-white py-4 header-dosen">
            <i class="bi bi-person-badge-fill display-5 mb-2 d-block"></i>
            <h2 class="fw-bolder mb-0 text-uppercase">{{ $dosen->nama ?? 'Nama Dosen' }}</h2>
            <p class="text-white-50 small mb-0">Profile Dosen | NIDN: {{ $dosen->nidn ?? '-' }}</p>
        </div>

        <div class="card-body px-4 px-md-5 py-5" style="background-color: #fbfbff;">

            {{-- NOTIFIKASI --}}
            @if(session('success'))
                <div class="alert alert-success text-center mb-5 rounded-3 shadow-sm py-2">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ================= SECTION UTAMA: DATA PERSONAL & KEPEGAWAIAN ================= --}}
            <h4 class="fw-bold mb-4 border-bottom pb-2 text-primary-dark">
                <i class="bi bi-info-circle-fill me-2 text-primary-dark"></i> Data Personal & Kepegawaian
            </h4>
            
            {{-- Menggunakan Row dan Col untuk Struktur 2 Kolom per Baris --}}
            <div class="row g-4 mb-4">
                
                {{-- Nama Lengkap (Kiri Atas) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-person-fill fs-4 me-3 text-primary-color"></i>
                        <p class="text-muted small mb-0">Nama Lengkap</p>
                        <p class="text-dark fw-bold mb-0 text-uppercase">{{ $dosen->nama ?? '-' }}</p>
                    </div>
                </div>

                {{-- Email (Kanan Atas) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-envelope-fill fs-4 me-3 text-primary-color"></i>
                        <p class="text-muted small mb-0">Email</p>
                        <p class="text-dark fw-semibold mb-0">{{ $dosen->email ?? $user->email ?? '-' }}</p>
                    </div>
                </div>

                {{-- NIDN / NIP (Kiri Kedua) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-qr-code-scan fs-4 me-3 text-primary-color"></i>
                        <p class="text-muted small mb-0">NIDN / NIP</p>
                        <p class="text-dark fw-semibold mb-0">{{ $dosen->nidn ?? $dosen->nip ?? '-' }}</p>
                    </div>
                </div>

                {{-- Nomor HP (Kanan Kedua) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-phone-fill fs-4 me-3 text-primary-color"></i>
                        <p class="text-muted small mb-0">Nomor HP</p>
                        <p class="text-dark fw-semibold mb-0">{{ $dosen->nomor_hp ?? '-' }}</p>
                    </div>
                </div>

                {{-- Pendidikan Terakhir (Kiri Ketiga) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-mortarboard-fill fs-4 me-3 text-primary-color"></i>
                        <p class="text-muted small mb-0">Pendidikan Terakhir</p>
                        <p class="text-dark fw-semibold mb-0">{{ $dosen->pendidikan_terakhir ?? '-' }}</p>
                    </div>
                </div>
                
                {{-- ID SINTA (Kanan Ketiga) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-journal-bookmark-fill fs-4 me-3 text-sinta"></i> 
                        <div>
                            <p class="text-muted small mb-0">ID SINTA</p>
                            <p class="text-dark fw-semibold mb-0">
                                {{ $dosen->id_sinta ?? $dosen->sinta_id ?? '-' }}
                            </p> 
                        </div>
                    </div>
                </div>

                {{-- Status Aktivitas (Kiri Keempat) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-lightning-charge-fill fs-4 me-3 text-primary-color"></i>
                        <div>
                            <p class="text-muted small mb-0">Status Aktivitas</p>
                            @php $status = $dosen->status_aktivitas ?? ''; @endphp
                            @if($status === 'Aktif')
                                <span class="badge status-aktif">Aktif</span>
                            @elseif($status === 'Cuti')
                                <span class="badge status-cuti">Cuti</span>
                            @elseif($status === 'Tidak Aktif')
                                <span class="badge status-tidak-aktif">Non Aktif</span>
                            @else
                                <span class="badge status-default">Belum Diatur</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Status Ikatan Kerja (Kanan Keempat) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-briefcase-fill fs-4 me-3 text-primary-color"></i>
                        <p class="text-muted small mb-0">Status Ikatan Kerja</p>
                        <p class="text-dark fw-semibold mb-0">{{ $dosen->status_ikatan_kerja ?? '-' }}</p>
                    </div>
                </div>
                
                {{-- Jenis Kelamin (Kiri Kelima) --}}
                <div class="col-md-6">
                    <div class="bio-box bio-box-smooth">
                        <i class="bi bi-gender-ambiguous fs-4 me-3 text-primary-color"></i>
                        <p class="text-muted small mb-0">Jenis Kelamin</p>
                        <p class="text-dark fw-semibold mb-0">{{ $dosen->jenis_kelamin ?? '-' }}</p>
                    </div>
                </div>

                {{-- PDDikti Link (Kanan Kelima - Dibuat terpisah di bawah) --}}
                {{-- Kosongkan slot ini di struktur 2 kolom jika PDDikti ditaruh di bawah --}}

            </div>
            
            <hr class="my-5">

            {{-- ================= LINK PDDIKTI (Sesuai Gambar 2) ================= --}}
            <h4 class="fw-bold mb-3 text-primary-dark">Akses Profil Resmi PDDIKTI</h4>
            <div class="d-flex align-items-center mb-4 pddikti-box">
                @if($dosen->link_pddikti)
                    <a href="{{ $dosen->link_pddikti }}" target="_blank"
                       class="btn btn-primary rounded-pill px-4 py-2 me-3 pddikti-btn">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Profil PDDIKTI
                    </a>

                    @php
                        $linkPendek = strlen($dosen->link_pddikti) > 50
                            ? substr($dosen->link_pddikti, 0, 50).'...'
                            : $dosen->link_pddikti;
                    @endphp

                    <span class="text-muted small d-none d-md-inline" title="{{ $dosen->link_pddikti }}">
                        Tautan: {{ $linkPendek }}
                    </span>
                @else
                    <p class="text-muted pt-2">Link profil PDDIKTI belum tersedia untuk dosen ini.</p>
                @endif
            </div>

            <hr class="my-5">


            {{-- ================= AKSI ================= --}}
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('dosen.index') }}" class="btn btn-secondary rounded-pill px-4 btn-smooth-action">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('profile.edit') }}"
                       class="btn text-dark fw-semibold px-4 py-2 rounded-pill btn-edit-profile btn-smooth-action">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profil
                    </a>

                    <form action="{{ route('profile.destroy') }}" method="POST"
                          onsubmit="return confirm('Yakin ingin hapus akun?')" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-outline-danger rounded-pill px-4 py-2 btn-smooth-action">
                            <i class="bi bi-trash-fill me-1"></i> Hapus Akun
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Palet Warna & Font */
:root {
    --primary-color: #0050a0;
    --primary-dark: #001F4D;
    --secondary-bg: #f7f9fc;
    --border-color: #e6e7ee;
    --success-color: #198754;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --sinta-color: #6c5ce7; 
    --pddikti-color: #00897b;
}

.text-primary-dark {
    color: var(--primary-dark) !important;
}
.text-primary-color {
    color: var(--primary-color) !important;
}
.text-sinta {
    color: var(--sinta-color) !important;
}
.text-pddikti {
    color: var(--pddikti-color) !important;
}

/* 1. Header & Card Styling (Gaya Modern) */
body {
    background: #f4f7fc; 
    font-family: 'Poppins', sans-serif;
}
.header-dosen {
    background: linear-gradient(135deg, var(--primary-dark) 0%, #0a3d62 100%);
    box-shadow: 0 5px 15px rgba(0, 31, 77, 0.4);
    border-bottom: 5px solid var(--warning-color);
}
.card {
    transition: box-shadow 0.3s ease;
}

/* 2. Biodata Boxes (Gaya Clean dan Smooth) */
.bio-box {
    /* Mengembalikan ke gaya single box per kolom */
    display: flex;
    flex-direction: column; 
    align-items: flex-start;
    
    background: #ffffff;
    border: 1px solid var(--border-color);
    padding: 15px 20px; /* Padding lebih besar */
    border-radius: 14px;
    box-shadow: 0 4px 8px rgba(0,0,0,.04);
}
/* Menyesuaikan ikon agar berada di atas, seperti gambar 1 */
.bio-box i {
    align-self: flex-start;
    margin-bottom: 5px; 
}

.bio-box-smooth {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.bio-box-smooth:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,.1);
}

/* Status Badges */
.badge {
    font-size: 0.8em;
    padding: 0.5em 0.8em;
    border-radius: 50px;
    font-weight: 600;
}
.status-aktif { background-color: var(--success-color); color: #fff; }
.status-cuti { background-color: var(--warning-color); color: #333; }
.status-tidak-aktif { background-color: var(--danger-color); color: #fff; }
.status-default { background-color: #ccc; color: #555; border: none; }


/* PDDikti Box (Sesuai Gambar 2 - Lebih menonjol) */
.pddikti-box {
    background: #e8f0ff;
    border: 1px dashed var(--primary-color);
    padding: 20px;
    border-radius: 12px;
    /* Memastikan tombol dan tautan sejajar */
    align-items: center; 
}
.pddikti-btn {
    transition: all 0.3s ease;
}

/* Tombol Aksi */
.btn-edit-profile {
    background-color: var(--primary-color);
    color: white !important;
    border: 1px solid var(--primary-dark);
}
.btn-smooth-action {
    transition: all 0.3s ease;
}
.btn-smooth-action:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    transform: translateY(-1px);
}
.btn-edit-profile:hover {
    background-color: var(--primary-dark);
}
</style>
@endsection