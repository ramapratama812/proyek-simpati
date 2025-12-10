@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Header Biru Tua dengan Gradasi --}}
    <div class="header-box text-center text-white fw-bold mb-4 py-3 rounded-4 shadow-lg">
        Tambah Mahasiswa
    </div>

    <div class="card shadow-lg border-0 rounded-4 p-4 mx-auto" style="max-width: 700px;">

        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mahasiswa.store') }}" method="POST">
            @csrf

            {{-- Nama --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama</label>
                <input type="text" name="nama" class="form-control input-rounded shadow-sm" value="{{ old('nama') }}" required>
            </div>

            {{-- NIM --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">NIM</label>
                <input type="text" name="nim" class="form-control input-rounded shadow-sm" value="{{ old('nim') }}" required>
            </div>

            {{-- Jenis Kelamin --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select input-rounded shadow-sm" required>
                    <option value="">-- Pilih --</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin') == "Laki-laki" ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin') == "Perempuan" ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            {{-- Status Terakhir --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Status Terakhir</label>
                <input type="text" name="status_terakhir" class="form-control input-rounded shadow-sm" value="{{ old('status_terakhir') }}">
            </div>

            {{-- Tombol --}}
            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>

        </form>
    </div>
</div>

<style>
body {
    background: #f4f7fc;
}

.header-box {
    background: linear-gradient(90deg, #003b95, #007bff);
    font-size: 1.4rem;
    letter-spacing: 0.5px;
}

.input-rounded {
    border-radius: 12px;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    transition: 0.2s;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #003f88);
    transform: scale(1.04);
}
</style>

@endsection
