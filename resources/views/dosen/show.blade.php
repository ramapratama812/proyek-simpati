@extends('layouts.app')
@section('content')
    <div class="container py-5">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">

            {{-- ================= HEADER ================= --}}
            {{-- Pastikan ada class text-center di sini --}}
            <div class="card-header text-center text-white py-4 header-dosen position-relative">

                {{-- ❗ LOGIKA AVATAR/FOTO DI ATAS NAMA (PATH KOREKSI) ❗ --}}
                @php
                    // Asumsi $dosen->nama tersedia di controller
                    $initial = strtoupper(substr($dosen->nama ?? '', 0, 1));
                @endphp

                {{-- Tambahkan d-inline-block untuk memastikan mx-auto berfungsi dengan baik --}}
                <div class="dosen-avatar-wrapper mb-3 d-inline-block">
                    @if ($dosen->foto)
                        {{-- ❗ KOREKSI: $dosen->photo -> $dosen->foto ❗ --}}
                        <img src="{{ asset('storage/' . $dosen->foto) }}" alt="{{ $dosen->nama }}"
                            class="dosen-profile-photo rounded-circle">
                    @else
                        <div
                            class="dosen-profile-initials rounded-circle d-flex align-items-center justify-content-center fw-bold">
                            {{ $initial }}
                        </div>
                    @endif
                </div>

                <h2 class="fw-bolder mb-0 text-uppercase">{{ $dosen->nama ?? 'Nama Dosen' }}</h2>
                <p class="text-white-50 small mb-0">Detail Dosen | NIDN: {{ $dosen->nidn ?? '-' }}</p>
            </div>

            <div class="card-body px-4 px-md-5 py-5" style="background-color: #fbfbff;">

                {{-- NOTIFIKASI --}}
                @if (session('success'))
                    <div class="alert alert-success text-center mb-5 rounded-3 shadow-sm py-2">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ================= SECTION UTAMA: BIODATA & AKSI ================= --}}
                <h4 class="fw-bold mb-4 border-bottom pb-2" style="color: #001F4D;">
                    <i class="bi bi-info-circle-fill me-2 text-primary-dark"></i> Data Personal & Kepegawaian
                </h4>

                <div class="row g-4 mb-5">
                    {{-- KOLOM KIRI (Data Utama) --}}
                    <div class="col-md-6">
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-person-fill fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">Nama Lengkap</p>
                                <p class="text-dark fw-bold mb-0 text-uppercase">{{ $dosen->nama ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-qr-code-scan fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">NIDN / NIP</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->nidn ?? ($dosen->nip ?? '-') }}</p>
                            </div>
                        </div>

                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-mortarboard-fill fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">Pendidikan Terakhir</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->pendidikan_terakhir ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-lightning-charge-fill fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">Status Aktivitas</p>
                                @php $status = $dosen->status_aktivitas ?? ''; @endphp
                                @if ($status === 'Aktif')
                                    <span class="badge status-aktif"><i class="bi bi-check-circle-fill me-1"></i>
                                        Aktif</span>
                                @elseif($status === 'Cuti')
                                    <span class="badge status-cuti"><i class="bi bi-pause-circle-fill me-1"></i> Cuti</span>
                                @elseif($status === 'Tidak Aktif')
                                    <span class="badge status-tidak-aktif"><i class="bi bi-x-circle-fill me-1"></i> Non
                                        Aktif</span>
                                @else
                                    <span class="badge bg-light text-dark border">Belum Diatur</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (Kontak & Ikatan Kerja) --}}
                    <div class="col-md-6">
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-envelope-fill fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">Email</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->email ?? ($user->email ?? '-') }}</p>
                            </div>
                        </div>

                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-phone-fill fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">Nomor HP</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->nomor_hp ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- ID SINTA --}}
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-journal-bookmark-fill fs-4 me-3 text-sinta"></i>
                            <div>
                                <p class="text-muted small mb-0">ID SINTA</p>
                                <p class="text-dark fw-semibold mb-0">
                                    {{ $dosen->id_sinta ?? ($dosen->sinta_id ?? '-') }}
                                </p>
                            </div>
                        </div>

                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-briefcase-fill fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">Status Ikatan Kerja</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->status_ikatan_kerja ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-gender-ambiguous fs-4 me-3 text-primary-dark"></i>
                            <div>
                                <p class="text-muted small mb-0">Jenis Kelamin</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->jenis_kelamin ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- ================= SECTION DOKUMENTASI (TAB) ================= --}}
                <h4 class="fw-bold mb-4 border-bottom pb-2" style="color: #001F4D;">
                    <i class="bi bi-journal-check me-2 text-primary-dark"></i> Riwayat Kegiatan & Publikasi
                </h4>

                {{-- Navigasi Tab --}}
                <ul class="nav nav-tabs custom-tabs mb-4" id="dosenTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="kegiatan-tab" data-bs-toggle="tab" data-bs-target="#kegiatan"
                            type="button" role="tab" aria-controls="kegiatan" aria-selected="true">
                            <i class="bi bi-person-workspace me-1 text-warning"></i> Kegiatan
                            ({{ count($dosen->kegiatanDiketuai ?? []) + count($dosen->anggotaProyek ?? []) }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="publikasi-tab" data-bs-toggle="tab" data-bs-target="#publikasi"
                            type="button" role="tab" aria-controls="publikasi" aria-selected="false">
                            <i class="bi bi-journal-text me-1 text-danger"></i> Publikasi
                            ({{ $dosen->publikasi->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pddikti-tab" data-bs-toggle="tab" data-bs-target="#pddikti"
                            type="button" role="tab" aria-controls="pddikti" aria-selected="false">
                            <i class="bi bi-globe me-1 text-info"></i> Profil PDDIKTI
                        </button>
                    </li>
                </ul>

                {{-- Konten Tab --}}
                <div class="tab-content" id="dosenTabContent">

                    {{-- TAB 1: KEGIATAN --}}
                    <div class="tab-pane fade show active" id="kegiatan" role="tabpanel"
                        aria-labelledby="kegiatan-tab">

                        <h5 class="fw-bold mb-3 mt-3 text-primary-dark">
                            <i class="bi bi-person-fill-gear me-2"></i> Kegiatan yang Diketuai
                        </h5>
                        <div class="scroll-list-container mb-5">
                            @forelse($dosen->kegiatanDiketuai ?? [] as $k)
                                <a href="{{ url('/projects/' . $k->id) }}" class="text-decoration-none text-dark d-block">
                                    <div class="kegiatan-box mb-3 transition-shadow">
                                        <strong>{{ $k->judul }}</strong><br>
                                        <span class="text-muted small">Tahun:
                                            {{ $k->tanggal ?? ($k->tahun_usulan ?? '-') }}</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted">Tidak ada kegiatan yang diketuai.</p>
                            @endforelse
                        </div>

                        <h5 class="fw-bold mb-3 text-primary-dark">
                            <i class="bi bi-people-fill me-2"></i> Kegiatan sebagai Anggota
                        </h5>
                        <div class="scroll-list-container">
                            @forelse($dosen->anggotaProyek ?? [] as $a)
                                @if ($a->project)
                                    <a href="{{ url('/projects/' . $a->project->id) }}"
                                        class="text-decoration-none text-dark d-block">
                                        <div class="kegiatan-box mb-3 transition-shadow">
                                            <strong>{{ $a->project->judul }}</strong><br>
                                            <span class="text-muted small">Ketua:
                                                {{ $a->project->ketua->nama ?? '-' }}</span>
                                        </div>
                                    </a>
                                @endif
                            @empty
                                <p class="text-muted">Belum mengikuti kegiatan sebagai anggota.</p>
                            @endforelse
                        </div>

                    </div>

                    {{-- TAB 2: PUBLIKASI --}}
                    <div class="tab-pane fade" id="publikasi" role="tabpanel" aria-labelledby="publikasi-tab">
                        <div class="scroll-list-container pt-3">
                            @if ($dosen->publikasi->count() > 0)
                                @foreach ($dosen->publikasi as $pub)
                                    <div class="card mb-3 shadow-sm border-0 publication-card">
                                        <div class="card-body py-3">
                                            <p class="mb-1 fw-bold">
                                                {{-- PERBAIKAN DI SINI: Mengubah warna tautan judul menjadi HITAM --}}
                                                <a href="{{ route('publications.show', $pub->id) }}"
                                                    class="text-decoration-none text-dark" style="font-size: 1.05rem;">
                                                    {{ $pub->judul }}
                                                </a>
                                            </p>

                                            <div class="d-flex align-items-center flex-wrap mt-2">
                                                <span class="text-muted small me-3">
                                                    <i class="bi bi-calendar me-1"></i> Tahun: {{ $pub->tahun ?? '-' }}
                                                </span>

                                                <span class="text-muted small me-3">
                                                    <i class="bi bi-tags-fill me-1"></i> Jenis: {{ $pub->jenis ?? '-' }}
                                                </span>

                                                @if ($pub->link ?? false)
                                                    <a href="{{ $pub->link }}" target="_blank"
                                                        class="btn btn-link btn-sm p-0 mt-1 mt-sm-0 text-primary-dark"
                                                        style="font-size: 0.85rem; text-decoration: none;">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> Link Eksternal
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted pt-3">Tidak ada data publikasi yang ditemukan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- TAB 3: PDDIKTI --}}
                    <div class="tab-pane fade" id="pddikti" role="tabpanel" aria-labelledby="pddikti-tab">
                        <h5 class="fw-bold mb-3 mt-3 text-primary-dark">Akses Profil Resmi PDDIKTI</h5>
                        <div class="d-flex align-items-center mb-4 pddikti-box">
                            @if ($dosen->link_pddikti)
                                <a href="{{ $dosen->link_pddikti }}" target="_blank"
                                    class="btn btn-primary rounded-pill px-4 py-2 me-3 pddikti-btn">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Profil PDDIKTI
                                </a>

                                @php
                                    $linkPendek =
                                        strlen($dosen->link_pddikti) > 50
                                            ? substr($dosen->link_pddikti, 0, 50) . '...'
                                            : $dosen->link_pddikti;
                                @endphp

                                <span class="text-muted small d-none d-md-inline" title="{{ $dosen->link_pddikti }}">
                                    Tautan: {{ $linkPendek }}
                                </span>
                            @else
                                <p class="text-muted pt-2">Link profil PDDIKTI belum tersedia untuk dosen ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                {{-- ================= TOMBOL AKSI ================= --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('dosen.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Dosen
                    </a>
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Palet Warna & Font */
        :root {
            --primary-color: #0050a0;
            --primary-dark: #001F4D;
            --secondary-bg: #f7f9fc;
            --border-color: #e6e7ee;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --sinta-color: #6c5ce7;
            /* Warna Ungu untuk SINTA */
            --light-blue: #e8f0ff;
        }

        .text-sinta {
            color: var(--sinta-color) !important;
        }

        /* 1. Header & Card Styling */
        .header-dosen {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #0a3d62 100%);
            box-shadow: 0 5px 15px rgba(0, 31, 77, 0.4);
            border-bottom: 5px solid var(--warning-color);
        }

        .text-primary-dark {
            color: var(--primary-dark) !important;
        }

        /* ❗ AVATAR STYLING (KOREKSI) ❗ */
        .dosen-avatar-wrapper {
            display: inline-block;
        }

        .dosen-profile-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .dosen-profile-initials {
            width: 120px;
            height: 120px;
            background-color: #e8f0ff;
            color: var(--primary-dark);
            font-size: 3rem;
            line-height: 1;
            border: 5px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }


        /* 2. Biodata Boxes */
        .bio-box {
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .bio-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, .1);
        }

        .bio-box .text-primary-dark {
            color: var(--primary-color) !important;
        }

        /* Status Badges (Diperkuat) */
        .badge {
            font-size: 0.8em;
            padding: 0.5em 0.8em;
        }

        .status-aktif {
            background-color: var(--success-color);
            color: #fff;
        }

        .status-cuti {
            background-color: var(--warning-color);
            color: #333;
        }

        .status-tidak-aktif {
            background-color: var(--danger-color);
            color: #fff;
        }

        /* 3. Kegiatan & Publikasi List Items */
        .kegiatan-box,
        .publication-card {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, .04);
            transition: all 0.2s ease-in-out;
        }

        .kegiatan-box:hover,
        .publication-card:hover {
            background-color: var(--secondary-bg);
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
            transform: translateY(-2px);
        }

        .publication-card {
            border-radius: 12px !important;
        }

        /* Scroll List Container */
        .scroll-list-container {
            max-height: 350px;
            overflow-y: auto;
            padding-right: 15px;
        }

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

        /* 4. Custom Tabs Styling */
        .custom-tabs .nav-link {
            color: var(--primary-dark);
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            padding: 10px 15px;
        }

        .custom-tabs .nav-link:hover {
            color: var(--primary-color);
            border-bottom-color: #d0e6ff;
            background-color: #f5f5f5;
        }

        .custom-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background-color: transparent;
        }

        /* PDDIKTI Link Button Smoothness */
        .pddikti-btn,
        .edit-btn-smooth {
            transition: all 0.3s ease;
        }

        .pddikti-btn:hover,
        .edit-btn-smooth:hover {
            box-shadow: 0 4px 10px rgba(0, 80, 160, 0.4);
            transform: translateY(-1px);
        }

        .pddikti-box {
            background: #e8f0ff;
            border: 1px dashed var(--primary-color);
            padding: 20px;
            border-radius: 12px;
        }
    </style>
    {{-- Memastikan Bootstrap JS untuk Tabs berfungsi (asumsi layout.app sudah memuatnya) --}}
    <script>
        // Hanya untuk memastikan tab aktif pada refresh, jika diperlukan
        document.addEventListener('DOMContentLoaded', function() {
            const triggerTabList = document.querySelectorAll('#dosenTab button')
            triggerTabList.forEach(triggerEl => {
                const tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', event => {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });
    </script>
@endsection
