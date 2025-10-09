@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 rounded-4 overflow-hidden gradient-shadow">

        {{-- HEADER --}}
        <div class="text-center text-white py-4"
             style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
            <h3 class="fw-bold mb-0">Biodata Dosen</h3>
        </div>

        {{-- BODY --}}
        <div class="card-body bg-light py-5 px-5">

            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="alert alert-success text-center mb-4 rounded-3 shadow-sm py-2">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                {{-- Kolom Kiri --}}
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label class="text-muted d-block">Nama</label>
                        <span class="fw-semibold">{{ $dosen->nama ?? $user->name }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Email</label>
                        <span class="fw-semibold">{{ $user->email ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Nomor HP</label>
                        <span class="fw-semibold">{{ $dosen->nomor_hp ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">NIDN / NIP</label>
                        <span class="fw-semibold">{{ $dosen->nidn ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Perguruan Tinggi</label>
                        <span class="fw-semibold">{{ $dosen->perguruan_tinggi ?? '-' }}</span>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label class="text-muted d-block">Program Studi</label>
                        <span class="fw-semibold">{{ $dosen->program_studi ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Status Ikatan Kerja</label>
                        <span class="fw-semibold">{{ $dosen->status_ikatan_kerja ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Jenis Kelamin</label>
                        <span class="fw-semibold">{{ $dosen->jenis_kelamin ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Pendidikan Terakhir</label>
                        <span class="fw-semibold">{{ $dosen->pendidikan_terakhir ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Status Aktivitas</label>
                        @if(($dosen->status_aktivitas ?? '') === 'Aktif')
                            <span class="badge bg-success px-3 py-2">Aktif</span>
                        @elseif(($dosen->status_aktivitas ?? '') === 'Cuti')
                            <span class="badge bg-warning text-dark px-3 py-2">Cuti</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('dosen.index') }}" 
                   class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
                    ← Kembali
                </a>

                <div>
                    <a href="{{ route('profile.edit') }}" 
                       class="btn text-dark fw-semibold px-4 py-2 rounded-pill me-2"
                       style="background-color: #ffca28;">
                        <i class="bi bi-pencil-square"></i> Edit Profil
                    </a>

                    <form action="{{ route('profile.destroy') }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-danger text-white fw-semibold px-4 py-2 rounded-pill">
                            <i class="bi bi-trash"></i> Hapus Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STYLE TAMBAHAN --}}
<style>
    body {
        background-color: #f5f6fa;
    }

    label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6c757d;
    }

    span.fw-semibold {
        font-size: 1rem;
        color: #212121;
    }

    .badge {
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .btn-outline-primary {
        border-width: 2px;
    }

    .btn-outline-primary:hover {
        background-color: #2575fc;
        color: #fff;
        border-color: #2575fc;
        transform: translateY(-1px);
        transition: 0.2s ease-in-out;
    }

    .btn-danger:hover {
        transform: translateY(-1px);
        transition: 0.2s ease-in-out;
        background-color: #c62828;
    }

    [style*="#ffca28"]:hover {
        background-color: #fbc02d !important;
        transform: translateY(-1px);
        transition: 0.2s ease-in-out;
    }

    /* Gradasi Shadow Glow */
    .gradient-shadow {
        box-shadow:
            0 10px 30px rgba(106, 17, 203, 0.15),
            0 20px 50px rgba(37, 117, 252, 0.1);
        transition: all 0.3s ease-in-out;
    }

    .gradient-shadow:hover {
        box-shadow:
            0 15px 45px rgba(106, 17, 203, 0.25),
            0 25px 70px rgba(37, 117, 252, 0.15);
        transform: translateY(-2px);
    }
</style>
@endsection
