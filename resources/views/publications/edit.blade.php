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

        .form-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .form-card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }

        .form-card-title {
            font-weight: 700;
            margin-bottom: 0;
            color: #0a58ca;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }

        .form-card-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .required-asterisk {
            color: #dc3545;
            margin-left: 3px;
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
            border-color: #ced4da;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('publications.show', $publication) }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
                    </a>
                </div>
                <h1 class="fw-bold mb-0">Edit Publikasi</h1>
                <p class="text-white-50 mb-0 mt-2">Perbarui informasi publikasi yang sudah ada.</p>
            </div>
        </div>

        <div class="container pb-5">
            <form method="POST" action="{{ route('publications.update', $publication) }}" enctype="multipart/form-data"
                onsubmit="return confirm('Simpan perubahan publikasi?');">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- Left Column: Main Form --}}
                    <div class="col-lg-8">
                        {{-- Data Utama --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-journal-bookmark me-2"></i> Data Publikasi</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Judul Publikasi<span
                                                class="required-asterisk">*</span></label>
                                        <input name="judul" class="form-control" required
                                            value="{{ old('judul', $publication->judul) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Publikasi</label>
                                        <input name="jenis" class="form-control"
                                            value="{{ old('jenis', $publication->jenis) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Jurnal / Prosiding</label>
                                        <input name="jurnal" class="form-control"
                                            value="{{ old('jurnal', $publication->jurnal) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tahun Terbit</label>
                                        <input name="tahun" type="number" class="form-control"
                                            value="{{ old('tahun', $publication->tahun) }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">DOI</label>
                                        <input name="doi" class="form-control"
                                            value="{{ old('doi', $publication->doi) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Penulis</label>
                                        <textarea name="penulis" class="form-control" rows="3"
                                            placeholder="Masukkan nama penulis, pisahkan dengan koma atau baris baru">{{ old('penulis', is_array($publication->penulis) ? implode(', ', $publication->penulis) : '') }}</textarea>
                                        <div class="form-text">Contoh: John Doe, Jane Smith</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Abstrak</label>
                                        <textarea name="abstrak" class="form-control" rows="5">{{ old('abstrak', $publication->abstrak) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Additional Info --}}
                    <div class="col-lg-4">
                        {{-- Detail Tambahan --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-list-ul me-2"></i> Detail Tambahan</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Volume</label>
                                        <input type="number" name="volume" class="form-control" min="1"
                                            value="{{ old('volume', $publication->volume) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor (Issue)</label>
                                        <input type="number" name="nomor" class="form-control" min="1"
                                            value="{{ old('nomor', $publication->nomor) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Jumlah Halaman</label>
                                        <input name="jumlah_halaman" type="number" class="form-control" min="1"
                                            value="{{ old('jumlah_halaman', $publication->jumlah_halaman) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Upload File --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-file-earmark-pdf me-2"></i> File Artikel</h5>
                            </div>
                            <div class="form-card-body">
                                <label class="form-label">File Saat Ini</label>
                                @if (!empty($publication->file))
                                    <div class="p-3 bg-light rounded-3 mb-3 border">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-file-earmark-pdf text-danger fs-4 me-2"></i>
                                            <span class="text-truncate small fw-bold text-dark">File Tersedia</span>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <a href="{{ asset('storage/' . $publication->file) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-secondary small mb-3">
                                        <i class="bi bi-info-circle me-1"></i> Belum ada file PDF.
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload File Baru</label>
                                    <input type="file" name="file" id="file" accept="application/pdf"
                                        class="form-control @error('file') is-invalid @enderror">
                                    <div class="form-text small text-muted mt-2">
                                        <i class="bi bi-info-circle me-1"></i> Mengganti file lama jika ada. (PDF, Maks
                                        10MB)
                                    </div>

                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="mb-3">
                                        <input type="hidden" name="gdrive_pdf_json" id="gdrive_pdf_json"
                                            value="{{ old('gdrive_pdf_json') }}">
                                        <div id="pdfPreview" class="mt-2"></div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="button"
                                            class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 py-2"
                                            id="btnPickPdf">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg"
                                                alt="Google Drive" style="width: 20px; height: 20px;">
                                            <span>Unggah dari Google Drive</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('publications.show', $publication) }}"
                                class="btn btn-outline-secondary py-2">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

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

            function setPicked(picked) {
                const input = document.getElementById('gdrive_pdf_json');
                if (input) input.value = JSON.stringify(picked);

                const preview = document.getElementById('pdfPreview');
                if (preview) {
                    if (picked && picked.name) {
                        preview.innerHTML = `
                            <div class="card border-primary shadow-sm bg-light-primary">
                                <div class="card-body p-2 d-flex align-items-center">
                                    <div class="bg-white rounded p-2 me-3 text-danger shadow-sm">
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
                const input = document.getElementById('gdrive_pdf_json');
                if (input) input.value = '';

                const preview = document.getElementById('pdfPreview');
                if (preview) preview.innerHTML = '';
            };

            // Initialize preview if old value exists
            window.addEventListener('DOMContentLoaded', () => {
                const oldInput = document.getElementById('gdrive_pdf_json');
                if (oldInput && oldInput.value) {
                    try {
                        const picked = JSON.parse(oldInput.value);
                        setPicked(picked);
                    } catch (e) {
                        console.error("Invalid JSON in old input", e);
                    }
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
        })();
    </script>
@endpush
