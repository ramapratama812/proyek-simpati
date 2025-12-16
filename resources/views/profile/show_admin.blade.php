@extends('layouts.app')

@section('content')
    <style>
        .page-header-gradient {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        }

        .profile-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            font-size: 2rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 500;
            color: #212529;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ url()->previous() }}" class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle p-3 shadow-sm">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                    <div>
                        <h1 class="fw-bold mb-0">Profil Admin</h1>
                        <div class="text-white-50 small">Kelola informasi akun administrator Anda</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card profile-card">
                        <div class="card-body p-4 p-md-5">
                            @if (session('success'))
                                <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4 mb-4">
                                {{-- Avatar --}}
                                <div class="avatar-circle bg-primary text-white rounded-circle shadow">
                                    {{ substr($user->name, 0, 1) }}
                                </div>

                                {{-- Main Info --}}
                                <div class="text-center text-md-start flex-grow-1">
                                    <h3 class="fw-bold text-dark mb-1">{{ $user->name }}</h3>
                                    <div class="text-muted mb-2">{{ $user->email }}</div>
                                    <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">
                                        <i class="bi bi-shield-check me-1"></i> {{ strtoupper($role) }}
                                    </span>
                                </div>
                            </div>

                            <hr class="my-4 opacity-10">

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 h-100">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                                <i class="bi bi-person-badge fs-5"></i>
                                            </div>
                                            <div class="info-label mb-0">Username</div>
                                        </div>
                                        <div class="info-value ps-5">{{ $user->username ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 h-100">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-success">
                                                <i class="bi bi-activity fs-5"></i>
                                            </div>
                                            <div class="info-label mb-0">Status Akun</div>
                                        </div>
                                        <div class="info-value ps-5">
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-2">
                                                {{ $user->status ?? 'Aktif' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 h-100">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-info">
                                                <i class="bi bi-calendar-check fs-5"></i>
                                            </div>
                                            <div class="info-label mb-0">Terdaftar Sejak</div>
                                        </div>
                                        <div class="info-value ps-5">
                                            {{ optional($user->created_at)->format('d F Y, H:i') ?? '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div
                                        class="p-3 bg-light rounded-3 h-100 d-flex align-items-center justify-content-center text-muted small">
                                        <i class="bi bi-info-circle me-2"></i> Informasi lainnya dapat ditambahkan di sini.
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
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
