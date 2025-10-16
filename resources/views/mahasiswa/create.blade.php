@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Mahasiswa</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mahasiswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Nama --}}
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" id="nama" name="nama" 
                   class="form-control @error('nama') is-invalid @enderror" 
                   value="{{ old('nama') }}" required>
            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- NIM --}}
        <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" id="nim" name="nim" 
                   class="form-control @error('nim') is-invalid @enderror" 
                   value="{{ old('nim') }}" required>
            @error('nim')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Jenis Kelamin --}}
        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" 
                    class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Program Studi --}}
        <div class="mb-3">
            <label for="program_studi" class="form-label">Program Studi</label>
            <input type="text" id="program_studi" name="program_studi" 
                   class="form-control @error('program_studi') is-invalid @enderror" 
                   value="{{ old('program_studi') }}" required>
            @error('program_studi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Perguruan Tinggi --}}
        <div class="mb-3">
            <label for="perguruan_tinggi" class="form-label">Perguruan Tinggi</label>
            <input type="text" id="perguruan_tinggi" name="perguruan_tinggi" 
                   class="form-control @error('perguruan_tinggi') is-invalid @enderror" 
                   value="{{ old('perguruan_tinggi') }}" required>
            @error('perguruan_tinggi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Status Terakhir --}}
        <div class="mb-3">
            <label for="status_terakhir" class="form-label">Status Terakhir</label>
            <input type="text" id="status_terakhir" name="status_terakhir" 
                   class="form-control @error('status_terakhir') is-invalid @enderror" 
                   value="{{ old('status_terakhir') }}">
            @error('status_terakhir')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Foto --}}
        <div class="mb-3">
            <label for="foto" class="form-label">Foto</label>
            <input type="file" id="foto" name="foto" 
                   class="form-control @error('foto') is-invalid @enderror">
            @error('foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tombol --}}
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
