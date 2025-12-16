@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="card border-0 rounded-4 overflow-hidden gradient-shadow" style="max-width: 1200px; margin: 0 auto;">

            {{-- ================= HEADER ================= --}}
            <div class="card-header text-center text-white py-4 header-dosen position-relative">
                {{-- Tombol Kembali (Kiri Atas) --}}
                <a href="{{ route('dosen.index') }}"
                    class="btn btn-header-action position-absolute top-0 start-0 m-3 text-decoration-none"
                    data-bs-toggle="tooltip" title="Kembali">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>

                {{-- Tombol Aksi (Kanan Atas) --}}
                <div class="position-absolute top-0 end-0 m-3 d-flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-header-action text-decoration-none"
                        data-bs-toggle="tooltip" title="Edit Profil">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </a>

                    <form action="{{ route('profile.destroy') }}" method="POST"
                        onsubmit="return confirm('Yakin ingin hapus akun? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-header-action btn-header-danger text-decoration-none"
                            data-bs-toggle="tooltip" title="Hapus Akun">
                            <i class="bi bi-trash-fill fs-5"></i>
                        </button>
                    </form>
                </div>

                {{-- ❗ LOGIKA AVATAR/FOTO (UKURAN 120PX) ❗ --}}
                @php
                    // Menggunakan $dosen->nama atau fallback ke $user->name jika diperlukan
                    $initial = strtoupper(substr($dosen->nama ?? $user->name, 0, 1));
                @endphp

                <div class="dosen-avatar-in-header mb-3 mx-auto">
                    @if ($dosen->foto)
                        <img src="{{ asset('storage/' . $dosen->foto) }}" alt="{{ $dosen->nama ?? $user->name }}"
                            class="dosen-profile-photo-in-header rounded-circle">
                    @else
                        <div
                            class="dosen-profile-initials-in-header rounded-circle d-flex align-items-center justify-content-center fw-bold">
                            {{ $initial }}
                        </div>
                    @endif
                </div>
                {{-- ❗ AKHIR LOGIKA AVATAR ❗ --}}

                <h2 class="fw-bolder mb-0 text-uppercase">{{ $dosen->nama ?? ($user->name ?? 'Profile Dosen') }}</h2>
                <p class="text-white-50 small mb-0">
                    Informasi Lengkap Dosen (NIDN: {{ $dosen->nidn ?? ($dosen->nip ?? '-') }})
                </p>
            </div>

            {{-- ================= BODY ================= --}}
            <div class="card-body px-5 py-5" style="background-color: #fbfbff;">

                {{-- Notifikasi sukses --}}
                @if (session('success'))
                    <div class="alert alert-success text-center mb-4 rounded-3 shadow-sm py-2">
                        {{ session('success') }}
                    </div>
                @endif

                <h4 class="fw-bold mb-4" style="color: #001F4D;">
                    <i class="bi bi-person-lines-fill me-2"></i> Detail Informasi
                </h4>

                {{-- ================= BIODATA ================= --}}
                <div class="row g-4 mb-5">
                    {{-- Kolom kiri (data pribadi) --}}
                    <div class="col-md-6">

                        {{-- Nama --}}
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-person-fill fs-4 me-3 text-primary"></i>
                            <div>
                                <p class="text-muted small mb-0">Nama Lengkap</p>
                                <p class="text-dark fw-bold mb-0">{{ $dosen->nama ?? ($user->name ?? '-') }}</p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-envelope-fill fs-4 me-3 text-primary"></i>
                            <div>
                                <p class="text-muted small mb-0">Email</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->email ?? ($user->email ?? '-') }}</p>
                            </div>
                        </div>

                        {{-- Nomor HP --}}
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
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->nidn ?? ($dosen->nip ?? '-') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom kanan (data akademik) --}}
                    <div class="col-md-6">

                        {{-- Jenis Kelamin --}}
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-gender-ambiguous fs-4 me-3 text-primary"></i>
                            <div>
                                <p class="text-muted small mb-0">Jenis Kelamin</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->jenis_kelamin ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Pendidikan Terakhir --}}
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-mortarboard-fill fs-4 me-3 text-primary"></i>
                            <div>
                                <p class="text-muted small mb-0">Pendidikan Terakhir</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->pendidikan_terakhir ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Status Ikatan Kerja --}}
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-briefcase-fill fs-4 me-3 text-primary"></i>
                            <div>
                                <p class="text-muted small mb-0">Status Ikatan Kerja</p>
                                <p class="text-dark fw-semibold mb-0">{{ $dosen->status_ikatan_kerja ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Status Aktivitas --}}
                        <div class="bio-box d-flex align-items-center mb-3">
                            <i class="bi bi-lightning-charge-fill fs-4 me-3 text-primary"></i>
                            <div>
                                <p class="text-muted small mb-0">Status Aktivitas</p>
                                @php $status = $dosen->status_aktivitas ?? ''; @endphp
                                @if ($status === 'Aktif')
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

                {{-- ===== INTEGRASI SISTEM ===== --}}
                <h4 class="fw-bold mb-4" style="color: #001F4D;">
                    <i class="bi bi-link-45deg me-2"></i> Integrasi Sistem
                </h4>

                <div class="row g-4">
                    {{-- SINTA ID --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="background-color: #f8f9fa;">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="bg-white p-3 rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <i class="bi bi-globe fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1 fw-bold text-uppercase">SINTA ID</p>
                                    <h5 class="fw-bolder text-dark mb-0">
                                        {{ $dosen->sinta_id ?? ($user->sinta_id ?? '-') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Google Drive --}}
                    <div class="col-md-6">
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
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning">
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
                                    <a href="{{ route('gdrive.connect') }}"
                                        class="btn btn-primary btn-sm w-100 rounded-pill">
                                        <i class="bi bi-link-45deg me-1"></i> Hubungkan Sekarang
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                {{-- ===== LINK PROFIL PDDIKTI ===== --}}
                <h4 class="fw-bold mb-4" style="color: #001F4D;">
                    <i class="bi bi-link-45deg me-2 text-dark"></i> Profil PDDIKTI
                </h4>
                <div class="d-flex align-items-center mb-4">
                    @if (!empty($dosen->link_pddikti))
                        <a href="{{ $dosen->link_pddikti }}" target="_blank"
                            class="btn btn-primary rounded-pill px-4 me-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Profil PDDIKTI
                        </a>

                        @php
                            $fullLink = $dosen->link_pddikti;
                            $linkPendek = strlen($fullLink) > 60 ? substr($fullLink, 0, 60) . '...' : $fullLink;
                        @endphp
                        <span class="text-muted small d-none d-md-inline" data-bs-toggle="tooltip"
                            title="{{ $fullLink }}">
                            Tautan: {{ $linkPendek }}
                        </span>
                    @else
                        <p class="text-muted mb-0">Link profil PDDIKTI belum tersedia.</p>
                    @endif
                </div>

                <hr class="my-5">

                {{-- ================= KEGIATAN DIKETUAI ================= --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color: #001F4D;">
                        <i class="bi bi-person-workspace me-2 text-warning"></i> Kegiatan yang Diketuai
                    </h4>
                    <div class="btn-group" role="group" aria-label="Filter Kegiatan Diketuai">
                        <button type="button" class="btn btn-outline-primary btn-sm active"
                            onclick="filterList('diketuai', 'all', this)">Semua</button>
                        <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="filterList('diketuai', 'Penelitian', this)">Penelitian</button>
                        <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="filterList('diketuai', 'Pengabdian', this)">Pengabdian</button>
                    </div>
                </div>

                <div id="list-diketuai" class="scrollable-list">
                    @php
                        $kegiatanDiketuai = $dosen->kegiatanDiketua ?? ($dosen->kegiatanDiketuai ?? collect());
                    @endphp
                    @forelse($kegiatanDiketuai as $k)
                        @if ($k->validation_status == 'approved')
                            <a href="{{ route('projects.show', $k->id) }}"
                                class="text-decoration-none text-dark item-kegiatan" data-jenis="{{ $k->jenis }}">
                                <div class="kegiatan-box mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $k->judul ?? '-' }}</strong>
                                        <span
                                            class="badge badge-jenis {{ strtolower($k->jenis) == 'penelitian' ? 'badge-penelitian' : 'badge-pengabdian' }}">
                                            {{ $k->jenis }}
                                        </span>
                                    </div>
                                    <span class="text-muted small d-block mb-2">
                                        Tahun: {{ $k->tanggal ?? ($k->tahun_usulan ?? '-') }}
                                    </span>
                                    {{-- Peserta --}}
                                    <div class="small text-muted border-top pt-2 mt-2">
                                        <i class="bi bi-people me-1"></i> Peserta:
                                        @if ($k->members && $k->members->count() > 0)
                                            {{ $k->members->pluck('name')->join(', ') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endif
                    @empty
                        <p class="text-muted">Tidak ada kegiatan diketuai yang disetujui.</p>
                    @endforelse
                </div>

                <hr class="my-5">

                {{-- ================= KEGIATAN DIIKUTI ================= --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color: #001F4D;">
                        <i class="bi bi-people-fill me-2 text-info"></i> Kegiatan yang Diikuti
                    </h4>
                    <div class="btn-group" role="group" aria-label="Filter Kegiatan Diikuti">
                        <button type="button" class="btn btn-outline-primary btn-sm active"
                            onclick="filterList('diikuti', 'all', this)">Semua</button>
                        <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="filterList('diikuti', 'Penelitian', this)">Penelitian</button>
                        <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="filterList('diikuti', 'Pengabdian', this)">Pengabdian</button>
                    </div>
                </div>

                <div id="list-diikuti" class="scrollable-list">
                    @php
                        $kegiatanDiikuti = $dosen->kegiatanDiikuti ?? ($dosen->anggotaProyek ?? collect());
                    @endphp
                    @forelse($kegiatanDiikuti as $ka)
                        @php
                            $project = $ka->project ?? $ka;
                        @endphp
                        @if ($project->validation_status == 'approved')
                            <a href="{{ route('projects.show', $project->id) }}"
                                class="text-decoration-none text-dark item-kegiatan" data-jenis="{{ $project->jenis }}">
                                <div class="kegiatan-box mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $project->judul ?? '-' }}</strong>
                                        <span
                                            class="badge badge-jenis {{ strtolower($project->jenis) == 'penelitian' ? 'badge-penelitian' : 'badge-pengabdian' }}">
                                            {{ $project->jenis }}
                                        </span>
                                    </div>
                                    <span class="text-muted small d-block mb-2">
                                        Tahun: {{ $project->tanggal ?? ($project->tahun_usulan ?? '-') }}
                                    </span>
                                    {{-- Peserta --}}
                                    <div class="small text-muted border-top pt-2 mt-2">
                                        <i class="bi bi-people me-1"></i> Peserta:
                                        @if ($project->members && $project->members->count() > 0)
                                            {{ $project->members->pluck('name')->join(', ') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endif
                    @empty
                        <p class="text-muted">Belum mengikuti kegiatan yang disetujui.</p>
                    @endforelse
                </div>

                <hr class="my-5">

                {{-- ================= PUBLIKASI ================= --}}
                <h4 class="fw-bold mb-3" style="color: #001F4D;">
                    <i class="bi bi-journal-text me-2 text-danger"></i> Publikasi Terbaru
                </h4>
                <div class="scrollable-list">
                    @forelse($dosen->publikasi ?? [] as $p)
                        @if ($p->validation_status == 'approved')
                            <a href="{{ route('publications.show', $p->id) }}" class="text-decoration-none text-dark">
                                <div class="kegiatan-box mb-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $p->judul }}</strong><br>
                                        <span
                                            class="text-primary small fw-semibold">{{ $p->jurnal ?? 'Jurnal Tidak Diketahui' }}</span><br>
                                        <span class="text-muted small">Tahun: {{ $p->tahun ?? '-' }}</span>
                                    </div>

                                    @if ($p->file)
                                        <object class="d-none">
                                            {{-- Dummy object to prevent link nesting issue if needed, but here we just want the button to work --}}
                                        </object>
                                        <span class="badge bg-secondary">Approved</span>
                                    @endif
                                </div>
                            </a>
                        @endif
                    @empty
                        <p class="text-muted">Belum ada publikasi yang disetujui.</p>
                    @endforelse
                </div>

                <hr class="my-5">

            </div>
        </div>
    </div>

    {{-- STYLE --}}
    <style>
        body {
            background-color: #f5f6fa;
        }

        .header-dosen {
            background: linear-gradient(135deg, #001F4D 0%, #0a3d62 100%);
            box-shadow: 0 5px 15px rgba(0, 31, 77, 0.4);
            border-bottom: 5px solid #ffc107;
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
            position: relative;
            /* Added position relative */
        }

        /* ❗ KOREKSI UKURAN AVATAR/INISIAL DI HEADER ❗ */
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
            background-color: #e8f0ff;
            color: #001F4D;
            font-size: 3rem;
            line-height: 1;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Header Action Buttons */
        .btn-header-action {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-header-action:hover {
            background: #fff;
            color: #001F4D;
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            border-color: #fff;
        }

        .btn-header-danger:hover {
            background: #ff4d4d;
            color: #fff;
            border-color: #ff4d4d;
        }

        .bio-box {
            background: #ffffff;
            border: 1px solid #e6e7ee;
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .04);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .bio-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, .08);
        }

        .kegiatan-box {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .04);
            transition: all 0.2s ease;
        }

        .kegiatan-box:hover {
            border-color: #001F4D;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
        }

        label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #6c757d;
        }

        span.fw-semibold {
            font-size: 1rem;
            color: #212121;
        }

        .badge {
            border-radius: 8px;
            font-size: 0.85rem;
        }

        /* Badge Jenis Kegiatan yang lebih rapi */
        .badge-jenis {
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            /* Optional: agar lebar badge seragam */
        }

        .badge-penelitian {
            background-color: #e3f2fd;
            /* Light Blue */
            color: #0d47a1;
            border: 1px solid #bbdefb;
        }

        .badge-pengabdian {
            background-color: #f3e5f5;
            /* Light Purple */
            color: #4a148c;
            border: 1px solid #e1bee7;
        }

        .btn-animated {
            transition: all 0.25s ease-in-out;
        }

        .btn-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-primary {
            border-width: 2px;
        }

        .btn-outline-primary:hover {
            background-color: #001F4D;
            color: #fff;
            border-color: #001F4D;
        }

        .btn-outline-danger:hover {
            background-color: #c62828;
            color: #fff;
            border-color: #c62828;
        }

        .gradient-shadow {
            box-shadow:
                0 10px 30px rgba(0, 31, 77, 0.2),
                0 20px 50px rgba(0, 53, 128, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .gradient-shadow:hover {
            box-shadow:
                0 15px 45px rgba(0, 31, 77, 0.25),
                0 25px 70px rgba(0, 53, 128, 0.15);
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: #001F4D;
            border-color: #001F4D;
        }

        .btn-primary:hover {
            background-color: #001533;
            border-color: #001533;
        }

        .btn-edit-profile {
            background-color: #ffc107;
            border: 1px solid #ffc107;
        }

        .btn-edit-profile:hover {
            background-color: #e0a800;
            border-color: #e0a800;
        }

        .scrollable-list {
            max-height: 400px;
            /* Approx 3-4 items depending on height */
            overflow-y: auto;
            padding-right: 5px;
        }

        /* Custom Scrollbar */
        .scrollable-list::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .scrollable-list::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .scrollable-list::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
    </style>

    <script>
        // Inisialisasi Tooltips Bootstrap untuk link PDDIKTI
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });

        function filterList(listType, filterJenis, btn) {
            // Update active button state
            let btnGroup = btn.parentElement;
            let buttons = btnGroup.querySelectorAll('.btn');
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Filter items (Case Insensitive)
            let listContainer = document.getElementById('list-' + listType);
            let items = listContainer.querySelectorAll('.item-kegiatan');
            let filterLower = filterJenis.toLowerCase();

            items.forEach(item => {
                let jenis = (item.getAttribute('data-jenis') || '').toLowerCase();

                if (filterLower === 'all' || jenis === filterLower) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
@endsection
