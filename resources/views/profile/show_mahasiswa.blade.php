@extends('layouts.app')

@section('content')
    {{--
    👇 PERBAIKAN: Ganti domain PDDikti yang lebih umum (kemdikbud.go.id)
    DAN set variabel $isProfileView = true (karena ini halaman profile sendiri)
--}}
    @php
        $pddiktiUrl =
            $mahasiswa->nim ?? false
                ? 'https://pddikti.kemdiktisaintek.go.id/search/mahasiswa/' . $mahasiswa->nim
                : null;

        // Variabel ini harus dikirim dari controller (method profile()), tapi kita set default agar tombol tetap muncul
        $isProfileView = true;
    @endphp

    <div class="container py-5">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; margin: 0 auto;">

            {{-- ================= HEADER (Style Dosen) ================= --}}
            <div class="card-header text-center text-white py-4 header-dosen position-relative">

                {{-- Avatar Logic --}}
                @php
                    $initial = strtoupper(substr($mahasiswa->nama ?? $user->name, 0, 1));
                @endphp

                <div class="dosen-avatar-in-header mb-3 mx-auto">
                    @if ($user->foto)
                        <img src="{{ asset($user->foto) }}" alt="{{ $mahasiswa->nama ?? $user->name }}"
                            class="dosen-profile-photo-in-header rounded-circle">
                    @else
                        <div
                            class="dosen-profile-initials-in-header rounded-circle d-flex align-items-center justify-content-center fw-bold">
                            {{ $initial }}
                        </div>
                    @endif
                </div>

                <h2 class="fw-bolder mb-0 text-uppercase">{{ $mahasiswa->nama ?? $user->name }}</h2>
                <p class="text-white-50 small mb-0">Profil Mahasiswa | NIM: {{ $mahasiswa->nim ?? '-' }}</p>
            </div>

            <div class="card-body px-4 px-md-5 py-5" style="background-color: #fbfbff;">

                @if (session('success'))
                    <div class="alert alert-success text-center mb-5 rounded-3 shadow-sm py-2">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ================= 1. DATA PERSONAL & AKADEMIK ================= --}}
                <h4 class="fw-bold mb-4 border-bottom pb-2 text-primary-dark">
                    <i class="bi bi-info-circle-fill me-2 text-primary-dark"></i> Data Personal & Akademik
                </h4>

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-person-fill fs-4 me-3 text-primary-color"></i>
                            <p class="text-muted small mb-0">Nama Lengkap</p>
                            <p class="text-dark fw-bold mb-0 text-uppercase">{{ $mahasiswa->nama ?? $user->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-envelope-fill fs-4 me-3 text-primary-color"></i>
                            <p class="text-muted small mb-0">Email</p>
                            <p class="text-dark fw-semibold mb-0">{{ $user->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-card-heading fs-4 me-3 text-primary-color"></i>
                            <p class="text-muted small mb-0">NIM</p>
                            <p class="text-dark fw-semibold mb-0">{{ $mahasiswa->nim ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-mortarboard-fill fs-4 me-3 text-primary-color"></i>
                            <p class="text-muted small mb-0">Semester</p>
                            <p class="text-dark fw-semibold mb-0">{{ $mahasiswa->semester ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-book-half fs-4 me-3 text-primary-color"></i>
                            <p class="text-muted small mb-0">Program Studi</p>
                            <p class="text-dark fw-semibold mb-0">{{ $mahasiswa->program_studi ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-building fs-4 me-3 text-primary-color"></i>
                            <p class="text-muted small mb-0">Perguruan Tinggi</p>
                            <p class="text-dark fw-semibold mb-0">{{ $mahasiswa->perguruan_tinggi ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-gender-ambiguous fs-4 me-3 text-primary-color"></i>
                            <p class="text-muted small mb-0">Jenis Kelamin</p>
                            <p class="text-dark fw-semibold mb-0">{{ $mahasiswa->jenis_kelamin ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bio-box bio-box-smooth">
                            <i class="bi bi-lightning-charge-fill fs-4 me-3 text-primary-color"></i>
                            <div>
                                <p class="text-muted small mb-0">Status Aktivitas</p>
                                @php $status = $mahasiswa->status_aktivitas ?? ''; @endphp
                                @if ($status === 'Aktif')
                                    <span class="badge status-aktif">Aktif</span>
                                @elseif($status === 'Cuti')
                                    <span class="badge status-cuti">Cuti</span>
                                @elseif($status === 'Tidak Aktif' || $status === 'Non-Aktif')
                                    <span class="badge status-tidak-aktif">Non Aktif</span>
                                @elseif($status === 'Lulus')
                                    <span class="badge bg-info text-dark">Lulus</span>
                                @elseif($status === 'Keluar')
                                    <span class="badge bg-danger text-white">Keluar (DO)</span>
                                @else
                                    <span class="badge status-default">Belum Diatur</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                {{-- ===== INTEGRASI SISTEM ===== --}}
                <h4 class="fw-bold mb-4" style="color: #001F4D;">
                    <i class="bi bi-link-45deg me-2"></i> Integrasi Sistem
                </h4>

                {{-- Google Drive --}}
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm h-100" style="background-color: #f8f9fa;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-white p-3 rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg"
                                        alt="Google Drive" style="width: 32px; height: 32px;">
                                </div>
                                <div>
                                    <p class="text-muted small mb-1 fw-bold text-uppercase">Google Drive</p>
                                    @if (auth()->user()->google_refresh_token)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Terhubung
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="bi bi-exclamation-circle me-1"></i> Belum Terhubung
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if (auth()->user()->google_refresh_token)
                                <a href="https://drive.google.com/drive/my-drive" target="_blank"
                                    class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Kelola Drive
                                </a>
                            @else
                                <a href="{{ route('gdrive.connect') }}" class="btn btn-primary btn-sm w-100 rounded-pill">
                                    <i class="bi bi-link-45deg me-1"></i> Hubungkan Sekarang
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="mb-5">

                {{-- ================= 2. PDDIKTI ================= --}}
                <h4 class="fw-bold mb-3 text-primary-dark">Akses Profil Resmi PDDIKTI</h4>
                <div class="d-flex align-items-center mb-5 pddikti-box">
                    @if ($pddiktiUrl)
                        <a href="{{ $pddiktiUrl }}" target="_blank"
                            class="btn btn-primary rounded-pill px-4 py-2 me-3 pddikti-btn btn-smooth-action">
                            <i class="bi bi-search me-1"></i> Cari Data di PDDIKTI
                        </a>
                        <span class="text-muted small d-none d-md-inline">
                            Klik tombol untuk mencari data Mahasiswa di <strong>PDDikti</strong>
                        </span>
                    @else
                        <p class="text-muted pt-2 mb-0">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Link pencarian tidak tersedia (NIM Kosong).
                        </p>
                    @endif
                </div>

                <hr class="mb-5">

                {{-- ================= 3. KEGIATAN YANG DIIKUTI ================= --}}
                <h4 class="fw-bold mb-4 border-bottom pb-2 text-primary-dark">
                    <i class="bi bi-person-workspace me-2 text-primary-dark"></i> Kegiatan yang Diikuti
                </h4>

                <div class="scroll-list-container mb-5">
                    @php
                        $memberships = $user->projectMembers ?? collect();
                    @endphp

                    @forelse($memberships as $member)
                        @if ($member->project)
                            <a href="{{ route('projects.show', $member->project->id) }}"
                                class="text-decoration-none text-dark d-block">
                                <div class="kegiatan-box mb-3 transition-shadow">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="d-block mb-1 text-primary-dark" style="font-size: 1.05rem;">
                                                {{ $member->project->judul }}
                                            </strong>
                                            <div class="text-muted small">
                                                <i class="bi bi-person-badge me-1"></i> Peran:
                                                <span
                                                    class="fw-semibold text-dark">{{ ucfirst($member->role ?? 'Anggota') }}</span>
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-calendar-event me-1"></i> Tahun:
                                                {{ $member->project->tanggal ?? ($member->project->tahun_usulan ?? '-') }}
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-secondary border">Detail <i
                                                class="bi bi-chevron-right ms-1"></i></span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @empty
                        <div class="text-center py-4 text-muted bg-white rounded-3 border border-light-subtle">
                            <i class="bi bi-inbox fs-4 d-block mb-2 opacity-50"></i>
                            Belum ada kegiatan yang diikuti.
                        </div>
                    @endforelse
                </div>

                <hr class="mb-4">

                {{-- ================= AKSI (KEMBALI + HANYA EDIT/HAPUS JIKA $isProfileView = true) ================= --}}
                <div class="d-flex justify-content-between align-items-center">

                    {{-- TOMBOL KEMBALI --}}
                    <a href="{{ route('mahasiswa.index') }}"
                        class="btn btn-secondary rounded-pill px-4 btn-smooth-action btn-back">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>

                    {{-- ❗ BLOK TOMBOL EDIT & HAPUS (MUNCUL KARENA $isProfileView = true) ❗ --}}
                    @if ($isProfileView)
                        <div class="d-flex gap-2">
                            @php
                                // Di profile.show, tombol selalu mengarah ke edit/destroy profile
                                $editRoute = route('profile.edit');
                                $deleteRoute = route('profile.destroy');
                            @endphp

                            {{-- Tombol Edit --}}
                            <a href="{{ $editRoute }}"
                                class="btn text-white fw-semibold px-4 py-2 rounded-pill btn-edit-profile btn-smooth-action">
                                <i class="bi bi-pencil-square me-1"></i> Edit Profil
                            </a>

                            {{-- Tombol Hapus --}}
                            <form action="{{ $deleteRoute }}" method="POST"
                                onsubmit="return confirm('Yakin ingin hapus akun ini? Data tidak bisa dikembalikan.')"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-outline-danger rounded-pill px-4 py-2 btn-smooth-action btn-delete-account">
                                    <i class="bi bi-trash-fill me-1"></i> Hapus Akun
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- ❗ AKHIR BLOK TOMBOL EDIT & HAPUS ❗ --}}

                </div>

            </div>
        </div>
    </div>

    <style>
        /* =========================================
                               STYLE SAMA PERSIS DENGAN DOSEN (FINAL)
                               ========================================= */

        :root {
            --primary-color: #0050a0;
            --primary-dark: #001F4D;
            --secondary-bg: #f7f9fc;
            --border-color: #e6e7ee;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-blue: #e8f0ff;
        }

        /* Global Transition */
        .btn-smooth-action,
        .bio-box,
        .pddikti-box a.btn,
        .kegiatan-box {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .text-primary-color {
            color: var(--primary-color) !important;
        }

        .text-primary-dark {
            color: var(--primary-dark) !important;
        }

        /* 1. Header Styling */
        .header-dosen {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #0a3d62 100%);
            box-shadow: 0 5px 15px rgba(0, 31, 77, 0.4);
            border-bottom: 5px solid var(--warning-color);
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }

        /* 2. Avatar Styling */
        .dosen-avatar-in-header {
            display: inline-block;
        }

        .dosen-profile-photo-in-header {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .dosen-profile-initials-in-header {
            width: 120px;
            height: 120px;
            background-color: var(--light-blue);
            color: var(--primary-dark);
            font-size: 3rem;
            line-height: 1;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* 3. Bio Box Styling */
        .bio-box {
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 15px 20px;
            border-radius: 14px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .04);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .bio-box i {
            align-self: flex-start;
            margin-bottom: 5px;
        }

        .bio-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
        }

        /* 4. Kegiatan Box Styling */
        .kegiatan-box {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, .04);
            cursor: pointer;
        }

        .kegiatan-box:hover {
            background-color: var(--secondary-bg);
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
            transform: translateY(-2px);
        }

        /* Status Badges */
        .badge {
            font-size: 0.8em;
            padding: 0.5em 0.8em;
            border-radius: 50px;
            font-weight: 600;
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

        .status-default {
            background-color: #ccc;
            color: #555;
            border: none;
        }

        /* PDDikti Box */
        .pddikti-box {
            background: #e8f0ff;
            border: 1px dashed var(--primary-color);
            padding: 20px;
            border-radius: 12px;
        }

        /* Buttons */
        .btn-edit-profile {
            background-color: var(--primary-color);
            color: white !important;
            border: 1px solid var(--primary-dark);
            box-shadow: 0 4px 10px rgba(0, 80, 160, 0.2);
        }

        .btn-edit-profile:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-delete-account {
            border-color: var(--danger-color);
            color: var(--danger-color) !important;
        }

        .btn-delete-account:hover {
            background-color: var(--danger-color);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-back:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        /* Scroll List */
        .scroll-list-container {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 5px;
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
        }
    </style>
@endsection
