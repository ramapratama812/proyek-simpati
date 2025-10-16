@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 800px; width: 100%;">

            {{-- Header --}}
            <div class="py-4 text-center text-white"
                style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
                <h3 class="fw-bold mb-0">Profil Saya</h3>
            </div>

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0 text-center" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- BODY --}}
            <div class="card-body bg-white p-5">

                {{-- === MODE VIEW === --}}
                <div id="viewMode">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama</strong><br>{{ $mahasiswa->nama ?? $user->name ?? '-' }}</p>
                            <p><strong>Email</strong><br>{{ $user->email ?? '-' }}</p>
                            <p><strong>NIM</strong><br>{{ $mahasiswa->nim ?? '-' }}</p>
                            <p><strong>Perguruan Tinggi</strong><br>{{ $mahasiswa->perguruan_tinggi ?? '-' }}</p>
                            <p><strong>Program Studi</strong><br>{{ $mahasiswa->program_studi ?? '-' }}</p>
                        </div>

                        <div class="col-md-6">
                            <p><strong>Jenjang Pendidikan</strong><br>{{ $mahasiswa->jenjang_pendidikan ?? 'D4' }}</p>
                            <p><strong>Jenis Kelamin</strong><br>{{ $mahasiswa->jenis_kelamin ?? '-' }}</p>
                            <p><strong>Semester</strong><br>{{ $mahasiswa->semester ?? '-' }}</p>
                            <p><strong>Status Aktivitas</strong><br>
                                <span class="badge px-3 py-2 {{ ($mahasiswa->status_aktivitas ?? '') == 'Aktif' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $mahasiswa->status_aktivitas ?? 'Aktif' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary rounded-pill px-4">
                            ← Kembali
                        </a>

                        <div class="d-flex gap-2">
                            <button href="{{ route('profile.edit') }}" id="editBtn" type="button" class="btn btn-warning rounded-pill px-4 text-white fw-semibold">
                                <i class="bi bi-pencil-square"></i> Edit Profil
                            </button>

                            <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                                    <i class="bi bi-trash"></i> Hapus Akun
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- === MODE EDIT === --}}
                <form id="editMode" action="{{ route('profile.update') }}" method="POST" style="display:none;">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ $mahasiswa->nama ?? $user->name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIM</label>
                                <input type="text" name="nim" class="form-control" value="{{ $mahasiswa->nim ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Perguruan Tinggi</label>
                                <input type="text" name="perguruan_tinggi" class="form-control" value="{{ $mahasiswa->perguruan_tinggi ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Program Studi</label>
                                <input type="text" name="program_studi" class="form-control" value="{{ $mahasiswa->program_studi ?? '' }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenjang Pendidikan</label>
                                <input type="text" name="jenjang_pendidikan" class="form-control" value="{{ $mahasiswa->jenjang_pendidikan ?? 'D4' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="Laki-laki" {{ ($mahasiswa->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ ($mahasiswa->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <input type="number" name="semester" class="form-control" value="{{ $mahasiswa->semester ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Aktivitas</label>
                                <select name="status_aktivitas" class="form-select">
                                    <option value="Aktif" {{ ($mahasiswa->status_aktivitas ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Cuti" {{ ($mahasiswa->status_aktivitas ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" id="cancelEdit" class="btn btn-outline-secondary rounded-pill px-4">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- SCRIPT TOGGLE --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelEdit');

    if (editBtn && viewMode && editMode) {
        editBtn.addEventListener('click', function() {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            editMode.style.display = 'none';
            viewMode.style.display = 'block';
        });
    }
});
</script>
@endpush

{{-- CSS --}}
<style>
body { background-color: #f5f6fa; font-family: 'Poppins', sans-serif; }
.card { border-radius: 1rem; }
.btn-outline-primary { border-color: #2575fc; color: #2575fc; }
.btn-outline-primary:hover { background-color: #2575fc; color: #fff; }
.btn-warning { background-color: #ffc107; border: none; }
.btn-warning:hover { background-color: #e0a800; }
.btn-danger { background-color: #dc3545; border: none; }
.btn-danger:hover { background-color: #b02a37; }
.badge { font-size: 0.9rem; border-radius: 10px; }
</style>
@endsection
