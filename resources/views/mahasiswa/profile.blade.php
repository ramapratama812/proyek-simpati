@extends('layouts.app')
@section('content')

<div class="container py-5 d-flex justify-content-center">

    {{-- 🔔 Notifikasi Sukses --}}
    @if(session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
            <div id="liveAlert" class="alert alert-success alert-dismissible fade show shadow-lg rounded-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- Card Profil --}}
    <div class="card border-0 shadow-lg rounded-4 p-4"
         style="max-width: 700px; width:100%; background-color: #ffffff;">

        {{-- Header --}}
        <h3 class="text-center fw-bold mb-2 text-primary">Profil Mahasiswa</h3>
        <p class="text-center text-muted mb-4" style="font-size: 0.95rem;">
            Informasi pribadi dan akademik Anda.
        </p>

        {{-- Foto / Inisial --}}
        <div class="d-flex flex-column align-items-center text-center mb-4">
            @php
                $photo = $mahasiswa->foto ?? null;
                $nameParts = explode(' ', $mahasiswa->nama ?? Auth::user()->name ?? 'U');
                $initials = strtoupper(substr($nameParts[0], 0, 1));
                if (count($nameParts) > 1) {
                    $initials .= strtoupper(substr(end($nameParts), 0, 1));
                }
            @endphp

            @if ($photo && Storage::disk('public')->exists($photo))
                <img src="{{ asset('storage/' . $photo) }}"
                     alt="Foto Profil"
                     class="rounded-circle border border-3 shadow-sm object-fit-cover"
                     width="110" height="110"
                     style="border-color: #1565c0 !important;">
            @else
                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center border border-3 shadow-sm"
                     style="width: 110px; height: 110px; font-size: 2.8rem; font-weight: bold;">
                    {{ $initials }}
                </div>
            @endif

            <h4 class="fw-bold mt-3 mb-1">{{ $mahasiswa->nama ?? 'Pengguna' }}</h4>
            <p class="text-muted small mb-0">{{ Auth::user()->email ?? '-' }}</p>
        </div>

        <hr class="mt-0 mb-4">

        {{-- Informasi Detail --}}
        <ul class="list-group list-group-flush text-start">
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="text-muted" style="min-width: 160px;">NIM</strong>
                <span class="fw-semibold text-end">{{ $mahasiswa->nim ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="text-muted" style="min-width: 160px;">Jenis Kelamin</strong>
                <span class="fw-semibold text-end">{{ $mahasiswa->jenis_kelamin ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="text-muted" style="min-width: 160px;">Status Terakhir</strong>
                <span class="fw-semibold text-end">{{ $mahasiswa->status_terakhir ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="text-muted" style="min-width: 160px;">Role</strong>
                <span class="fw-bold text-primary text-end">{{ Auth::user()->role ?? 'Mahasiswa' }}</span>
            </li>
        </ul>

        {{-- Tombol --}}
        <div class="mt-4 d-flex justify-content-end">
            <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}"
               class="btn btn-primary px-4 rounded-pill fw-semibold shadow-sm">
                Edit Profil
            </a>
        </div>
    </div>
</div>

{{-- CSS Global Samakan Dengan Halaman Dosen --}}
<style>
    body { background-color: #f0f3f8; }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1565c0;
        box-shadow: 0 0 0 0.25rem rgba(21, 101, 192, 0.2);
    }

    .btn-primary {
        background-color: #1565c0;
        border: none;
        transition: 0.2s;
    }
    .btn-primary:hover {
        background-color: #0d47a1;
        transform: scale(1.02);
    }
</style>

@endsection
