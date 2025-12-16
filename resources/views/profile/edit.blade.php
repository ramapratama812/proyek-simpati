@extends('layouts.app')

@section('content')
    <div class="container py-4">

        {{-- Main Card --}}
        {{-- ❗ LEBAR MAKSIMUM DIUBAH KE 1100PX ❗ --}}
        <div class="card border-0 shadow-lg rounded-4 mx-auto form-main-card-vertical" style="max-width: 1100px !important;">

            {{-- Header (Gaya Modern) --}}
            <header class="text-center pt-4 pb-4 header-simpati-style">
                <i class="bi bi-person-circle mb-2 header-icon-lg-white"></i>
                <h3 class="fw-bold mb-1 text-white">Edit Profile Dosen</h3>
                <p class="text-light form-subtitle">
                    Perbarui informasi pribadi dan akademik Anda.
                </p>
            </header>

            {{-- FORM --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body p-5">

                    {{-- ================= BAGIAN 1: FOTO PROFIL ================= --}}
                    <h4 class="fw-bold mb-4 section-title-custom text-primary-dark">
                        <i class="bi bi-camera me-2 title-icon"></i> Foto Profile
                    </h4>

                    <div class="col-12 text-center mb-4 pt-2">
                        <img src="{{ $dosen->foto ? asset('storage/' . $dosen->foto) : asset('images/default-user.png') }}"
                            class="avatar mb-3 shadow-lg">

                        <input type="file" name="foto" class="form-control input-fancy input-file-custom mx-auto"
                            style="max-width: 350px;" accept="image/*">

                        <div class="form-text small text-muted mt-2">
                            JPG / PNG
                        </div>
                        @error('foto')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="section-divider-bold my-5">

                    {{-- ================= BAGIAN 2: DATA PERSONAL ================= --}}
                    <h4 class="fw-bold mb-4 section-title-custom text-primary-dark">
                        <i class="bi bi-person me-2 title-icon"></i> Data Personal
                    </h4>

                    <div class="row g-4">
                        {{-- NAMA --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control input-fancy"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- EMAIL (DISABLED) --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Email</label>
                            <input type="email" class="form-control input-fancy bg-light" value="{{ $user->email }}"
                                disabled>
                            <div class="form-text small text-muted">Email tidak dapat diubah.</div>
                        </div>

                        {{-- NOMOR HP --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Nomor HP</label>
                            <input type="text" name="nomor_hp" class="form-control input-fancy"
                                value="{{ old('nomor_hp', $dosen->nomor_hp ?? '') }}">
                            @error('nomor_hp')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- JENIS KELAMIN --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select input-fancy">
                                <option value="">Pilih</option>
                                <option value="Laki-laki" {{ ($dosen->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ ($dosen->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="section-divider-bold my-5">

                    {{-- ================= BAGIAN 3: DATA AKADEMIK ================= --}}
                    <h4 class="fw-bold mb-4 section-title-custom text-primary-dark">
                        <i class="bi bi-mortarboard me-2 title-icon"></i> Data Akademik
                    </h4>

                    <div class="row g-4">

                        {{-- NIDN / NIP --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">NIDN / NIP</label>
                            <input type="text" name="nidn" class="form-control input-fancy"
                                value="{{ old('nidn', $dosen->nidn ?? '') }}">
                            @error('nidn')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- STATUS IKATAN KERJA --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Status Ikatan Kerja</label>
                            <select name="status_ikatan_kerja" class="form-select input-fancy">
                                <option value="">Pilih</option>
                                <option value="Dosen Tetap"
                                    {{ ($dosen->status_ikatan_kerja ?? '') == 'Dosen Tetap' ? 'selected' : '' }}>Dosen Tetap
                                </option>
                                <option value="Dosen Tidak Tetap"
                                    {{ ($dosen->status_ikatan_kerja ?? '') == 'Dosen Tidak Tetap' ? 'selected' : '' }}>Dosen
                                    Tidak Tetap</option>
                            </select>
                            @error('status_ikatan_kerja')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PENDIDIKAN TERAKHIR --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="form-select input-fancy">
                                <option value="">Pilih</option>
                                <option value="S1" {{ ($dosen->pendidikan_terakhir ?? '') == 'S1' ? 'selected' : '' }}>S1
                                </option>
                                <option value="S2" {{ ($dosen->pendidikan_terakhir ?? '') == 'S2' ? 'selected' : '' }}>S2
                                </option>
                                <option value="S3" {{ ($dosen->pendidikan_terakhir ?? '') == 'S3' ? 'selected' : '' }}>S3
                                </option>
                            </select>
                            @error('pendidikan_terakhir')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- STATUS AKTIVITAS --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Status Aktivitas</label>
                            <select name="status_aktivitas" class="form-select input-fancy">
                                <option value="Aktif" {{ ($dosen->status_aktivitas ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="Tidak Aktif"
                                    {{ ($dosen->status_aktivitas ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif
                                </option>
                                <option value="Cuti" {{ ($dosen->status_aktivitas ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti
                                </option>
                            </select>
                            @error('status_aktivitas')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- LINK PDDIKTI --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">Link PDDIKTI</label>
                            <input type="url" name="link_pddikti" class="form-control input-fancy"
                                value="{{ old('link_pddikti', $dosen->link_pddikti ?? '') }}">
                            @error('link_pddikti')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SINTA ID --}}
                        <div class="col-md-6">
                            <label class="form-label-custom text-primary-dark">SINTA ID</label>
                            <input type="text" name="sinta_id" class="form-control input-fancy"
                                value="{{ old('sinta_id', $dosen->sinta_id ?? ($dosen->id_sinta ?? '')) }}">
                            @error('sinta_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-between pt-4 pb-4 px-5 border-top footer-button-area">
                    <a href="{{ route('profile.show') }}"
                        class="btn btn-secondary-custom rounded-pill btn-smooth-action">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary-custom rounded-pill btn-smooth-action">
                        <i class="bi bi-save me-2"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <style>
        /* ===== PALET WARNA ===== */
        :root {
            --primary-color: #0050a0;
            --primary-dark: #001F4D;
            --header-bg: linear-gradient(135deg, var(--primary-dark) 0%, #0a3d62 100%);
            --accent-color: #ffc107;
            /* Kuning */
            --input-border-color: #e6e7ee;
            --button-primary: var(--primary-color);
            --button-secondary: #6c757d;
            --text-color: #333;
            --divider-color: #c7d2e0;
            /* Warna untuk pemisah yang lebih jelas */
        }

        /* ===== GLOBAL STYLING ===== */
        body {
            background: #f4f7fc;
            font-family: 'Poppins', sans-serif;
        }

        .form-main-card-vertical {
            transition: box-shadow 0.3s ease;
            border: 1px solid #e0e5ee;
        }

        .form-main-card-vertical:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* ===== HEADER STYLING ===== */
        .header-simpati-style {
            background: var(--header-bg);
            border-bottom: 5px solid var(--accent-color);
            box-shadow: 0 4px 15px rgba(0, 31, 77, 0.4);
            border-radius: 0;
            margin-bottom: 0 !important;
        }

        .header-icon-lg-white {
            font-size: 2.5rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .form-subtitle {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* ===== AVATAR & UPLOAD ===== */
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--accent-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .input-file-custom {
            max-width: 300px;
            padding: 0.75rem 1rem;
            cursor: pointer;
            font-size: 0.9rem;
            /* Menyesuaikan input file agar seragam */
            border: 1px solid var(--input-border-color);
            border-radius: 10px;
        }

        .input-file-custom:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 80, 160, 0.2);
        }


        /* ===== FORM ELEMENTS ===== */
        .text-primary-dark {
            color: var(--primary-dark) !important;
        }

        /* Judul Bagian Utama */
        .section-title-custom {
            color: var(--primary-dark);
            font-size: 1.5rem;
            margin-bottom: 1.5rem !important;
            /* Jarak yang konsisten */
            border-bottom: 2px solid #f0f4f8;
            padding-bottom: 0.5rem;
        }

        .section-title-custom .title-icon {
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .form-label-custom {
            font-weight: 600;
            color: var(--primary-dark);
            /* Label lebih jelas */
            display: block;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .input-fancy {
            border: 1px solid var(--input-border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #fff;
            color: var(--primary-dark);
            font-size: 0.95rem;
        }

        .input-fancy:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 80, 160, 0.2);
        }

        /* ❗ KOREKSI: BATAS TEBAL HORIZONTAL ANTAR BAGIAN FORM ❗ */
        .section-divider-bold {
            border: 0;
            height: 3px;
            /* Ditebalkan agar lebih jelas */
            background-color: var(--divider-color);
        }


        /* ===== BUTTONS ===== */
        .footer-button-area {
            padding-left: 2.5rem !important;
            padding-right: 2.5rem !important;
            border-top: 1px solid var(--divider-color) !important;
            /* Batas atas button footer diperjelas */
            background: #fcfcfc;
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary-custom {
            background-color: var(--button-primary);
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary-custom {
            background-color: var(--button-secondary);
            color: white;
        }

        .btn-secondary-custom:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-smooth-action {
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {

            /* Menyesuaikan padding di mobile agar tidak terlalu ketat */
            .card-body,
            .footer-button-area {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }
        }
    </style>
@endsection
