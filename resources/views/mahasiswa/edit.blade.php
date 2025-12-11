@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header text-white text-center py-3"
             style="background: linear-gradient(90deg, #6A11CB 0%, #2575FC 100%);">
            <h4 class="mb-0 fw-bold">Edit Profil Mahasiswa</h4>
        </div>

        <form action="{{ route('mahasiswa.update', $mahasiswa->id) }}" method="POST" class="p-4">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control rounded-pill shadow-sm border-0"
                           value="{{ old('nama', $mahasiswa->nama) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Program Studi</label>
                    <input type="text" name="program_studi" class="form-control rounded-pill shadow-sm border-0"
                           value="{{ old('program_studi', $mahasiswa->program_studi) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control rounded-pill shadow-sm border-0"
                           value="{{ old('email', $mahasiswa->user->email ?? '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Perguruan Tinggi</label>
                    <input type="text" name="perguruan_tinggi" class="form-control rounded-pill shadow-sm border-0"
                           value="{{ old('perguruan_tinggi', $mahasiswa->perguruan_tinggi) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">NIM</label>
                    <input type="text"
                           name="nim"
                           class="form-control rounded-pill shadow-sm border-0 @error('nim') is-invalid @enderror"
                           value="{{ old('nim', $mahasiswa->nim) }}"
                           required>
                    @error('nim')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Semester</label>
                    <input type="text" name="semester" class="form-control rounded-pill shadow-sm border-0"
                           value="{{ old('semester', $mahasiswa->semester) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select rounded-pill shadow-sm border-0" required>
                        @php $jk = old('jenis_kelamin', $mahasiswa->jenis_kelamin); @endphp
                        <option value="Perempuan" {{ $jk == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        <option value="Laki-laki" {{ $jk == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status Aktivitas</label>
                    <select name="status_terakhir" class="form-select rounded-pill shadow-sm border-0" required>
                        @php $status = old('status_terakhir', $mahasiswa->status_terakhir); @endphp
                        <option value="Aktif" {{ $status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Cuti" {{ $status == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Lulus" {{ $status == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="DO" {{ $status == 'DO' ? 'selected' : '' }}>Drop Out</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5">
                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>

                <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                    <i class="bi bi-save2 me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    input.form-control:focus, select.form-select:focus {
        border-color: #2575FC !important;
        box-shadow: 0 0 8px rgba(37,117,252,0.3);
    }
</style>
@endsection
