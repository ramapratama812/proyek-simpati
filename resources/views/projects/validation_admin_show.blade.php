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
            font-size: 1.1rem;
        }

        .detail-card-body {
            padding: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 500;
            color: #212529;
            font-size: 1rem;
        }

        .action-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .action-header {
            padding: 1.25rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .action-body {
            padding: 1.5rem;
        }

        .validation-section {
            border-left: 4px solid #dee2e6;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }

        .validation-section.approve {
            border-color: #198754;
        }

        .validation-section.revision {
            border-color: #ffc107;
        }

        .validation-section.reject {
            border-color: #dc3545;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('projects.validation.index') }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="fw-bold mb-2">Validasi Kegiatan</h1>
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="badge bg-white text-primary px-3 py-1 rounded-pill fw-bold text-uppercase small">{{ $project->jenis }}</span>
                            @include('projects._validation_badge', ['project' => $project])
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row g-4">
                {{-- Left Column: Details --}}
                <div class="col-lg-8">

                    {{-- Main Info --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-info-circle me-2"></i> Detail Kegiatan</h5>
                        </div>
                        <div class="detail-card-body">
                            <h4 class="fw-bold text-dark mb-4">{{ $project->judul }}</h4>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="info-label">Ketua Pengusul</div>
                                    <div class="info-value d-flex align-items-center">
                                        <div class="avatar-circle bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2"
                                            style="width: 32px; height: 32px;">
                                            {{ substr(optional($project->ketua)->name ?? '?', 0, 1) }}
                                        </div>
                                        {{ optional($project->ketua)->name }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Skema</div>
                                    <div class="info-value">{{ $project->skema }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Bidang Ilmu</div>
                                    <div class="info-value">{{ $project->bidang_ilmu }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Kategori Kegiatan</div>
                                    <div class="info-value">{{ $project->kategori_kegiatan }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Tahun Usulan</div>
                                    <div class="info-value">{{ $project->tahun_usulan }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Tahun Pelaksanaan</div>
                                    <div class="info-value">{{ $project->tahun_pelaksanaan }}</div>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25">

                            <div class="mb-4">
                                <div class="info-label mb-2">Abstrak</div>
                                <div class="bg-light p-3 rounded text-secondary" style="line-height: 1.6;">
                                    {{ $project->abstrak }}
                                </div>
                            </div>

                            @if ($project->validation_note)
                                <div class="alert alert-warning border-0 shadow-sm d-flex">
                                    <i class="bi bi-exclamation-circle-fill fs-4 me-3 text-warning"></i>
                                    <div>
                                        <div class="fw-bold text-dark">Catatan Validasi Terakhir</div>
                                        <div class="text-secondary">{{ $project->validation_note }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Publications --}}
                    <div class="card detail-card">
                        <div class="detail-card-header">
                            <h5 class="detail-card-title"><i class="bi bi-journal-text me-2"></i> Publikasi Terkait</h5>
                        </div>
                        <div class="detail-card-body p-0">
                            @if ($project->publications->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-1 mb-2 d-block"></i>
                                    Belum ada publikasi yang dikaitkan.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3">Judul Publikasi</th>
                                                <th>Tahun</th>
                                                <th>Jenis</th>
                                                <th class="text-end pe-4">Tautan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($project->publications as $pub)
                                                <tr>
                                                    <td class="ps-4 fw-medium">
                                                        {{ $pub->title ?? ($pub->judul ?? 'Tanpa judul') }}</td>
                                                    <td>{{ $pub->year ?? ($pub->tahun ?? '-') }}</td>
                                                    <td><span
                                                            class="badge bg-light text-dark border">{{ $pub->type ?? ($pub->jenis ?? '-') }}</span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        @php
                                                            $url =
                                                                $pub->url ??
                                                                ($pub->tautan ?? ($pub->link ?? ($pub->doi ?? null)));
                                                        @endphp
                                                        @if ($url)
                                                            <a href="{{ route('publications.show', $pub) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-box-arrow-up-right"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right Column: Actions --}}
                <div class="col-lg-4">

                    {{-- Files --}}
                    <div class="card action-card mb-4">
                        <div class="action-header bg-light text-dark">
                            <i class="bi bi-folder2-open me-2"></i> Berkas Pendukung
                        </div>
                        <div class="action-body">
                            @if ($project->surat_proposal)
                                <a href="{{ asset('storage/' . $project->surat_proposal) }}" target="_blank"
                                    class="btn btn-outline-primary w-100 mb-2 text-start d-flex align-items-center p-3 border-2">
                                    <i class="bi bi-file-earmark-pdf fs-3 me-3"></i>
                                    <div class="lh-sm">
                                        <div class="fw-bold">Surat Proposal</div>
                                        <div class="small text-muted">Klik untuk melihat</div>
                                    </div>
                                </a>
                            @else
                                <div class="alert alert-secondary mb-2 small"><i class="bi bi-info-circle me-1"></i>
                                    Proposal belum diunggah</div>
                            @endif

                            @if ($project->surat_persetujuan)
                                <a href="{{ asset('storage/' . $project->surat_persetujuan) }}" target="_blank"
                                    class="btn btn-outline-success w-100 mb-2 text-start d-flex align-items-center p-3 border-2">
                                    <i class="bi bi-file-earmark-check fs-3 me-3"></i>
                                    <div class="lh-sm">
                                        <div class="fw-bold">Surat Persetujuan</div>
                                        <div class="small text-muted">Klik untuk melihat</div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Validation Actions --}}
                    <div class="card action-card">
                        <div class="action-header bg-primary text-white">
                            <i class="bi bi-shield-check me-2"></i> Aksi Validasi
                        </div>
                        <div class="action-body">

                            {{-- Approve --}}
                            <div class="validation-section approve">
                                <h6 class="fw-bold text-success mb-2"><i class="bi bi-check-circle-fill me-1"></i> Setujui
                                    Kegiatan</h6>
                                <form method="POST" action="{{ route('projects.validation.approve', $project) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Catatan (opsional)</label>
                                        <textarea name="note" class="form-control form-control-sm" rows="2" placeholder="Tambahkan catatan...">{{ old('note') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Surat Persetujuan P3M (PDF) <span
                                                class="text-danger">*</span></label>

                                        <!-- Local Upload -->
                                        <div class="mb-2">
                                            <input type="file" name="surat_persetujuan" id="inputLocalProposal"
                                                class="form-control form-control-sm" accept="application/pdf" required>
                                        </div>

                                        <div class="text-center text-muted small my-2" style="font-size: 0.75rem;">
                                            <span class="bg-white px-2 text-uppercase fw-bold">atau</span>
                                        </div>

                                        <!-- Google Drive -->
                                        <input type="hidden" name="gdrive_pdf_persetujuan_json"
                                            id="gdrive_pdf_persetujuan_json"
                                            value="{{ old('gdrive_pdf_persetujuan_json') }}">

                                        <div class="d-grid">
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center gap-2"
                                                id="btnPickPdf">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg"
                                                    alt="Google Drive" style="width: 16px; height: 16px;">
                                                <span>Pilih dari Google Drive</span>
                                            </button>
                                        </div>
                                        <div id="pdfPreview" class="mt-2"></div>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm"
                                        onclick="return confirm('Setujui usulan kegiatan ini?');">
                                        Setujui Usulan
                                    </button>
                                </form>
                            </div>

                            <hr class="my-4">

                            {{-- Revision --}}
                            <div class="validation-section revision">
                                <h6 class="fw-bold text-warning mb-2"><i class="bi bi-arrow-counterclockwise me-1"></i>
                                    Minta Revisi</h6>
                                <form method="POST" action="{{ route('projects.validation.revision', $project) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark">Catatan Revisi <span
                                                class="text-danger">*</span></label>
                                        <textarea name="note" class="form-control form-control-sm" rows="2" required
                                            placeholder="Jelaskan bagian yang perlu direvisi...">{{ old('note') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100 fw-bold text-dark shadow-sm"
                                        onclick="return confirm('Kirim permintaan revisi ke dosen?');">
                                        Kirim Permintaan Revisi
                                    </button>
                                </form>
                            </div>

                            <hr class="my-4">

                            {{-- Reject --}}
                            <div class="validation-section reject mb-0">
                                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-x-circle-fill me-1"></i> Tolak Usulan
                                </h6>
                                <form method="POST" action="{{ route('projects.validation.reject', $project) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark">Alasan Penolakan <span
                                                class="text-danger">*</span></label>
                                        <textarea name="note" class="form-control form-control-sm" rows="2" required
                                            placeholder="Jelaskan alasan penolakan...">{{ old('note') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm"
                                        onclick="return confirm('Tolak usulan kegiatan ini?');">
                                        Tolak Usulan
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

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
                const driveInput = document.getElementById('gdrive_pdf_persetujuan_json');
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
@endsection
