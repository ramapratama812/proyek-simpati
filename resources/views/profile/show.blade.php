@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">

        {{-- ================= HEADER ================= --}}
        <div class="card-header text-center text-white py-4 header-dosen position-relative">
            
            {{-- ❗ LOGIKA AVATAR/FOTO (UKURAN 120PX) ❗ --}}
            @php
                // Menggunakan $dosen->nama atau fallback ke $user->name jika diperlukan
                $initial = strtoupper(substr($dosen->nama ?? $user->name, 0, 1));
            @endphp
            
            <div class="dosen-avatar-in-header mb-3 mx-auto">
                @if ($dosen->foto) 
                    <img src="{{ asset('storage/' . $dosen->foto) }}" 
                         alt="{{ $dosen->nama ?? $user->name }}" 
                         class="dosen-profile-photo-in-header rounded-circle">
                @else
                    <div class="dosen-profile-initials-in-header rounded-circle d-flex align-items-center justify-content-center fw-bold">
                        {{ $initial }}
                    </div>
                @endif
            </div>
            {{-- ❗ AKHIR LOGIKA AVATAR ❗ --}}

            <h2 class="fw-bolder mb-0 text-uppercase">{{ $dosen->nama ?? $user->name }}</h2>
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
                        <p class="text-dark fw-bold mb-0 text-uppercase">{{ $dosen->nama ?? $user->name }}</p>
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

            </div>
            
            <hr class="my-5">

            {{-- ================= LINK PDDIKTI ================= --}}
            <h4 class="fw-bold mb-3 text-primary-dark">Akses Profil Resmi PDDIKTI</h4>
            <div class="d-flex align-items-center mb-4 pddikti-box">
                @if($dosen->link_pddikti)
                    <a href="{{ $dosen->link_pddikti }}" target="_blank"
                       class="btn btn-primary rounded-pill px-4 py-2 me-3 pddikti-btn btn-smooth-action">
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
                <a href="{{ route('dosen.index') }}" class="btn btn-secondary rounded-pill px-4 btn-smooth-action btn-back">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>

                <div class="d-flex gap-2">
                    {{-- TOMBOL EDIT PROFIL DENGAN HOVER SMOOTH --}}
                    <a href="{{ route('profile.edit') }}"
                       class="btn text-white fw-semibold px-4 py-2 rounded-pill btn-edit-profile btn-smooth-action">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profil
                    </a>

                    {{-- TOMBOL HAPUS AKUN DENGAN HOVER SMOOTH --}}
                    <form action="{{ route('profile.destroy') }}" method="POST"
                          onsubmit="return confirm('Yakin ingin hapus akun?')" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-outline-danger rounded-pill px-4 py-2 btn-smooth-action btn-delete-account">
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
    --light-blue: #e8f0ff;
}

/* 1. Global Transisi untuk Elemen Interaktif */
.btn-smooth-action, .bio-box, .pddikti-box a.btn {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); /* Custom bezier for smooth effect */
}

.text-sinta {
    color: var(--sinta-color) !important;
}
.text-primary-color {
    color: var(--primary-color) !important;
}
.text-primary-dark { /* Menggunakan kode warna #001F4D */
    color: var(--primary-dark) !important;
}


/* 1. Header & Card Styling */
.header-dosen {
    background: linear-gradient(135deg, var(--primary-dark) 0%, #0a3d62 100%);
    box-shadow: 0 5px 15px rgba(0, 31, 77, 0.4);
    border-bottom: 5px solid var(--warning-color);
    padding-top: 2rem !important;
    padding-bottom: 2rem !important;
}

/* ❗ KOREKSI UKURAN AVATAR/INISIAL DI HEADER ❗ */
/* Ukuran dikembalikan ke 120px */
.dosen-avatar-in-header {
    display: inline-block; 
}

.dosen-profile-photo-in-header {
    width: 120px; 
    height: 120px;
    object-fit: cover;
    border: 5px solid white; 
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); 
}

.dosen-profile-initials-in-header {
    width: 120px; 
    height: 120px;
    background-color: var(--light-blue);
    color: var(--primary-dark);
    font-size: 3rem; 
    line-height: 1;
    border: 5px solid white; 
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); 
    display: inline-flex; 
    align-items: center; 
    justify-content: center;
}
/* ❗ AKHIR KOREKSI UKURAN AVATAR ❗ */


/* 2. Biodata Boxes (Hover Smooth & Spacing Jelas) */
.bio-box {
    background: #ffffff;
    border: 1px solid var(--border-color);
    padding: 15px 20px; 
    border-radius: 14px;
    box-shadow: 0 4px 8px rgba(0,0,0,.04);
    
    display: flex;
    flex-direction: column; 
    align-items: flex-start;
}

.bio-box i {
    align-self: flex-start;
    margin-bottom: 5px; 
}

.bio-box:hover {
    transform: translateY(-3px); 
    box-shadow: 0 8px 20px rgba(0,0,0,.15); 
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

/* PDDikti Box */
.pddikti-box {
    background: #e8f0ff;
    border: 1px dashed var(--primary-color);
    padding: 20px;
    border-radius: 12px;
}

/* ==========================================================
   4. PERBAIKAN TOMBOL AKSI (HOVER LEBIH SMOOTH DAN MENARIK)
   ========================================================== */

/* Tombol Edit Profile (Menonjol) */
.btn-edit-profile {
    background-color: var(--primary-color);
    color: white !important; /* Pastikan teks putih */
    border: 1px solid var(--primary-dark);
    box-shadow: 0 4px 10px rgba(0, 80, 160, 0.2);
}

.btn-edit-profile:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

/* Tombol Hapus Akun (Gaya Outline dengan Hover Kuat) */
.btn-delete-account {
    border-color: var(--danger-color);
    color: var(--danger-color) !important;
}

.btn-delete-account:hover {
    background-color: var(--danger-color);
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
}

/* Tombol Kembali (Secondary) */
.btn-back:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}


/* Scroll List Container */
.scroll-list-container {
    max-height: 350px;
    overflow-y: auto;
    padding-right: 15px;
}
.scroll-list-container::-webkit-scrollbar { width: 6px; }
.scroll-list-container::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 10px; }
.scroll-list-container::-webkit-scrollbar-track { background-color: #f1f1f1; border-radius: 10px; }

/* Custom Tabs Styling */
.custom-tabs .nav-link {
    color: var(--primary-dark);
    font-weight: 600;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 10px 15px;
}
.custom-tabs .nav-link:hover {
    color: var(--primary-color);
    border-bottom-color: #d0e6ff;
    background-color: #f5f5f5;
}
.custom-tabs .nav-link.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
    background-color: transparent;
}
</style>
{{-- Memastikan Bootstrap JS untuk Tabs berfungsi (asumsi layout.app sudah memuatnya) --}}
<script>
    // Hanya untuk memastikan tab aktif pada refresh, jika diperlukan
    document.addEventListener('DOMContentLoaded', function () {
        const triggerTabList = document.querySelectorAll('#dosenTab button')
        triggerTabList.forEach(triggerEl => {
            const tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', event => {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });
</script>
@endsection
