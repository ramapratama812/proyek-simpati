@extends('layouts.app')

@section('content')

@php
    $user = $user ?? auth()->user();
    $mahasiswa = $mahasiswa ?? ($user->mahasiswa ?? null);
@endphp

<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 mx-auto"
         style="max-width: 1200px; background-color: #ffffff; padding: 3rem 4rem;">

        <h3 class="text-center fw-bold mb-2 text-primary">Edit Profil Mahasiswa</h3>
        <p class="text-center text-muted mb-4" style="font-size: 0.95rem;">
            Perbarui informasi biodata akademik Anda.
        </p>

        {{-- Notifikasi --}}
        @if(session('success'))
        <div class="alert alert-success text-center mb-4 rounded-3 shadow-sm py-2">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger text-center mb-4 rounded-3 shadow-sm py-2">
            <strong>Terjadi kesalahan!</strong> Periksa kembali input Anda.
        </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('mahasiswa.update', $mahasiswa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-5">
                {{-- Kolom kiri --}}
                <div class="col-md-6">
                    <div class="p-4 rounded-3 shadow-sm mb-4" style="background-color: #fafbfc;">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text" name="nama"
                                   value="{{ old('nama', $mahasiswa->nama ?? $user->name ?? '') }}"
                                   class="form-control shadow-sm">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email ?? '') }}"
                                   class="form-control shadow-sm">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIM</label>
                            <input type="text" name="nim"
                                   value="{{ old('nim', $mahasiswa->nim ?? '') }}"
                                   class="form-control shadow-sm">
                        </div>

                    </div>
                </div>

                {{-- Kolom kanan --}}
                <div class="col-md-6">
                    <div class="p-4 rounded-3 shadow-sm mb-4" style="background-color: #fafbfc;">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select shadow-sm">
                                <option value="" disabled {{ !$mahasiswa->jenis_kelamin ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Semester</label>
                            <input type="number" min="1" name="semester"
                                   value="{{ old('semester', $mahasiswa->semester ?? '') }}"
                                   class="form-control shadow-sm">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Aktivitas</label>
                            <select name="status_aktivitas" class="form-select shadow-sm">
                                <option value="Aktif" {{ old('status_aktivitas', $mahasiswa->status_aktivitas) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Cuti" {{ old('status_aktivitas', $mahasiswa->status_aktivitas) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('mahasiswa.index', $mahasiswa->id) }}" class="btn btn-outline-primary px-4 rounded-pill fw-semibold">
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
