@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 mx-auto"
         style="max-width: 1000px; background-color: #ffffff;">

        {{-- CARD HEADER --}}
        <div class="card-header bg-white border-0 pt-5 px-5">
            <h3 class="text-center fw-bolder mb-2" style="color: #001F4D;">
                <i class="bi bi-person-circle me-2"></i> Edit Profil Dosen
            </h3>
            <p class="text-center text-muted mb-0" style="font-size: 0.95rem;">
                Perbarui informasi pribadi dan akademik Anda.
            </p>
        </div>

        <div class="card-body px-5 pb-5">

            {{-- NOTIFIKASI ERROR/SUCCESS --}}
            @if(session('success'))
                <div class="alert alert-success text-center mb-4 rounded-3 shadow-sm py-2">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-4 rounded-3 shadow-sm py-3">
                    Mohon periksa kembali input Anda. Terdapat beberapa kesalahan validasi.
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- ========================== --}}
                    {{-- KOLOM KIRI: DATA PERSONAL --}}
                    {{-- ========================== --}}
                    <div class="col-md-6">
                        <fieldset class="form-group-section">
                            <legend class="fw-bold fs-5 mb-4 text-secondary">
                                <i class="bi bi-person-vcard-fill me-2"></i> Data Personal
                            </legend>

                            {{-- Nama --}}
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-1"></i> Nama Lengkap
                                </label>
                                <input type="text" id="name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email (non editable) --}}
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope-fill me-1"></i> Email (Akun)
                                </label>
                                <input type="email" id="email" class="form-control bg-light"
                                       value="{{ $user->email }}" disabled>
                                <small class="form-text text-muted">Email tidak dapat diubah di halaman ini.</small>
                            </div>

                            {{-- Nomor HP --}}
                            <div class="mb-4">
                                <label for="nomor_hp" class="form-label fw-semibold">
                                    <i class="bi bi-phone-fill me-1"></i> Nomor HP
                                </label>
                                <input type="text" id="nomor_hp" name="nomor_hp"
                                       class="form-control @error('nomor_hp') is-invalid @enderror"
                                       placeholder="Contoh: 081234567890"
                                       value="{{ old('nomor_hp', $dosen->nomor_hp ?? '') }}">
                                @error('nomor_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="mb-4">
                                <label for="jenis_kelamin" class="form-label fw-semibold">
                                    <i class="bi bi-gender-ambiguous me-1"></i> Jenis Kelamin
                                </label>
                                <select id="jenis_kelamin" name="jenis_kelamin"
                                        class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </fieldset>
                    </div>

                    {{-- ========================== --}}
                    {{-- KOLOM KANAN: DATA AKADEMIK --}}
                    {{-- ========================== --}}
                    <div class="col-md-6">
                        <fieldset class="form-group-section">
                            <legend class="fw-bold fs-5 mb-4 text-secondary">
                                <i class="bi bi-mortarboard-fill me-2"></i> Data Akademik
                            </legend>

                            {{-- NIDN / NIP --}}
                            <div class="mb-4">
                                <label for="nidn" class="form-label fw-semibold">
                                    <i class="bi bi-qr-code-scan me-1"></i> NIDN / NIP
                                </label>
                                <input type="text" id="nidn" name="nidn"
                                       class="form-control @error('nidn') is-invalid @enderror"
                                       placeholder="Masukkan NIDN atau NIP Anda"
                                       value="{{ old('nidn', $dosen->nidn ?? '') }}">
                                @error('nidn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status Ikatan Kerja --}}
                            <div class="mb-4">
                                <label for="status_ikatan_kerja" class="form-label fw-semibold">
                                    <i class="bi bi-briefcase-fill me-1"></i> Status Ikatan Kerja
                                </label>
                                <select id="status_ikatan_kerja" name="status_ikatan_kerja"
                                        class="form-select @error('status_ikatan_kerja') is-invalid @enderror">
                                    <option value="">Pilih Status</option>
                                    <option value="Dosen Tetap" {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == 'Dosen Tetap' ? 'selected' : '' }}>Dosen Tetap</option>
                                    <option value="Dosen Tidak Tetap" {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == 'Dosen Tidak Tetap' ? 'selected' : '' }}>Dosen Tidak Tetap</option>
                                </select>
                                @error('status_ikatan_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pendidikan Terakhir --}}
                            <div class="mb-4">
                                <label for="pendidikan_terakhir" class="form-label fw-semibold">
                                    <i class="bi bi-book-fill me-1"></i> Pendidikan Terakhir
                                </label>
                                <select id="pendidikan_terakhir" name="pendidikan_terakhir"
                                        class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                                    <option value="">Pilih Jenjang</option>
                                    <option value="S1" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('pendidikan_terakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status Aktivitas --}}
                            <div class="mb-4">
                                <label for="status_aktivitas" class="form-label fw-semibold">
                                    <i class="bi bi-activity me-1"></i> Status Aktivitas
                                </label>
                                <select id="status_aktivitas" name="status_aktivitas"
                                        class="form-select @error('status_aktivitas') is-invalid @enderror">
                                    <option value="Aktif" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Cuti" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                    <option value="Tidak Aktif" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status_aktivitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Perguruan Tinggi (Dipindahkan ke sini agar logis) --}}
                            <div class="mb-4">
                                <label for="perguruan_tinggi" class="form-label fw-semibold">
                                    <i class="bi bi-house-door-fill me-1"></i> Perguruan Tinggi
                                </label>
                                <input type="text" id="perguruan_tinggi" name="perguruan_tinggi"
                                       class="form-control @error('perguruan_tinggi') is-invalid @enderror"
                                       value="{{ old('perguruan_tinggi', $dosen->perguruan_tinggi ?? '') }}">
                                @error('perguruan_tinggi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PROGRAM STUDI --}}
                            <div class="mb-4">
                                <label for="program_studi" class="form-label fw-semibold">
                                    <i class="bi bi-clipboard-data-fill me-1"></i> Program Studi
                                </label>
                                <input type="text" id="program_studi" name="program_studi"
                                       class="form-control @error('program_studi') is-invalid @enderror"
                                       value="{{ old('program_studi', $dosen->program_studi ?? '') }}">
                                @error('program_studi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- LINK PDDIKTI --}}
                            <div class="mb-0">
                                <label for="link_pddikti" class="form-label fw-semibold">
                                    <i class="bi bi-link-45deg me-1"></i> Link Profil PDDIKTI
                                </label>
                                <input type="text" id="link_pddikti" name="link_pddikti"
                                       class="form-control @error('link_pddikti') is-invalid @enderror"
                                       placeholder="https://pddikti.kemdikbud.go.id/data/..."
                                       value="{{ old('link_pddikti', $dosen->link_pddikti ?? '') }}">
                                @error('link_pddikti')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </fieldset>
                    </div>
                </div>

                <hr class="mt-5 mb-4">

                {{-- Tombol --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
                    </a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-semibold shadow-lg">
                        <i class="bi bi-save-fill me-1"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    /* Warna utama yang lebih gelap dan profesional */
    :root {
        --primary-color: #001F4D; /* Warna biru gelap */
        --secondary-color: #495057;
        --accent-color: #1565c0;
    }

    body { background-color: #f8f9fa; }

    /* Styling Form Group Section */
    .form-group-section {
        border: 1px solid #dee2e6;
        padding: 25px;
        border-radius: 12px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        height: 100%; /* Agar tinggi fieldset sama di kolom */
    }

    .form-group-section legend {
        float: none;
        width: inherit;
        padding: 0 10px;
        border-bottom: none;
        font-size: 1.1rem;
        color: var(--primary-color) !important;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.95rem;
        border: 1px solid #ced4da;
    }

    /* Custom Focus Style */
    .form-control:focus, .form-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.25rem rgba(21, 101, 192, 0.25);
        background-color: #fff;
    }

    /* Button Styling */
    .btn-primary {
        background-color: var(--accent-color);
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-primary:hover {
        background-color: #0d47a1;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15) !important;
    }
    .btn-outline-secondary {
        color: var(--secondary-color);
        border-color: var(--secondary-color);
        transition: all 0.2s ease-in-out;
    }
    .btn-outline-secondary:hover {
        background-color: var(--secondary-color);
        color: #fff;
    }

    /* Invalid feedback style */
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .invalid-feedback {
        font-size: 0.85rem;
    }
</style>
@endsection