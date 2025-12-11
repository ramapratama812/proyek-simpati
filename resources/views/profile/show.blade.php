@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">

        {{-- ================= HEADER DENGAN STYLING BIRU TUA ================= --}}
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

                {{-- KOLOM KIRI (DATA PRIBADI) --}}
                <div class="col-md-6">

                    {{-- NAMA --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-person-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Nama Lengkap</p>
                            <p class="text-dark fw-bold mb-0">{{ $dosen->nama ?? $user->name ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- EMAIL --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-envelope-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Email</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->email ?? $user->email ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- NOMOR HP --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-phone-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Nomor HP</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->nomor_hp ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- NIDN / NIP --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-qr-code-scan fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">NIDN / NIP</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->nidn ?? $dosen->nip ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (DATA AKADEMIK) --}}
                <div class="col-md-6">

                    {{-- JENIS KELAMIN --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-gender-ambiguous fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Jenis Kelamin</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->jenis_kelamin ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- PENDIDIKAN TERAKHIR --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-mortarboard-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Pendidikan Terakhir</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->pendidikan_terakhir ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- STATUS IKATAN KERJA --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-briefcase-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">Status Ikatan Kerja</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->status_ikatan_kerja ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- STATUS AKTIVITAS --}}
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
                    
                    {{-- SINTA ID (DIPINDAHKAN KE SINI) --}}
                    <div class="bio-box d-flex align-items-center mb-3">
                        <i class="bi bi-globe fs-4 me-3 text-primary"></i>
                        <div>
                            <p class="text-muted small mb-0">SINTA ID</p>
                            <p class="text-dark fw-semibold mb-0">{{ $dosen->sinta_id ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5">

            {{-- ===== LINK PROFIL PDDIKTI (SECTION TERPISAH SEPERTI DI GAMBAR) ===== --}}
            <h4 class="fw-bold mb-4" style="color: #001F4D;">
                <i class="bi bi-link-45deg me-2 text-dark"></i> Profil PDDIKTI
            </h4>
            <div class="d-flex align-items-center mb-4">
                @if(!empty($dosen->link_pddikti))
                    <a href="{{ $dosen->link_pddikti }}"
                        target="_blank"
                        class="btn btn-primary rounded-pill px-4 me-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Profil PDDIKTI
                    </a>
                    
                    @php
                        // Mempersingkat link untuk tampilan info
                        $linkPendek = strlen($dosen->link_pddikti) > 60 
                            ? substr($dosen->link_pddikti, 0, 60).'...'
                            : $dosen->link_pddikti;
                    @endphp
                    <span class="text-muted small d-none d-md-inline" 
                        data-bs-toggle="tooltip" 
                        title="{{ $dosen->link_pddikti }}">
                        Tautan: {{ $linkPendek }}
                    </span>
                @else
                    <p class="text-muted mb-0">Link profil PDDIKTI belum tersedia.</p>
                @endif
            </div>

            <hr class="my-5">

            {{-- ================= KEGIATAN DIKETUAI ================= --}}
            <h4 class="fw-bold mb-3" style="color: #001F4D;">
                <i class="bi bi-person-workspace me-2 text-warning"></i> Kegiatan yang Diketuai
            </h4>

            @forelse($dosen->kegiatanDiketuai ?? [] as $k)
                <div class="kegiatan-box mb-3">
                    <strong>{{ $k->judul }}</strong><br>
                    <span class="text-muted small">Tahun: {{ $k->tanggal ?? $k->tahun_usulan ?? '-' }}</span>
                </div>
            @empty
                <p class="text-muted">Tidak ada kegiatan diketuai.</p>
            @endforelse

            <hr class="my-5">

            {{-- ================= KEGIATAN DIIKUTI ================= --}}
            <h4 class="fw-bold mb-3" style="color: #001F4D;">
                <i class="bi bi-people-fill me-2 text-info"></i> Kegiatan yang Diikuti
            </h4>

            @forelse($dosen->kegiatanDiikuti ?? [] as $ka)
                <div class="kegiatan-box mb-3">
                    <strong>{{ $ka->judul }}</strong><br>
                    <span class="text-muted small">Tahun: {{ $ka->tanggal ?? $ka->tahun_usulan ?? '-' }}</span>
                </div>
            @empty
                <p class="text-muted">Belum mengikuti kegiatan.</p>
            @endforelse

            <hr class="my-5">

            {{-- ================= PUBLIKASI ================= --}}
            <h4 class="fw-bold mb-3" style="color: #001F4D;">
                <i class="bi bi-journal-text me-2 text-danger"></i> Publikasi Terbaru
            </h4>

            @forelse($dosen->publikasi ?? [] as $p)
                <div class="kegiatan-box mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $p->judul }}</strong><br>
                        <span class="text-muted small">Tahun: {{ $p->tahun ?? '-' }}</span>
                    </div>

                    @if($p->file)
                        <a href="{{ asset('storage/'.$p->file) }}"
                            target="_blank"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat File
                        </a>
                    @endif
                </div>
            @empty
                <p class="text-muted">Belum ada publikasi.</p>
            @endforelse

            <hr class="my-5">

            {{-- ================= TOMBOL AKSI ================= --}}
            <div class="d-flex justify-content-between align-items-center">

                <a href="{{ route('dosen.index') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>

                <div class="d-flex gap-2">

                    <a href="{{ route('profile.edit') }}"
                        class="btn text-dark fw-semibold px-4 py-2 rounded-pill btn-edit-profile">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profil
                    </a>

                    <form action="{{ route('profile.destroy') }}" method="POST"
                            onsubmit="return confirm('Yakin ingin hapus akun? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4 py-2">
                            <i class="bi bi-trash-fill me-1"></i> Hapus Akun
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* CSS Baru untuk Header */
.header-dosen {
    /* Gradien warna dasar biru tua */
    background: linear-gradient(135deg, #001F4D 0%, #0a3d62 100%); 
    box-shadow: 0 5px 15px rgba(0, 31, 77, 0.4); 
    border-bottom: 5px solid #ffc107; /* Garis bawah kuning */
    padding-top: 2rem !important;
    padding-bottom: 2rem !important;
}

/* CSS Tambahan/Perbaikan (dipertahankan dari kode Anda) */
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

.kegiatan-box {
    background: white;
    border-radius: 12px;
    padding: 15px 20px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 5px rgba(0,0,0,.04);
}

body {
    background: #f3f4ff;
}

/* Styling untuk tombol kustom */
.btn-primary {
    background-color: #001F4D;
    border-color: #001F4D;
}
.btn-primary:hover {
    background-color: #001533;
    border-color: #001533;
}
.btn-edit-profile {
    background-color: #ffc107; /* Warna kuning */
    border: 1px solid #ffc107;
}
.btn-edit-profile:hover {
    background-color: #e0a800;
    border-color: #e0a800;
}
</style>

<script>
    // Inisialisasi Tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function () {
        // Pastikan variabel bootstrap tersedia di lingkungan Anda
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }
    });
</script>
@endsection