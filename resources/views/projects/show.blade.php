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

        .detail-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .detail-card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }

        .detail-card-title {
            font-weight: 700;
            margin-bottom: 0;
            color: #0a58ca;
            display: flex;
            align-items: center;
        }

        .detail-card-body {
            padding: 1.5rem;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 500;
            color: #212529;
            margin-bottom: 1rem;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        .doc-img-wrapper {
            position: relative;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            aspect-ratio: 4/3;
        }

        .doc-img-wrapper:hover {
            transform: scale(1.02);
        }

        .doc-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
        }

        .delete-btn-overlay {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .doc-img-wrapper:hover .delete-btn-overlay {
            opacity: 1;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="mb-3">
                    <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill me-2">
                        <i
                            class="bi {{ strtolower($project->jenis) == 'penelitian' ? 'bi-journal-bookmark' : 'bi-people' }} me-1"></i>
                        {{ ucfirst($project->jenis) }}
                    </span>
                    @include('projects._validation_badge', ['project' => $project])
                </div>
                <h1 class="fw-bold mb-3">{{ $project->judul }}</h1>
                <div class="d-flex align-items-center text-white-50">
                    <div class="d-flex align-items-center me-4">
                        <i class="bi bi-person-circle me-2"></i>
                        <span>{{ optional($project->ketua)->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-event me-2"></i>
                        <span>{{ $project->tahun_usulan ?? ($project->mulai?->format('Y') ?? '-') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row g-4">
                {{-- Main Content --}}
                <div class="col-lg-8">

                    {{-- Overview --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-file-text me-2"></i> Abstrak & Informasi Umum</h5>
                        </div>
                        <div class="detail-card-body">
                            <div class="mb-4">
                                <div class="info-label">Abstrak</div>
                                <p class="text-muted" style="text-align: justify;">{{ $project->abstrak }}</p>
                            </div>
                            @if ($project->keywords)
                                <div>
                                    <div class="info-label">Kata Kunci</div>
                                    <div>
                                        @foreach (explode(',', $project->keywords) as $keyword)
                                            <span
                                                class="badge bg-light text-dark border me-1 mb-1">{{ trim($keyword) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Details Grid --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-grid-3x3-gap me-2"></i> Detail Kegiatan</h5>
                        </div>
                        <div class="detail-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Skema</div>
                                    <div class="info-value">{{ $project->skema }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Kategori</div>
                                    <div class="info-value">{{ $project->kategori_kegiatan }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Bidang Ilmu</div>
                                    <div class="info-value">{{ $project->bidang_ilmu }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">TKT</div>
                                    <div class="info-value">{{ $project->tkt ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Periode Pelaksanaan</div>
                                    <div class="info-value">
                                        {{ $project->mulai ? $project->mulai->format('d M Y') : '-' }} —
                                        {{ $project->selesai ? $project->selesai->format('d M Y') : '-' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Status Pelaksanaan</div>
                                    <div class="info-value">
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                            {{ $project->status ?? 'Belum ditentukan' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <hr class="text-muted opacity-25 my-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Biaya Disetujui</div>
                                    <div class="info-value text-success fw-bold">Rp
                                        {{ number_format($project->biaya, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Sumber Dana</div>
                                    <div class="info-value">{{ $project->sumber_dana }}</div>
                                </div>
                                @if ($project->mitra_nama)
                                    <div class="col-12">
                                        <div class="info-label">Mitra / Instansi</div>
                                        <div class="info-value">{{ $project->mitra_nama }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Outputs & Links --}}
                    @if ($project->target_luaran || $project->tautan)
                        <div class="card detail-card">
                            <div class="detail-card-header">
                                <h5 class="detail-card-title"><i class="bi bi-bullseye me-2"></i> Luaran & Tautan</h5>
                            </div>
                            <div class="detail-card-body">
                                @if ($project->target_luaran)
                                    <div class="mb-3">
                                        <div class="info-label">Target Luaran</div>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($project->target_luaran as $luaran)
                                                <li class="list-group-item px-0 py-2 border-bottom-0"><i
                                                        class="bi bi-check2-circle text-success me-2"></i>
                                                    {{ $luaran }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if ($project->tautan)
                                    <div>
                                        <div class="info-label">Tautan Pendukung</div>
                                        <a href="{{ $project->tautan }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm mt-1">
                                            <i class="bi bi-link-45deg me-1"></i> Buka Tautan
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Team Members --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-people me-2"></i> Tim Pelaksana</h5>
                        </div>
                        <div class="detail-card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle bg-primary-subtle text-primary">K</div>
                                <div>
                                    <div class="fw-bold">{{ optional($project->ketua)->name ?? '-' }}</div>
                                    <div class="text-muted small">Ketua Pelaksana</div>
                                </div>
                            </div>
                            @if ($project->members && $project->members->count())
                                @foreach ($project->members as $m)
                                    @if ($m->pivot && $m->pivot->peran === 'anggota')
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-circle">A</div>
                                            <div>
                                                <div class="fw-bold">{{ $m->name }}</div>
                                                <div class="text-muted small">Anggota</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Documentation --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-images me-2"></i> Dokumentasi Kegiatan</h5>
                        </div>
                        <div class="detail-card-body">
                            @if (($project->images && $project->images->count()) || ($project->media && $project->media->count()))
                                <div class="row g-3">
                                    @foreach ($project->images as $img)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="doc-img-wrapper">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    data-bs-img-src="{{ asset('storage/' . $img->path) }}">
                                                    <img src="{{ asset('storage/' . $img->path) }}" class="doc-img">
                                                </a>
                                                @if (auth()->id() && auth()->id() == $project->ketua_id)
                                                    <form class="delete-btn-overlay" method="POST"
                                                        action="{{ route('projects.images.destroy', [$project, $img]) }}"
                                                        onsubmit="return confirm('Hapus gambar ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm"><i
                                                                class="bi bi-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Google Drive Images --}}
                                    @foreach ($project->media as $media)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="doc-img-wrapper">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    data-bs-img-src="{{ route('projects.media.proxy', $media->id) }}">
                                                    <img src="{{ route('projects.media.proxy', $media->id) }}"
                                                        class="doc-img" referrerpolicy="no-referrer" loading="lazy"
                                                        onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Preview'; this.parentElement.href='{{ $media->web_view_link }}'; this.parentElement.target='_blank';">
                                                </a>
                                                {{-- Delete button for media (Optional, requires route) --}}
                                                @if (auth()->id() && auth()->id() == $project->ketua_id)
                                                    <form class="delete-btn-overlay" method="POST"
                                                        action="{{ route('projects.media.destroy', [$project, $media->id]) }}"
                                                        onsubmit="return confirm('Hapus dokumentasi ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm"><i
                                                                class="bi bi-trash"></i></button>
                                                    </form>
                                                @endif

                                                <div class="position-absolute bottom-0 start-0 m-2">
                                                    <span class="badge bg-light text-dark border shadow-sm">
                                                        <i class="bi bi-google"></i> Drive
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-image fs-1 opacity-25 mb-2 d-block"></i>
                                    <p class="mb-0">Belum ada dokumentasi yang diunggah.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">

                    {{-- Validation Status --}}
                    <div class="card detail-card">
                        <div class="detail-card-body">
                            <h6 class="fw-bold mb-3 text-uppercase text-muted small">Status Validasi</h6>
                            <div class="mb-3">
                                @include('projects._validation_badge', ['project' => $project])
                            </div>

                            @if ($project->validation_note)
                                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis small mb-0">
                                    <i class="bi bi-info-circle-fill me-1"></i>
                                    <strong>Catatan Admin:</strong><br>
                                    {{ $project->validation_note }}
                                </div>
                            @endif

                            @if ($project->surat_persetujuan)
                                <div class="mt-3 pt-3 border-top">
                                    <a href="{{ asset('storage/' . $project->surat_persetujuan) }}" target="_blank"
                                        class="d-flex align-items-center text-decoration-none text-success">
                                        <i class="bi bi-file-earmark-check-fill fs-4 me-2"></i>
                                        <div class="lh-1">
                                            <div class="fw-bold">Surat Persetujuan</div>
                                            <small class="text-muted">Klik untuk melihat</small>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="card detail-card">
                        <div class="detail-card-body">
                            <h6 class="fw-bold mb-3 text-uppercase text-muted small">Aksi</h6>

                            <div class="d-grid gap-2 mb-3">
                                <a href="{{ route('projects.publications.index', $project) }}"
                                    class="btn btn-primary fw-semibold">
                                    <i class="bi bi-journal-text me-2"></i> Lihat Publikasi
                                </a>
                                @if ($project->surat_proposal)
                                    <a href="{{ asset('storage/' . $project->surat_proposal) }}" target="_blank"
                                        class="btn btn-outline-secondary">
                                        <i class="bi bi-file-earmark-pdf me-2"></i> Proposal
                                    </a>
                                @endif
                            </div>

                            @php
                                $userId = auth()->id();
                                $isKetuaOrCreator =
                                    $userId && ($userId == $project->ketua_id || $userId == $project->created_by);
                            @endphp

                            @if ($isKetuaOrCreator && in_array($project->validation_status, ['draft', 'revision_requested']))
                                <form method="POST" action="{{ route('projects.submitValidation', $project) }}"
                                    class="mb-3"
                                    onsubmit="return confirm('Ajukan kegiatan ini untuk divalidasi admin?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-send me-2"></i> Ajukan Validasi
                                    </button>
                                </form>
                            @endif

                            @if ($isKetuaOrCreator && $project->validation_status !== 'approved')
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <a href="{{ route('projects.edit', $project) }}"
                                            class="btn btn-outline-warning w-100">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                            onsubmit="return confirm('Hapus kegiatan ini? Tindakan tidak bisa dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100">
                                                <i class="bi bi-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <hr>
                            <a href="{{ route('projects.index') }}" class="btn btn-light w-100 text-muted">
                                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
                            </a>
                        </div>
                    </div>

                    {{-- Add Documentation (Participant Only) --}}
                    @php
                        $userId = auth()->id();
                        $isParticipant =
                            $userId && ($userId == $project->ketua_id || $project->members->contains('id', $userId));
                    @endphp

                    @if ($isParticipant)
                        <div class="card detail-card mb-4">
                            <div class="detail-card-body p-4">
                                <h6 class="fw-bold mb-3 text-uppercase text-primary d-flex align-items-center">
                                    <i class="bi bi-cloud-plus me-2"></i> Tambah Dokumentasi
                                </h6>
                                <form method="POST" action="{{ route('projects.images.store', $project) }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-3">
                                        <!-- Local Upload -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Upload dari
                                                Perangkat</label>
                                            <input type="file" name="images[]" class="form-control" multiple
                                                accept="image/*">
                                            <div class="form-text small text-muted">
                                                <i class="bi bi-info-circle me-1"></i> Format: JPG, PNG. Maks 2MB.
                                            </div>
                                        </div>

                                        <!-- Google Drive -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Ambil dari Google
                                                Drive</label>
                                            <input type="hidden" name="gdrive_image_json" id="gdrive_image_json"
                                                value="{{ old('gdrive_image_json') }}">
                                            <button type="button"
                                                class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2"
                                                id="btnPickImage">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg"
                                                    alt="Google Drive" style="width: 18px; height: 18px;">
                                                <span>Pilih File</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Preview Area -->
                                    <div id="imagePreview" class="row g-2 mt-3"></div>

                                    <div class="mt-3 pt-3 border-top">
                                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                                            <i class="bi bi-upload me-2"></i> Simpan Dokumentasi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tampilan Gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-body p-0 text-center position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <img src="" id="modalImage" class="img-fluid rounded shadow-lg" style="max-height: 85vh;">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', event => {
                const triggerElement = event.relatedTarget;
                const imgSrc = triggerElement.getAttribute('data-bs-img-src');
                const modalImage = imageModal.querySelector('#modalImage');
                modalImage.src = imgSrc;
            });
        }
    </script>
@endpush

@push('scripts')
    <script defer src="https://apis.google.com/js/api.js"></script>

    <script>
        (() => {
            let pickerReady = false;
            const ORIGIN = window.location.origin;

            function loadPicker() {
                if (!window.gapi) return;
                gapi.load('picker', {
                    callback: () => (pickerReady = true)
                });
            }
            window.addEventListener('load', loadPicker);

            async function getPickerAuth() {
                const res = await fetch("{{ route('gdrive.token') }}", {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error("Token endpoint tidak mengembalikan JSON. Awal response: " + text.slice(0,
                        120));
                }

                if (!res.ok) throw new Error(data.message || 'Gagal mengambil token Google Drive.');
                return data; // { access_token, api_key, app_id }
            }

            // --- UI Logic for Proposal ---
            const localInput = document.getElementById('inputLocalProposal');
            const driveInput = document.getElementById('gdrive_pdf_proposal_json');
            const preview = document.getElementById('pdfPreview');

            function updateRequiredState() {
                if (!localInput || !driveInput) return;

                if (driveInput.value) {
                    // Kalau ada file Drive, local input jadi optional
                    localInput.removeAttribute('required');
                    localInput.classList.remove('is-invalid'); // cleanup visual
                } else {
                    // Kalau Drive kosong, local input wajib (kecuali kalau user sudah pilih file local, tapi required tetap ada biar browser cek)
                    localInput.setAttribute('required', 'required');
                }
            }

            function setPicked(picked) {
                if (driveInput) driveInput.value = JSON.stringify(picked);

                // Clear local input karena user pilih Drive
                if (localInput) localInput.value = '';

                updateRequiredState();

                if (preview) {
                    if (picked && picked.name) {
                        preview.innerHTML = `
                            <div class="card border-primary shadow-sm bg-white mt-2">
                                <div class="card-body p-2 d-flex align-items-center">
                                    <div class="bg-light-primary rounded p-2 me-3 text-danger">
                                        <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mb-0 text-primary fw-bold text-truncate" title="${picked.name}">
                                            ${picked.name}
                                        </h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-google me-1"></i> Google Drive File
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2 rounded-circle"
                                            onclick="clearPicked()" title="Hapus file">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    } else {
                        preview.innerHTML = '';
                    }
                }
            }

            window.clearPicked = function() {
                if (driveInput) driveInput.value = '';
                if (preview) preview.innerHTML = '';
                updateRequiredState();
            };

            // Initialize
            window.addEventListener('DOMContentLoaded', () => {
                // Check old value
                if (driveInput && driveInput.value) {
                    try {
                        const picked = JSON.parse(driveInput.value);
                        setPicked(picked);
                    } catch (e) {
                        console.error("Invalid JSON in old input", e);
                    }
                }

                // Listener for local input
                if (localInput) {
                    localInput.addEventListener('change', () => {
                        if (localInput.files.length > 0) {
                            // Kalau user pilih file local, hapus pilihan Drive
                            clearPicked();
                        }
                    });
                }
            });

            function isPdfFile(file) {
                if (!file) return false;
                if (file.type === 'application/pdf') return true;
                return (file.name || '').toLowerCase().endsWith('.pdf');
            }

            async function openPdfPicker() {
                if (!pickerReady) {
                    alert('Google Picker belum siap. Coba refresh halaman lalu klik lagi.');
                    return;
                }
                if (!window.google || !google.picker) {
                    alert('Library google.picker belum ter-load. Cek apakah api.js keblok / error di console.');
                    return;
                }

                try {
                    const {
                        access_token,
                        api_key,
                        app_id
                    } = await getPickerAuth();

                    if (!access_token) {
                        alert(
                            'Access Token tidak ditemukan. Silakan hubungkan ulang akun Google Drive Anda di menu Profil/Integrasi.'
                        );
                        return;
                    }

                    const docsView = new google.picker.DocsView()
                        .setIncludeFolders(true)
                        .setSelectFolderEnabled(false)
                        .setMimeTypes('application/pdf');

                    // IMPORTANT: jangan panggil setIncludeFolders di DocsUploadView (sering bikin Upload tab hilang)
                    const uploadView = new google.picker.DocsUploadView();
                    if (typeof uploadView.setMimeTypes === 'function') {
                        uploadView.setMimeTypes('application/pdf');
                    }

                    let picker; // biar bisa di-close dari callback

                    picker = new google.picker.PickerBuilder()
                        .setDeveloperKey(api_key)
                        .setAppId(String(app_id))
                        .setOAuthToken(access_token)
                        .setOrigin(ORIGIN)
                        .addView(docsView)
                        .addView(uploadView)
                        .setCallback((data) => {
                            // Versi yang robust: baca ACTION & DOCUMENTS via constant dulu
                            const action = data[google.picker.Response.ACTION] || data.action;

                            if (action === google.picker.Action.PICKED || action === 'picked') {
                                const docs = data[google.picker.Response.DOCUMENTS] || data.docs || [];
                                const d = docs[0];
                                if (!d) return;

                                const picked = {
                                    id: d.id,
                                    name: d.name,
                                    mimeType: d.mimeType,
                                    url: d.url || d.webViewLink || null
                                };

                                setPicked(picked);

                                // Tutup picker supaya UX enak
                                try {
                                    picker.setVisible(false);
                                } catch (e) {}
                            }
                        })
                        .build();

                    picker.setVisible(true);

                } catch (err) {
                    alert(err.message || 'Terjadi error saat membuka Google Drive Picker.');
                    console.error(err);
                }
            }

            // Drag & drop upload ke Drive lalu otomatis "kepilih"
            async function uploadPdfToDrive(file) {
                const {
                    access_token
                } = await getPickerAuth();
                if (!access_token) throw new Error('Access Token tidak ditemukan. Hubungkan ulang Google Drive.');

                const boundary = 'simpati_' + Date.now();
                const metadata = {
                    name: file.name,
                    mimeType: file.type || 'application/pdf'
                };

                const body = new Blob([
                    `--${boundary}\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n`,
                    JSON.stringify(metadata),
                    `\r\n--${boundary}\r\nContent-Type: ${metadata.mimeType}\r\n\r\n`,
                    file,
                    `\r\n--${boundary}--`
                ], {
                    type: `multipart/related; boundary=${boundary}`
                });

                const res = await fetch(
                    'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,mimeType,webViewLink', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + access_token,
                            'Content-Type': `multipart/related; boundary=${boundary}`
                        },
                        body
                    }
                );

                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const msg = data?.error?.message || 'Gagal upload ke Google Drive.';
                    throw new Error(msg);
                }

                setPicked({
                    id: data.id,
                    name: data.name,
                    mimeType: data.mimeType,
                    url: data.webViewLink || null
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.getElementById('btnPickPdf');
                if (!btn) return;

                btn.addEventListener('click', openPdfPicker);

                // Drag and drop langsung ke tombol (tanpa ubah tampilan tombol)
                btn.addEventListener('dragover', (e) => {
                    e.preventDefault();
                });
                btn.addEventListener('drop', async (e) => {
                    e.preventDefault();
                    const file = e.dataTransfer?.files?.[0];
                    if (!isPdfFile(file)) {
                        alert('Yang bisa di-drag ke sini hanya file PDF.');
                        return;
                    }

                    try {
                        await uploadPdfToDrive(file);
                        const preview = document.getElementById('pdfPreview');
                        if (preview) preview.innerText += ' (di-upload ke Drive)';
                    } catch (err) {
                        alert(err.message || 'Upload ke Drive gagal.');
                        console.error(err);
                    }
                });
            });
            // --- UI Logic for Image Picker ---
            const imageInput = document.getElementById('gdrive_image_json');
            const imagePreview = document.getElementById('imagePreview');
            let selectedImages = [];

            function updateImagePreview() {
                if (!imagePreview) return;
                imagePreview.innerHTML = '';

                selectedImages.forEach((img, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4 position-relative';
                    col.innerHTML = `
                            <div class="card border shadow-sm h-100">
                                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center text-center">
                                    <div class="text-primary mb-2">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                    <small class="text-truncate w-100 fw-bold" title="${img.name}">${img.name}</small>
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-1 lh-1"
                                            onclick="removeImage(${index})" style="width: 24px; height: 24px;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    imagePreview.appendChild(col);
                });

                if (imageInput) {
                    imageInput.value = selectedImages.length ? JSON.stringify(selectedImages) : '';
                }
            }

            window.removeImage = function(index) {
                selectedImages.splice(index, 1);
                updateImagePreview();
            };

            // Initialize Image Preview
            window.addEventListener('DOMContentLoaded', () => {
                if (imageInput && imageInput.value) {
                    try {
                        selectedImages = JSON.parse(imageInput.value);
                        updateImagePreview();
                    } catch (e) {
                        console.error("Invalid JSON in image input", e);
                    }
                }
            });

            async function openImagePicker() {
                if (!pickerReady) {
                    alert('Google Picker belum siap. Coba refresh halaman.');
                    return;
                }

                try {
                    const {
                        access_token,
                        api_key,
                        app_id
                    } = await getPickerAuth();

                    const docsView = new google.picker.DocsView()
                        .setIncludeFolders(true)
                        .setSelectFolderEnabled(false)
                        .setMimeTypes('image/jpeg,image/png,image/webp')
                        .setMode(google.picker.DocsViewMode.GRID);

                    const uploadView = new google.picker.DocsUploadView();
                    if (typeof uploadView.setMimeTypes === 'function') {
                        uploadView.setMimeTypes('image/jpeg,image/png,image/webp');
                    }

                    const picker = new google.picker.PickerBuilder()
                        .setDeveloperKey(api_key)
                        .setAppId(String(app_id))
                        .setOAuthToken(access_token)
                        .setOrigin(ORIGIN)
                        .addView(docsView)
                        .addView(uploadView) // Add Upload View
                        .enableFeature(google.picker.Feature.MULTISELECT_ENABLED) // Enable multi-select
                        .setCallback((data) => {
                            if (data[google.picker.Response.ACTION] === google.picker.Action.PICKED) {
                                const docs = data[google.picker.Response.DOCUMENTS] || [];

                                // Append new selections to existing ones
                                docs.forEach(d => {
                                    // Avoid duplicates based on ID
                                    if (!selectedImages.find(img => img.id === d.id)) {
                                        selectedImages.push({
                                            id: d.id,
                                            name: d.name,
                                            mimeType: d.mimeType,
                                            url: d.url || d.webViewLink || null
                                        });
                                    }
                                });

                                updateImagePreview();
                            }
                        })
                        .build();

                    picker.setVisible(true);

                } catch (err) {
                    alert('Gagal membuka Google Picker: ' + err.message);
                }
            }

            // Drag & drop upload ke Drive untuk Image
            async function uploadImageToDrive(file) {
                const {
                    access_token
                } = await getPickerAuth();
                if (!access_token) throw new Error('Access Token tidak ditemukan. Hubungkan ulang Google Drive.');

                const boundary = 'simpati_' + Date.now();
                const metadata = {
                    name: file.name,
                    mimeType: file.type || 'image/jpeg'
                };

                const body = new Blob([
                    `--${boundary}\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n`,
                    JSON.stringify(metadata),
                    `\r\n--${boundary}\r\nContent-Type: ${metadata.mimeType}\r\n\r\n`,
                    file,
                    `\r\n--${boundary}--`
                ], {
                    type: `multipart/related; boundary=${boundary}`
                });

                const res = await fetch(
                    'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,mimeType,webViewLink', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + access_token,
                            'Content-Type': `multipart/related; boundary=${boundary}`
                        },
                        body
                    }
                );

                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const msg = data?.error?.message || 'Gagal upload ke Google Drive.';
                    throw new Error(msg);
                }

                // Add to selected images
                if (!selectedImages.find(img => img.id === data.id)) {
                    selectedImages.push({
                        id: data.id,
                        name: data.name,
                        mimeType: data.mimeType,
                        url: data.webViewLink || null
                    });
                    updateImagePreview();
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                const btnImage = document.getElementById('btnPickImage');
                if (btnImage) {
                    btnImage.addEventListener('click', openImagePicker);

                    // Drag and drop listeners
                    btnImage.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        btnImage.classList.add('bg-light'); // Visual feedback
                    });
                    btnImage.addEventListener('dragleave', (e) => {
                        e.preventDefault();
                        btnImage.classList.remove('bg-light');
                    });
                    btnImage.addEventListener('drop', async (e) => {
                        e.preventDefault();
                        btnImage.classList.remove('bg-light');

                        const files = e.dataTransfer?.files;
                        if (!files || files.length === 0) return;

                        // Upload all dropped images
                        let successCount = 0;
                        const originalButtonContent = btnImage.innerHTML; // Store original content
                        for (let i = 0; i < files.length; i++) {
                            const file = files[i];
                            if (!file.type.startsWith('image/')) {
                                alert(`File ${file.name} bukan gambar.`);
                                continue;
                            }

                            try {
                                // Optional: Show loading indicator
                                btnImage.innerHTML =
                                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading ${file.name}...`;

                                await uploadImageToDrive(file);
                                successCount++;
                            } catch (err) {
                                console.error(`Gagal upload ${file.name}:`, err);
                                alert(`Gagal upload ${file.name}: ${err.message}`);
                            }
                        }

                        // Restore button text
                        btnImage.innerHTML = originalButtonContent;

                        if (successCount > 0) {
                            // Optional: success message
                        }
                    });
                }
            });
        })();
    </script>
@endpush
