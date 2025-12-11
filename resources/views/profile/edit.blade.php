@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Card Kontainer Utama --}}
    <div class="card border-0 shadow-lg rounded-4 mx-auto form-main-card-vertical">

        {{-- Header Form Utama --}}
        <header class="text-center pt-4 pb-4 mb-4 header-simpati-style">
            <i class="bi bi-person-circle mb-2 header-icon-lg-white"></i>
            <h3 class="fw-bold mb-1 text-white">Edit Profil Dosen</h3>
            <p class="text-light form-subtitle">
                Perbarui informasi pribadi dan akademik Anda.
            </p>
        </header>

        {{-- Form Edit Profil --}}
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Menggunakan g-4 untuk jarak vertikal section --}}
            <div class="row g-4 px-4 pb-0"> 
                
                {{-- === SECTION 1: DATA PERSONAL (FULL WIDTH) === --}}
                <div class="col-12 pt-4"> 
                    <h4 class="fw-bold mb-4 pb-2 section-title-custom">
                        <i class="bi bi-person me-2 title-icon"></i> Data Personal
                    </h4>

                    {{-- Menggunakan g-4 untuk jarak antar input yang lega --}}
                    <div class="row g-4"> 
                        <div class="col-md-6">
                            {{-- Nama Lengkap --}}
                            <div class="form-item">
                                <label for="name" class="form-label-custom"><i class="bi bi-person-vcard me-2"></i> Nama Lengkap</label>
                                <input type="text" id="name" name="name" class="form-control input-fancy"
                                    value="{{ old('name', $user->name) }}" required placeholder="Masukkan Nama Lengkap Anda">
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Email (Non-editable) --}}
                            <div class="form-item">
                                <label for="email" class="form-label-custom"><i class="bi bi-envelope me-2"></i> Email (Akun)</label>
                                <input type="email" id="email" class="form-control input-fancy bg-light"
                                    value="{{ $user->email }}" disabled>
                                <div class="form-text text-danger">Email tidak dapat diubah di halaman ini.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            {{-- Nomor HP --}}
                            <div class="form-item">
                                <label for="nomor_hp" class="form-label-custom"><i class="bi bi-telephone me-2"></i> Nomor HP</label>
                                <input type="text" id="nomor_hp" name="nomor_hp" class="form-control input-fancy"
                                    value="{{ old('nomor_hp', $dosen->nomor_hp ?? '') }}"
                                    placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Jenis Kelamin --}}
                            <div class="form-item">
                                <label for="jenis_kelamin" class="form-label-custom"><i class="bi bi-gender-ambiguous me-2"></i> Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select input-fancy">
                                    <option value="" disabled {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == '' ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- *** Akhir Section Data Personal *** --}}
                
                {{-- PENTING: Mengurangi margin-top di CSS untuk menaikkan posisi section berikutnya --}}
                <hr class="section-divider-tight"> 

                {{-- === SECTION 2: DATA AKADEMIK & LINK EKSTERNAL (FULL WIDTH) === --}}
                <div class="col-12 pt-4"> 
                    <h4 class="fw-bold mb-4 pb-2 section-title-custom">
                        <i class="bi bi-mortarboard me-2 title-icon"></i> Data Akademik
                    </h4>

                    {{-- Menggunakan g-2 untuk membuat jarak vertikal antar input lebih rapat --}}
                    <div class="row g-2"> 
                        <div class="col-md-6">
                            {{-- NIDN / NIP --}}
                            <div class="form-item">
                                <label for="nidn" class="form-label-custom"><i class="bi bi-card-heading me-2"></i> NIDN / NIP</label>
                                <input type="text" id="nidn" name="nidn" class="form-control input-fancy"
                                    value="{{ old('nidn', $dosen->nidn ?? '') }}" placeholder="Masukkan NIDN atau NIP">
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Status Ikatan Kerja --}}
                            <div class="form-item">
                                <label for="status_ikatan_kerja" class="form-label-custom"><i class="bi bi-briefcase me-2"></i> Status Ikatan Kerja</label>
                                <select id="status_ikatan_kerja" name="status_ikatan_kerja" class="form-select input-fancy">
                                    <option value="" disabled {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == '' ? 'selected' : '' }}>Pilih Status</option>
                                    <option value="Dosen Tetap" {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == 'Dosen Tetap' ? 'selected' : '' }}>Dosen Tetap</option>
                                    <option value="Dosen Tidak Tetap" {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == 'Dosen Tidak Tetap' ? 'selected' : '' }}>Dosen Tidak Tetap</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Pendidikan Terakhir --}}
                            <div class="form-item">
                                <label for="pendidikan_terakhir" class="form-label-custom"><i class="bi bi-book me-2"></i> Pendidikan Terakhir</label>
                                <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="form-select input-fancy">
                                    <option value="" disabled {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == '' ? 'selected' : '' }}>Pilih Jenjang</option>
                                    <option value="S1" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Status Aktivitas --}}
                            <div class="form-item">
                                <label for="status_aktivitas" class="form-label-custom"><i class="bi bi-check-circle me-2"></i> Status Aktivitas</label>
                                <select id="status_aktivitas" name="status_aktivitas" class="form-select input-fancy">
                                    <option value="Aktif" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="Cuti" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Perguruan Tinggi (Non-editable) --}}
                            <div class="form-item">
                                <label class="form-label-custom"><i class="bi bi-bank me-2"></i> Perguruan Tinggi</label>
                                <input type="text" class="form-control input-fancy bg-light"
                                    value="{{ $dosen->perguruan_tinggi ?? 'Politeknik Negeri Tanah Laut' }}" disabled>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            {{-- Program Studi (Non-editable) --}}
                            <div class="form-item">
                                <label class="form-label-custom"><i class="bi bi-book-half me-2"></i> Program Studi</label>
                                <input type="text" class="form-control input-fancy bg-light"
                                    value="{{ $dosen->program_studi ?? 'Teknologi Informasi' }}" disabled>
                            </div>
                        </div>

                        {{-- Link PDDIKTI --}}
                        <div class="col-md-6">
                            <div class="form-item">
                                <label for="link_pddikti" class="form-label-custom"><i class="bi bi-link-45deg me-2"></i> Link Profil PDDIKTI</label>
                                <input type="url" name="link_pddikti" id="link_pddikti" class="form-control input-fancy"
                                        value="{{ old('link_pddikti', optional($dosen)->link_pddikti ?? '') }}"
                                        placeholder="Masukkan URL Profil PDDIKTI Anda">
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- SINTA ID --}}
                            <div class="form-item">
                                <label for="sinta_id" class="form-label-custom"><i class="bi bi-globe me-2"></i> SINTA ID</label>
                                <input type="text" name="sinta_id" id="sinta_id" class="form-control input-fancy"
                                        value="{{ old('sinta_id', optional($dosen)->sinta_id ?? $user->sinta_id ?? '') }}"
                                        placeholder="Cth: 665313 (Opsional)">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- *** Akhir Section Data Akademik *** --}}
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-between pt-4 pb-4 px-4 border-top">
                <a href="{{ route('profile.show') }}" class="btn btn-secondary-custom px-4 rounded-pill fw-semibold btn-cancel">
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Profil
                </a>
                <button type="submit" class="btn btn-primary-custom px-4 rounded-pill fw-bold shadow-lg-hover">
                    <i class="bi bi-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Variabel Warna */
    :root {
        --primary-color: #007bff; /* Biru primer */
        --dark-blue: #1A237E; /* Warna teks utama & header */
        --input-border-color: #ced4da;
        --accent-color: #FFC107; /* Kuning/Gold untuk highlight */
    }

    /* General & Body */
    body { background-color: #f0f3f8; }

    /* Card Styling */
    .form-main-card-vertical {
        max-width: 1000px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); 
        padding: 0; 
    }

    /* === Header Styling (Biru Tua) === */
    .header-simpati-style {
        background-color: var(--dark-blue);
        color: #fff;
        border-top-left-radius: calc(0.5rem - 1px);
        border-top-right-radius: calc(0.5rem - 1px);
        border-bottom: 5px solid var(--accent-color); /* Garis Kuning Pemisah */
        margin-bottom: 0 !important;
    }
    .header-icon-lg-white {
        font-size: 3rem;
        color: #fff !important; 
    }
    .form-subtitle {
        font-size: 0.95rem;
    }

    /* Section Title & Separator */
    .section-title-custom {
        font-size: 1.25rem;
        font-weight: 700 !important;
        margin-bottom: 1.5rem; /* Jarak section title ke input */
        color: var(--dark-blue);
        border-bottom: 3px solid var(--accent-color);
        padding-bottom: 8px !important;
    }
    .title-icon {
        color: var(--primary-color);
    }
    .section-divider-tight {
        /* PENTING: Mengurangi jarak section divider secara drastis */
        border-top: 1px solid #e0e0e0;
        margin-top: 1rem; /* Jarak atas section dikurangi drastis */
        margin-bottom: 1rem; /* Jarak bawah section dikurangi drastis */
        width: 100%;
        opacity: 0.5;
    }

    /* Layout: Konten */
    .row.g-4 { /* Jarak vertikal antar input (Data Personal) */
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 1.5rem; 
    }
    .row.g-2 { /* Jarak vertikal antar input (Data Akademik) */
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 0.5rem; /* Paling Rapat */
    }
    .col-12 {
        padding-left: 2rem !important;
        padding-right: 2rem !important;
    }


    /* Input Field Customization */
    .form-item {
        margin-bottom: 0; 
    }
    .form-label-custom {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark-blue);
        margin-bottom: 0.4rem;
        display: block;
    }

    .input-fancy {
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 1rem;
        border: 1px solid var(--input-border-color);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }
    .input-fancy:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.2), 0 0 10px rgba(0, 123, 255, 0.1);
    }
    .input-fancy.bg-light {
        background-color: #f7f9fc !important;
        color: #777;
    }
    .form-text {
        margin-top: 0.3rem;
    }


    /* Button Styling */
    .btn-primary-custom {
        background-color: var(--primary-color);
        border: none;
        transition: all 0.3s ease;
        padding: 10px 25px;
        font-size: 1rem;
    }
    .shadow-lg-hover:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary-custom {
        border: 1px solid var(--dark-blue);
        color: var(--dark-blue);
        background-color: #fff;
        transition: all 0.3s;
        padding: 10px 25px;
    }
    .btn-secondary-custom:hover {
        background-color: var(--dark-blue);
        color: #fff;
    }
</style>
@endsection
