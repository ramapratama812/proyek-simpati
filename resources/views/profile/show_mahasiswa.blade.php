@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 850px; width: 100%;">
            
            {{-- HEADER --}}
            <div class="py-4 text-center text-white"
                 style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
                <h3 class="fw-bold mb-0">Detail Profil Mahasiswa</h3>
            </div>

            {{-- NOTIFIKASI --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0 text-center" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- BODY --}}
            <div class="card-body bg-white p-5">

                <div class="row">
                    {{-- KIRI --}}
                    <div class="col-md-6 mb-3">
                        <p><strong>Nama</strong><br>{{ $mahasiswa->nama ?? '-' }}</p>
                        <p><strong>Email</strong><br>{{ $user->email ?? '-' }}</p>
                        <p><strong>NIM</strong><br>{{ $mahasiswa->nim ?? '-' }}</p>
                        <p><strong>Jenis Kelamin</strong><br>{{ $mahasiswa->jenis_kelamin ?? '-' }}</p>
                    </div>

                    {{-- KANAN --}}
                    <div class="col-md-6 mb-3">
                        <p><strong>Perguruan Tinggi</strong><br>{{ $mahasiswa->perguruan_tinggi ?? '-' }}</p>
                        <p><strong>Program Studi</strong><br>{{ $mahasiswa->program_studi ?? '-' }}</p>
                        <p><strong>Semester</strong><br>{{ $mahasiswa->semester ?? '-' }}</p>
                        <p><strong>Status Aktivitas</strong><br>
                            <span class="badge px-3 py-2 {{ ($mahasiswa->status_aktivitas ?? '') == 'Aktif' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $mahasiswa->status_aktivitas ?? 'Aktif' }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                {{-- TOMBOL AKSI --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                        ← Kembali
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-warning rounded-pill px-4 text-white fw-semibold">
                            <i class="bi bi-pencil-square"></i> Edit Profil
                        </a>

                        {{-- FORM HAPUS AKUN --}}
                        <form action="{{ route('profile.destroy') }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus akun ini? Semua data akan hilang!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                                <i class="bi bi-trash"></i> Hapus Akun
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- CSS --}}
<style>
body { background-color: #f5f6fa; font-family: 'Poppins', sans-serif; }
.card { border-radius: 1rem; }
p { font-size: 0.95rem; margin-bottom: 1rem; }
strong { color: #003b95; }

.btn-outline-primary {
    border-color: #2575fc; color: #2575fc;
}
.btn-outline-primary:hover {
    background-color: #2575fc; color: #fff;
}
.btn-warning {
    background-color: #ffc107; border: none;
}
.btn-warning:hover {
    background-color: #e0a800;
}
.btn-danger {
    background-color: #dc3545; border: none;
}
.btn-danger:hover {
    background-color: #bb2d3b;
}
.badge {
    font-size: 0.9rem; border-radius: 10px;
}
</style>
@endsection
