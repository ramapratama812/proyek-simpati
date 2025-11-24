@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4 text-center text-white py-3 rounded-4"
        style="background: linear-gradient(90deg, #003b95 0%, #007bff 100%);">
        Edit Profil Mahasiswa
    </h3>

    <div class="card shadow-lg p-4 border-0 rounded-4" style="max-width: 900px; margin: 0 auto;">
        <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Program Studi</label>
                <input type="text" name="program_studi" value="{{ old('program_studi', $mahasiswa->program_studi) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email', $mahasiswa->user->email ?? '') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Perguruan Tinggi</label>
                <input type="text" name="perguruan_tinggi" value="{{ old('perguruan_tinggi', $mahasiswa->perguruan_tinggi) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">NIM</label>
                <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Semester</label>
                <input type="text" name="semester" value="{{ old('semester', $mahasiswa->semester) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="Perempuan" {{ $mahasiswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    <option value="Laki-laki" {{ $mahasiswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status Aktivitas</label>
                <select name="status_terakhir" class="form-select" required>
                    <option value="Aktif" {{ $mahasiswa->status_terakhir == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ $mahasiswa->status_terakhir == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ $mahasiswa->status_terakhir == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO" {{ $mahasiswa->status_terakhir == 'DO' ? 'selected' : '' }}>Drop Out</option>
                </select>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary px-4">
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    body {
        background: #f4f7fc;
    }
    .form-control:focus, .form-select:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 8px rgba(0,123,255,0.3);
    }
</style>
@endsection
