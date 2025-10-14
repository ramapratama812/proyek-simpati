@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 mx-auto"
         style="max-width: 1200px; background-color: #ffffff; padding: 3rem 4rem;">

        <h3 class="text-center fw-bold mb-2 text-primary">Edit Profil Dosen</h3>
        <p class="text-center text-muted mb-5" style="font-size: 0.95rem;">
            Perbarui informasi pribadi dan akademik Anda.
        </p>

        {{-- Pastikan route pakai profile.update --}}
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-5">
                {{-- Kolom kiri --}}
                <div class="col-md-6">
                    <div class="p-4 rounded-3 shadow-sm mb-4" style="background-color: #fafbfc;">
                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama</label>
                            <input type="text" id="name" name="name" class="form-control shadow-sm"
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        {{-- Email (non-editable) --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" id="email" class="form-control shadow-sm bg-light"
                                   value="{{ $user->email }}" disabled>
                        </div>

                        {{-- Nomor HP --}}
                        <div class="mb-3">
                            <label for="nomor_hp" class="form-label fw-semibold">Nomor HP</label>
                            <input type="text" id="nomor_hp" name="nomor_hp" class="form-control shadow-sm"
                                   value="{{ old('nomor_hp', $dosen->nomor_hp ?? '') }}"
                                   placeholder="Contoh: 081234567890">
                        </div>

                        {{-- NIDN / NIP --}}
                        <div class="mb-3">
                            <label for="nidn" class="form-label fw-semibold">NIDN / NIP</label>
                            <input type="text" id="nidn" name="nidn" class="form-control shadow-sm"
                                   value="{{ old('nidn', $dosen->nidn ?? '') }}">
                        </div>

                        {{-- Perguruan Tinggi --}}
                        
                    </div>
                </div>

                {{-- Kolom kanan --}}
                <div class="col-md-6">
                    <div class="p-4 rounded-3 shadow-sm mb-4" style="background-color: #fafbfc;">
                        {{-- Status Ikatan Kerja --}}
                        <div class="mb-3">
                            <label for="status_ikatan_kerja" class="form-label fw-semibold">Status Ikatan Kerja</label>
                            <select id="status_ikatan_kerja" name="status_ikatan_kerja" class="form-select shadow-sm">
                                <option value="" disabled {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == '' ? 'selected' : '' }}>Pilih Status</option>
                                <option value="Dosen Tetap" {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == 'Dosen Tetap' ? 'selected' : '' }}>Dosen Tetap</option>
                                <option value="Dosen Tidak Tetap" {{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja ?? '') == 'Dosen Tidak Tetap' ? 'selected' : '' }}>Dosen Tidak Tetap</option>
                            </select>
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="form-select shadow-sm">
                                <option value="" disabled {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == '' ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $dosen->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        {{-- Pendidikan Terakhir --}}
                        <div class="mb-3">
                            <label for="pendidikan_terakhir" class="form-label fw-semibold">Pendidikan Terakhir</label>
                            <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="form-select shadow-sm">
                                <option value="" disabled {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == '' ? 'selected' : '' }}>Pilih Pendidikan</option>
                                <option value="S1" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir ?? '') == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                        </div>

                        {{-- Status Aktivitas (enum: Aktif, Tidak Aktif, Cuti) --}}
                        <div class="mb-3">
                            <label for="status_aktivitas" class="form-label fw-semibold">Status Aktivitas</label>
                            <select id="status_aktivitas" name="status_aktivitas" class="form-select shadow-sm">
                                <option value="Aktif" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="Cuti" {{ old('status_aktivitas', $dosen->status_aktivitas ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>
                        </div>

                        {{-- Program Studi --}}
                    
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('profile.show') }}" class="btn btn-outline-primary px-4 rounded-pill fw-semibold">
                    ← Batal
                </a>
                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    body { background-color: #f0f3f8; }
    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1565c0;
        box-shadow: 0 0 0 0.25rem rgba(21, 101, 192, 0.2);
    }
    .btn-primary {
        background-color: #1565c0;
        border: none;
        transition: 0.2s;
    }
    .btn-primary:hover {
        background-color: #0d47a1;
        transform: scale(1.02);
    }
    .btn-outline-primary:hover {
        background-color: #1565c0;
        color: #fff;
    }
</style>
@endsection
