@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">Edit Profil Dosen</h3>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('dosen.update', $dosen->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $dosen->nama) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <input type="text" name="jenis_kelamin" value="{{ old('jenis_kelamin', $dosen->jenis_kelamin) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Perguruan Tinggi</label>
                <input type="text" name="perguruan_tinggi" value="{{ old('perguruan_tinggi', $dosen->perguruan_tinggi) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Program Studi</label>
                <input type="text" name="program_studi" value="{{ old('program_studi', $dosen->program_studi) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Pendidikan Terakhir</label>
                <input type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Status Ikatan Kerja</label>
                <input type="text" name="status_ikatan_kerja" value="{{ old('status_ikatan_kerja', $dosen->status_ikatan_kerja) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Status Aktivitas</label>
                <input type="text" name="status_aktivitas" value="{{ old('status_aktivitas', $dosen->status_aktivitas) }}" class="form-control">
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('dosen.show', $dosen->id) }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
