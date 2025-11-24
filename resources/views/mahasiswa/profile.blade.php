@extends('layouts.app') 
@section('content')

<div class="container py-4 d-flex justify-content-center">
    
    {{-- 🔔 Notifikasi Sukses --}}
    @if(session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
            <div id="liveAlert" class="alert alert-success alert-dismissible fade show shadow-lg small" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    
    {{-- Card Profil --}}
    <div class="card shadow p-3" style="max-width: 450px; width:100%; border-radius: 15px;">

        {{-- Nama & Email --}}
        <div class="text-center mb-3">
            <h5 class="fw-bold mb-1">{{ $mahasiswa->nama ?? 'Pengguna' }}</h5>
            <p class="text-muted mb-0 small">{{ Auth::user()->email ?? '-' }}</p>
        </div>

        <hr class="mt-2 mb-3"> 

        {{-- Detail Informasi --}}
        <ul class="list-group list-group-flush small">
            <li class="list-group-item d-flex justify-content-between px-0 py-1">
                <strong class="text-muted" style="min-width: 120px;">NIM</strong>
                <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0 py-1">
                <strong class="text-muted" style="min-width: 120px;">Program Studi</strong>
                <span class="fw-semibold">{{ $mahasiswa->program_studi ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0 py-1">
                <strong class="text-muted" style="min-width: 120px;">Jenis Kelamin</strong>
                <span class="fw-semibold">{{ $mahasiswa->jenis_kelamin ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0 py-1">
                <strong class="text-muted" style="min-width: 120px;">Status Terakhir</strong>
                <span class="fw-semibold">{{ $mahasiswa->status_terakhir ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0 py-1">
                <strong class="text-muted" style="min-width: 120px;">Role</strong>
                <span class="fw-bold text-primary">{{ Auth::user()->role ?? 'Mahasiswa' }}</span>
            </li>
        </ul>

        {{-- Tombol Edit --}}
        <div class="mt-3 d-grid">
            <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-primary btn-sm rounded-4 fw-semibold">
                Edit Profil
            </a>
        </div>
    </div>
</div>
@endsection
