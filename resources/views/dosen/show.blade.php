@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">

        {{-- ================= HEADER ================= --}}
        <div class="card-header text-center text-white py-4 header-dosen">
            <i class="bi bi-person-circle display-5 mb-2 d-block"></i>
            <h2 class="fw-bolder mb-0 text-uppercase">Profile Dosen</h2>
            <p class="text-white-50 small mb-0">Informasi Lengkap Dosen (NIDN: {{ $dosen->nidn ?? '-' }})</p>
        </div>

        <div class="card-body px-5 py-5" style="background-color: #fbfbff;">

            {{-- NOTIFIKASI --}}
            @if(session('success'))
                <div class="alert alert-success text-center mb-4 rounded-3 shadow-sm py-2">
                    {{ session('success') }}
                </div>
            @endif

            <h4 class="fw-bold mb-4" style="color: #001F4D;">
                <i class="bi bi-person-lines-fill me-2"></i> Detail Informasi
            </h4>

            {{-- ================= BIODATA ================= --}}
            <div class="row g-4 mb-5">

                {{-- KOLOM KIRI --}}
                <div class="col-md-6">

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-person-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Nama Lengkap</p>
                            <p class="text-dark fw-bold mb-0">{{ $dosen->nama ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-envelope-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Email</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->email ?? $user->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-phone-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Nomor HP</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->nomor_hp ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-qr-code-scan fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">NIDN / NIP</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->nidn ?? $dosen->nip ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-md-6">

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-gender-ambiguous fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Jenis Kelamin</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->jenis_kelamin ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-mortarboard-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Pendidikan Terakhir</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->pendidikan_terakhir ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-briefcase-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Status Ikatan Kerja</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->status_ikatan_kerja ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-lightning-charge-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Status Aktivitas</p>

                            @php $status = $dosen->status_aktivitas ?? ''; @endphp

                            @if($status === 'Aktif')
                                <span class="badge bg-success px-3 py-2">Aktif</span>
                            @elseif($status === 'Cuti')
                                <span class="badge bg-warning text-dark px-3 py-2">Cuti</span>
                            @elseif($status === 'Tidak Aktif')
                                <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                            @else
                                <span class="badge bg-light text-dark px-3 py-2 border">Belum Diatur</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5">

            {{-- ================= LINK PDDIKTI ================= --}}
            <h4 class="fw-bold mb-4" style="color: #001F4D;">
                <i class="bi bi-globe me-2 text-dark"></i> Profil PDDIKTI
            </h4>

            <div class="d-flex align-items-center mb-4">
                @if($dosen->link_pddikti)
                    <a href="{{ $dosen->link_pddikti }}" target="_blank"
                       class="btn btn-primary rounded-pill px-4 me-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Profil PDDIKTI
                    </a>

                    @php
                        $linkPendek = strlen($dosen->link_pddikti) > 60
                            ? substr($dosen->link_pddikti, 0, 60).'...'
                            : $dosen->link_pddikti;
                    @endphp

                    <span class="text-muted small d-none d-md-inline" title="{{ $dosen->link_pddikti }}">
                        Tautan: {{ $linkPendek }}
                    </span>
                @else
                    <p class="text-muted">Link profil PDDIKTI belum tersedia.</p>
                @endif
            </div>

            <hr class="my-5">

            {{-- ================= KEGIATAN DIKETUAI ================= --}}
            <h4 class="fw-bold mb-3" style="color: #001F4D;">
                <i class="bi bi-person-workspace me-2 text-warning"></i> Kegiatan yang Diketuai
            </h4>

            {{-- Tambahkan wadah yang dapat digulir (scrollable container) --}}
            <div class="scroll-list-container">
                @forelse($dosen->kegiatanDiketuai ?? [] as $k)
                    <a href="{{ url('/projects/'.$k->id) }}" class="text-decoration-none text-dark d-block">
                        <div class="kegiatan-box mb-3 transition-shadow">
                            <strong>{{ $k->judul }}</strong><br>
                            <span class="text-muted small">Tahun: {{ $k->tanggal ?? $k->tahun_usulan ?? '-' }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-muted">Tidak ada kegiatan diketuai.</p>
                @endforelse
            </div>

            <hr class="my-5">

            {{-- ================= KEGIATAN DIIKUTI ================= --}}
            <h4 class="fw-bold mb-3" style="color: #001F4D;">
                <i class="bi bi-people-fill me-2 text-info"></i> Kegiatan yang Diikuti
            </h4>

            {{-- Tambahkan wadah yang dapat digulir (scrollable container) --}}
            <div class="scroll-list-container">
                @forelse($dosen->anggotaProyek ?? [] as $a)
                    @if($a->project)
                    <a href="{{ url('/projects/'.$a->project->id) }}" class="text-decoration-none text-dark d-block">
                        <div class="kegiatan-box mb-3 transition-shadow">
                            <strong>{{ $a->project->judul }}</strong><br>
                            <span class="text-muted small">Ketua: {{ $a->project->ketua->nama ?? '-' }}</span>
                        </div>
                    </a>
                    @endif
                @empty
                    <p class="text-muted">Belum mengikuti kegiatan.</p>
                @endforelse
            </div>

            <hr class="my-5">

            {{-- ================= PUBLIKASI TERBARU ================= --}}
            <h4 class="fw-bold mb-3" style="color: #001F4D;">
                <i class="bi bi-journal-text me-2 text-danger"></i> Publikasi Terbaru
            </h4>

            {{-- Tambahkan wadah yang dapat digulir (scrollable container) --}}
            <div class="scroll-list-container">
                @if ($dosen->publikasi->count() > 0)
                    @foreach ($dosen->publikasi as $pub)
                        <div class="card mb-3 shadow-sm border-0 publication-card">
                            <div class="card-body py-3">

                                {{-- JUDUL YANG BISA DIKLIK LANGSUNG --}}
                                <p class="mb-1 fw-bold">
                                    <a href="{{ route('publications.show', $pub->id) }}"
                                        class="text-decoration-none"
                                        style="color: #001F4D;">
                                        {{ $pub->judul }}
                                    </a>
                                </p>

                                <div class="d-flex align-items-center flex-wrap">
                                    <span class="text-muted small me-3">
                                        <i class="bi bi-calendar me-1"></i> **Tahun:** {{ $pub->tahun ?? '-' }}
                                    </span>

                                    <span class="text-muted small me-3">
                                        <i class="bi bi-tags-fill me-1"></i> **Jenis:** {{ $pub->jenis ?? '-' }}
                                    </span>

                                    {{-- Jika ada link publikasi eksternal --}}
                                    @if ($pub->link ?? false)
                                        <a href="{{ $pub->link }}" target="_blank"
                                            class="btn btn-link btn-sm p-0 mt-1 mt-sm-0"
                                            style="font-size: 0.85rem; text-decoration: none;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Link Eksternal
                                        </a>
                                    @endif
                                </div>


                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">Belum ada publikasi.</p>
                @endif
            </div>


            <hr class="my-5">

            {{-- ================= TOMBOL ================= --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('dosen.index') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

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
}
.kegiatan-box {
    background: white;
    border-radius: 12px;
    padding: 15px 20px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 5px rgba(0,0,0,.04);
    transition: all 0.2s ease-in-out; /* Tambahkan transisi */
}
/* Efek hover untuk kegiatan-box */
.kegiatan-box:hover, .publication-card:hover {
    background-color: #f7f9fc;
    border-color: #c0d8f0;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
    transform: translateY(-2px);
}

/* KONTEN YANG BISA DI GULIR */
.scroll-list-container {
    max-height: 350px; /* Batasi tinggi maksimum */
    overflow-y: auto; /* Aktifkan scroll vertikal */
    padding-right: 15px; /* Beri ruang untuk scrollbar */
}

/* Styling untuk scrollbar (hanya bekerja di beberapa browser) */
.scroll-list-container::-webkit-scrollbar {
    width: 6px;
}
.scroll-list-container::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 10px;
}
.scroll-list-container::-webkit-scrollbar-track {
    background-color: #f1f1f1;
    border-radius: 10px;
}

/* Publikasi Card Styling */
.publication-card {
    border-radius: 12px !important;
    transition: all 0.2s ease-in-out;
}
.publication-card .card-body {
    padding: 15px 20px;
}
</style>
@endsection
