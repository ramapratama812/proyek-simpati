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

        .import-section {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ url()->previous() ?: url('/publications') }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <h1 class="fw-bold mb-0">Tambah Publikasi Baru</h1>
                <p class="text-white-50 mb-0 mt-2">Isi formulir di bawah ini untuk menambahkan data publikasi.</p>
            </div>
        </div>

        <div class="container pb-5">
            <form method="POST" action="{{ route('publications.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="project_id" value="{{ old('project_id', $projectId) }}">

                <div class="row g-4">
                    {{-- Left Column: Main Form --}}
                    <div class="col-lg-8">

                        {{-- Import Section --}}
                        <div class="import-section">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-2 text-primary">
                                    <i class="bi bi-cloud-download-fill fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-primary">Impor Otomatis dari DOI</h6>
                            </div>
                            <div class="input-group">
                                <input id="doiImportInput" class="form-control"
                                    placeholder="Masukkan DOI (Contoh: 10.1038/nphys1170)">
                                <button type="button" id="btnImportDoi" class="btn btn-primary px-4"><i
                                        class="bi bi-magic me-2"></i>Impor Data</button>
                            </div>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Data akan terisi otomatis
                                jika DOI ditemukan di Crossref.</div>
                        </div>

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
                                        <input name="judul" class="form-control" value="{{ old('judul') }}"
                                            placeholder="Masukkan judul lengkap..." required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Publikasi</label>
                                        <input name="jenis" class="form-control"
                                            placeholder="Contoh: Jurnal, Prosiding, Artikel" value="{{ old('jenis') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Jurnal / Prosiding</label>
                                        <input name="jurnal" class="form-control" placeholder="Nama wadah publikasi"
                                            value="{{ old('jurnal') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tahun Terbit</label>
                                        <input name="tahun" type="number" min="1980" class="form-control"
                                            placeholder="YYYY" value="{{ old('tahun') }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">DOI</label>
                                        <input name="doi" id="doiField" class="form-control"
                                            placeholder="10.xxxx/xxxxx" value="{{ old('doi') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Penulis</label>
                                        <textarea name="penulis" class="form-control" rows="3"
                                            placeholder="Masukkan nama penulis. Pisahkan dengan koma jika lebih dari satu.">{{ old('penulis') }}</textarea>
                                        <div class="form-text">Contoh: John Doe, Jane Smith, Alan Turing</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Abstrak</label>
                                        <textarea name="abstrak" class="form-control" rows="5" placeholder="Ringkasan isi publikasi...">{{ old('abstrak') }}</textarea>
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
                                            value="{{ old('volume') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor (Issue)</label>
                                        <input type="number" name="nomor" class="form-control" min="1"
                                            value="{{ old('nomor') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Jumlah Halaman</label>
                                        <input name="jumlah_halaman" type="number" class="form-control"
                                            value="{{ old('jumlah_halaman') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Upload File --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-file-earmark-pdf me-2"></i> Upload File</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="mb-3">
                                    <label for="file" class="form-label">File Artikel (PDF)</label>
                                    <input type="file" name="file" id="file" accept="application/pdf"
                                        class="form-control @error('file') is-invalid @enderror">

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

                                    <div class="form-text small text-muted mt-2">
                                        <i class="bi bi-info-circle me-1"></i> Format PDF, Maksimal 2MB.
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i class="bi bi-save me-2"></i> Simpan Publikasi
                            </button>
                            <a href="{{ url()->previous() ?: url('/publications') }}"
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

{{-- Buat integrasi crossref (impor DOI) --}}
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

    <script>
        (function() {
            function normDOI(s) {
                if (!s) return '';
                s = s.trim();
                return s.replace(/^https?:\/\/(dx\.)?doi\.org\//i, ''); // hilangkan prefix URL jika ada
            }

            function pickYear(msg) {
                const src = (msg['published-print']?.['date-parts']?.[0] ||
                    msg['published-online']?.['date-parts']?.[0] ||
                    msg['issued']?.['date-parts']?.[0]) || [];
                return src[0] || '';
            }

            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('btnImportDoi');
                if (!btn) return;
                btn.addEventListener('click', async function() {
                    const raw = document.getElementById('doiImportInput').value;
                    const doi = normDOI(raw);
                    if (!doi) {
                        alert('Isi DOI terlebih dahulu.');
                        return;
                    }

                    // Show loading state
                    const originalText = btn.innerHTML;
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
                    btn.disabled = true;

                    try {
                        const res = await fetch('https://api.crossref.org/works/' +
                            encodeURIComponent(doi));
                        if (!res.ok) throw new Error('Gagal menghubungi Crossref');
                        const json = await res.json();
                        const m = json.message || {};

                        // isi field
                        document.querySelector('[name=judul]').value = (m.title && m.title[0]) ||
                            '';
                        document.querySelector('[name=jurnal]').value =
                            (m['container-title'] && m['container-title'][0]) || (m.publisher ||
                                '');
                        document.querySelector('[name=tahun]').value = pickYear(m);
                        document.querySelector('[name=volume]').value = m.volume || '';
                        document.querySelector('[name=nomor]').value = m.issue || '';
                        document.querySelector('[name=abstrak]').value = m.abstract || '';
                        document.querySelector('[name=jumlah_halaman]').value = calculatePageCount(m
                            .page);
                        document.querySelector('[name=doi]').value = m.DOI || doi;
                        document.querySelector('[name=jenis]').value = (m.type || '').replace(/_/g,
                            ' ');

                        // Populate authors
                        if (m.author && Array.isArray(m.author)) {
                            const authors = m.author.map(a => `${a.given || ''} ${a.family || ''}`
                                .trim()).join(', ');
                            document.querySelector('[name=penulis]').value = authors;
                        }

                        function calculatePageCount(page) {
                            if (!page) return '';
                            const match = page.match(/(\d+)-(\d+)/);
                            if (match) {
                                return parseInt(match[2]) - parseInt(match[1]) + 1;
                            }
                            return '';
                        }

                        alert(
                            'Data berhasil diimpor dari Crossref. Silakan periksa kembali sebelum menyimpan.'
                        );
                    } catch (e) {
                        console.error(e);
                        alert('Impor DOI gagal. Periksa nilai DOI atau coba lagi.');
                    } finally {
                        // Restore button state
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                });
            });
        })();
    </script>
@endpush
