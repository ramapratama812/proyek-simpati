@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">

        {{-- ================= HEADER ================= --}}
        <div class="card-header text-center text-white py-4 header-dosen">
            <i class="bi bi-person-badge-fill display-5 mb-2 d-block"></i>
            <h2 class="fw-bolder mb-0 text-uppercase">Biodata Mahasiswa</h2>
            <p class="text-white-50 small mb-0">
                Informasi Lengkap Mahasiswa (NIM: {{ $mahasiswa->nim ?? '-' }})
            </p>
        </div>

        <div class="card-body px-5 py-5" style="background-color: #fbfbff;">

            {{-- ================= DATA BIODATA ================= --}}
            <div class="row g-4 mb-5">

                {{-- KOLOM KIRI --}}
                <div class="col-md-6">
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-person-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Nama Lengkap</p>
                            <p class="text-dark fw-bold mb-0">{{ $mahasiswa->nama ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-envelope-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Email</p>
                            <p class="text-dark fw-semibold mb-0">
                                {{ $mahasiswa->user->email ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-md-6">
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-qr-code fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">NIM</p>
                            <p class="text-dark fw-semibold mb-0">{{ $mahasiswa->nim ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-gender-ambiguous fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Jenis Kelamin</p>
                            <p class="text-dark fw-semibold mb-0">
                                {{ $mahasiswa->jenis_kelamin ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-lightning-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Status Aktivitas</p>
                            @php
                                $status = strtolower($mahasiswa->status_aktivitas ?? 'aktif');
                            @endphp

                            @if ($status === 'aktif')
                                <span class="badge bg-success px-3 py-2">Aktif</span>
                            @elseif ($status === 'cuti')
                                <span class="badge bg-warning text-dark px-3 py-2">Cuti</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5">

                    {{-- ================= PDDIKTI ================= --}}
                    <h4 class="fw-bold mb-4" style="color: #001F4D;">
                        <i class="bi bi-globe me-2 text-dark"></i> Profil PDDIKTI
                    </h4>
                    @php
                        $nim = $mahasiswa->nim ?? null;
                    @endphp

                    @if($mahasiswa->nim)
                    <a href="https://pddikti.kemdiktisaintek.go.id/search/{{ $mahasiswa->nim }}" target="_blank" class="btn btn-primary">
                        Lihat Profil PDDikti
                    </a>
                    </a>
                        @endif
                        <hr class="my-5">

            {{-- ================= KEGIATAN / PROYEK ================= --}}
            <h4 class="fw-bold mb-3" style="color: #001F4D;">
                <i class="bi bi-people-fill me-2 text-info"></i> Kegiatan / Proyek yang Diikuti
            </h4>

            @php
                $memberships = $mahasiswa->user->projectMembers ?? collect();
            @endphp

            @if ($memberships->isEmpty())
                <p class="text-muted">
                    Mahasiswa ini belum mengikuti kegiatan atau proyek apa pun.
                </p>
            @else
                <ul class="list-group mb-4">
                    @foreach ($memberships as $member)
                        @if ($member->project)
                            <li class="list-group-item">
                                <a href="{{ route('research-projects.show', $member->project->id) }}"
                                   class="fw-bold text-primary text-decoration-none">
                                    {{ $member->project->judul }}
                                </a>

                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-person-badge"></i>
                                    Peran: {{ ucfirst($member->role ?? 'anggota') }}
                                </small>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif

            <hr class="my-5">

            <a href="{{ route('mahasiswa.index') }}"
               class="btn btn-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

        </div>
    </div>
</div>

<style>
.header-dosen {
    background: linear-gradient(135deg, #001F4D 0%, #0a3d62 100%);
    box-shadow: 0 5px 15px rgba(0, 31, 77, 0.4);
    border-bottom: 5px solid #ffc107;
}

.bio-box {
    background: #ffffff;
    border: 1px solid #e6e7ee;
    padding: 12px 16px;
    border-radius: 14px;
    box-shadow: 0 4px 8px rgba(0,0,0,.04);
    transition: transform 0.2s, box-shadow 0.2s;
}

.bio-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,.08);
}
</style>
@endsection
