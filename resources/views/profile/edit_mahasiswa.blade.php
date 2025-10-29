@extends('layouts.app')

@section('content')

@php
    $user = $user ?? auth()->user();
    $mahasiswa = $mahasiswa ?? ($user->mahasiswa ?? null);
@endphp

<div class="container py-5">
    <div class="card border-0 rounded-4 overflow-hidden gradient-shadow">

        {{-- HEADER --}}
        <div class="text-center text-white py-4"
             style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
            <h3 class="fw-bold mb-0">Edit Profil Mahasiswa</h3>
        </div>

        {{-- BODY --}}
        <div class="card-body bg-light py-5 px-5">

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

            {{-- FORM EDIT PROFIL --}}
            <form action="{{ route('mahasiswa.update', $mahasiswa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama"
                                   value="{{ old('nama', $mahasiswa->nama ?? $user->name ?? '') }}"
                                   class="form-control rounded-pill shadow-sm @error('nama') is-invalid @enderror">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email ?? '') }}"
                                   class="form-control rounded-pill shadow-sm @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIM</label>
                            <input type="text" name="nim"
                                   value="{{ old('nim', $mahasiswa->nim ?? '') }}"
                                   class="form-control rounded-pill shadow-sm @error('nim') is-invalid @enderror">
                            @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin"
                                    class="form-select rounded-pill shadow-sm @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Semester</label>
                            <input type="number" name="semester" min="1"
                                   value="{{ old('semester', $mahasiswa->semester ?? '') }}"
                                   class="form-control rounded-pill shadow-sm @error('semester') is-invalid @enderror">
                            @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Aktivitas</label>
                            <select name="status_aktivitas"
                                    class="form-select rounded-pill shadow-sm @error('status_aktivitas') is-invalid @enderror">
                                <option value="">-- Pilih Status --</option>
                                <option value="Aktif" {{ old('status_aktivitas', $mahasiswa->status_aktivitas ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Cuti" {{ old('status_aktivitas', $mahasiswa->status_aktivitas ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>
                            @error('status_aktivitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
                        ← Kembali
                    </a>
                    <button type="submit" class="btn btn-success text-white fw-semibold px-4 py-2 rounded-pill">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CSS --}}
<style>
    body { background-color: #f5f6fa; }
    .form-control, .form-select { border: 1.5px solid #dee2e6; transition: all 0.2s ease-in-out; }
    .form-control:focus, .form-select:focus { border-color: #2575fc; box-shadow: 0 0 0 0.2rem rgba(37,117,252,.25); }
    .btn-success:hover { background-color: #2e7d32; transform: translateY(-1px); transition: 0.2s ease-in-out; }
    .gradient-shadow { box-shadow: 0 10px 30px rgba(106,17,203,.15), 0 20px 50px rgba(37,117,252,.1); }
</style>

@endsection
