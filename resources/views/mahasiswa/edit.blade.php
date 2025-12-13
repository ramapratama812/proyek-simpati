@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">Edit Profil Mahasiswa</h3>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">NIM</label>
                <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="Laki-laki" {{ $mahasiswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ $mahasiswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Semester</label>
                <input type="text" name="semester" value="{{ old('semester', $mahasiswa->semester) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Status Aktivitas</label>
                <select name="status_terakhir" class="form-control" required>
                    <option value="Aktif" {{ $mahasiswa->status_terakhir == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ $mahasiswa->status_terakhir == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ $mahasiswa->status_terakhir == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO" {{ $mahasiswa->status_terakhir == 'DO' ? 'selected' : '' }}>Drop Out</option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection