@extends('layouts.app') 
@section('content')

<div class="container py-5 d-flex justify-content-center">
    
    {{-- 🔔 Notifikasi Sukses (posisi fixed kanan atas) --}}
    @if(session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
            <div id="liveAlert" class="alert alert-success alert-dismissible fade show shadow-lg" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    
    {{-- Card Profil Utama --}}
    <div class="card shadow-lg p-4" style="max-width: 600px; width:100%; border-radius: 15px;">

        {{-- Foto/Inisial dan Nama --}}
        <div class="d-flex flex-column align-items-center text-center mb-4">
            @php
                $photo = $mahasiswa->foto ?? null;
                $nameParts = explode(' ', $mahasiswa->nama ?? Auth::user()->name ?? 'U');
                $initials = strtoupper(substr($nameParts[0], 0, 1));
                if (count($nameParts) > 1) {
                    $initials .= strtoupper(substr(end($nameParts), 0, 1));
                }
            @endphp

            @if ($photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($photo))
                <img src="{{ asset('storage/' . $photo) }}" 
                    alt="Foto Profil"
                    class="rounded-circle border border-3 shadow-sm object-fit-cover"
                    width="100" height="100" style="border-color: #0d6efd !important;">
            @else
                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center border border-3 shadow-sm" 
                    style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                    {{ $initials }}
                </div>
            @endif
            
            <h4 class="fw-bold mt-3 mb-1">{{ $mahasiswa->nama ?? 'Pengguna' }}</h4>
            <p class="text-muted mb-0 small">{{ Auth::user()->email ?? '-' }}</p>
        </div>

        <hr class="mt-0 mb-4"> 

        {{-- Detail Informasi --}}
        <ul class="list-group list-group-flush text-start">
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="small text-muted" style="min-width: 150px;">NIM:</strong>
                <span class="fw-medium text-end">{{ $mahasiswa->nim ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="small text-muted" style="min-width: 150px;">Program Studi:</strong>
                <span class="fw-medium text-end">{{ $mahasiswa->program_studi ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="small text-muted" style="min-width: 150px;">Perguruan Tinggi:</strong>
                <span class="fw-medium text-end">{{ $mahasiswa->perguruan_tinggi ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="small text-muted" style="min-width: 150px;">Nomor Telepon:</strong>
                <span class="fw-medium text-end">{{ $mahasiswa->nomor_telepon ?? Auth::user()->phone ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="small text-muted" style="min-width: 150px;">Jenis Kelamin:</strong>
                <span class="fw-medium text-end">{{ $mahasiswa->jenis_kelamin ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="small text-muted" style="min-width: 150px;">Status Terakhir:</strong>
                <span class="fw-medium text-end">{{ $mahasiswa->status_terakhir ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <strong class="small text-muted" style="min-width: 150px;">Role:</strong>
                <span class="fw-bold text-primary text-end">{{ Auth::user()->role ?? 'Mahasiswa' }}</span>
            </li>
        </ul>

        {{-- Tombol Edit --}}
        <div class="mt-4 d-grid">
            <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-primary btn-lg">
                Edit Profil
            </a>
        </div>
    </div>
</div>
@endsection
