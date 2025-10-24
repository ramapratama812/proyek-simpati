@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 800px; width: 100%;">

            {{-- Header --}}
            <div class="py-4 text-center text-white"
                style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
                <h3 class="fw-bold mb-0">Biodata Mahasiswa</h3>
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

                {{-- === BIODATA MAHASISWA === --}}
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama</strong><br>{{ $mahasiswa->nama ?? $user->name ?? '-' }}</p>
                        <p><strong>Email</strong><br>{{ $user->email ?? '-' }}</p>
                        <p><strong>NIM</strong><br>{{ $mahasiswa->nim ?? '-' }}</p>
                    </div>

                    <div class="col-md-6">
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

                {{-- Tombol kembali saja --}}
                <div class="text-center">
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS --}}
<style>
body { background-color: #f5f6fa; font-family: 'Poppins', sans-serif; }
.card { border-radius: 1rem; }
.btn-outline-primary { border-color: #2575fc; color: #2575fc; }
.btn-outline-primary:hover { background-color: #2575fc; color: #fff; }
.badge { font-size: 0.9rem; border-radius: 10px; }
</style>
@endsection
