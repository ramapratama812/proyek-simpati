@extends('layouts.app')
@section('content')
    <div class="container py-5 d-flex justify-content-center">

        {{-- 🔔 Notifikasi Sukses --}}
        @if (session('success'))
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
                <div class="alert alert-success alert-dismissible fade show shadow-lg rounded-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        {{-- Card Profil --}}
        <div class="card border-0 shadow-lg rounded-4 mx-auto"
            style="max-width: 700px; width:100%; background-color: #ffffff; padding: 3rem 4rem;">

            {{-- Header --}}
            <h3 class="text-center fw-bold mb-2 text-primary">Profil Mahasiswa</h3>
            <p class="text-center text-muted mb-5" style="font-size: 0.95rem;">
                Informasi pribadi dan akademik Anda.
            </p>

            {{-- Foto / Inisial --}}
            <div class="d-flex flex-column align-items-center text-center mb-4">
                @php
                    $photo = $mahasiswa->foto ?? null;
                    $nameParts = explode(' ', $mahasiswa->nama ?? (Auth::user()->name ?? 'U'));
                    $initials = strtoupper(substr($nameParts[0], 0, 1));
                    if (count($nameParts) > 1) {
                        $initials .= strtoupper(substr(end($nameParts), 0, 1));
                    }
                @endphp

                @if ($photo && Storage::disk('public')->exists($photo))
                    <img src="{{ asset('storage/' . $photo) }}"
                        class="rounded-circle border border-3 shadow-sm object-fit-cover" width="120" height="120"
                        style="border-color: #1565c0 !important;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center
                            border border-3 shadow-sm"
                        style="width: 120px; height: 120px; font-size: 2.8rem; font-weight: bold;">
                        {{ $initials }}
                    </div>
                @endif

                <h4 class="fw-bold mt-3 mb-1">{{ $mahasiswa->nama ?? 'Pengguna' }}</h4>
                <p class="text-muted small mb-0">{{ Auth::user()->email ?? '-' }}</p>
            </div>

            <hr class="mt-0 mb-4">

            {{-- Informasi Detail --}}
            <div class="p-4 rounded-3 shadow-sm mb-4" style="background-color: #fafbfc;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted fw-semibold">NIM</span>
                        <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted fw-semibold">Jenis Kelamin</span>
                        <span class="fw-semibold">{{ $mahasiswa->jenis_kelamin ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted fw-semibold">Status Terakhir</span>
                        <span class="fw-semibold">{{ $mahasiswa->status_terakhir ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted fw-semibold">Role</span>
                        <span class="fw-bold text-primary">{{ Auth::user()->role ?? 'Mahasiswa' }}</span>
                    </li>
                </ul>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}"
                    class="btn btn-primary px-4 rounded-pill fw-semibold shadow-sm">
                    Edit Profil
                </a>
            </div>
        </div>
    </div>

    {{-- CSS SAMA PERSIS DENGAN DOSEN --}}
    <style>
        body {
            background-color: #f0f3f8;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
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

        .btn-outline-primary:hover {
            background-color: #1565c0;
            color: #fff;
        }
    </style>
@endsection
