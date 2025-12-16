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

        .member-controls {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .member-list-container {
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
            background-color: #fff;
        }
    </style>

    <div class="container-fluid px-0">
        {{-- Header Section --}}
        <div class="page-header-gradient px-4">
            <div class="container">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('projects.show', $project) }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
                    </a>
                </div>
                <h1 class="fw-bold mb-0">Edit Kegiatan</h1>
                <p class="text-white-50 mb-0 mt-2">Perbarui informasi kegiatan penelitian atau pengabdian.</p>
            </div>
        </div>

        <div class="container pb-5">

            {{-- Error summary --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div class="fw-bold">Gagal menyimpan perubahan</div>
                    </div>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- Left Column --}}
                    <div class="col-lg-8">

                        {{-- Data Akademik --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-journal-bookmark me-2"></i> Data Akademik & Umum
                                </h5>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Jenis Kegiatan<span
                                                class="required-asterisk">*</span></label>
                                        <select name="jenis" class="form-select" required>
                                            <option value="penelitian" @selected(old('jenis', $project->jenis) == 'penelitian')>Penelitian</option>
                                            <option value="pengabdian" @selected(old('jenis', $project->jenis) == 'pengabdian')>Pengabdian</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Judul Kegiatan<span
                                                class="required-asterisk">*</span></label>
                                        <input type="text" name="judul" class="form-control"
                                            value="{{ old('judul', $project->judul) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kategori Kegiatan<span
                                                class="required-asterisk">*</span></label>
                                        <input type="text" name="kategori_kegiatan" class="form-control"
                                            value="{{ old('kategori_kegiatan', $project->kategori_kegiatan) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bidang Ilmu<span
                                                class="required-asterisk">*</span></label>
                                        <input type="text" name="bidang_ilmu" class="form-control"
                                            value="{{ old('bidang_ilmu', $project->bidang_ilmu) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Skema<span class="required-asterisk">*</span></label>
                                        <input type="text" name="skema" class="form-control"
                                            value="{{ old('skema', $project->skema) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Abstrak<span class="required-asterisk">*</span></label>
                                        <textarea name="abstrak" rows="5" class="form-control" required>{{ old('abstrak', $project->abstrak) }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Kata Kunci</label>
                                        <input type="text" name="keywords" class="form-control"
                                            value="{{ old('keywords', $project->keywords) }}"
                                            placeholder="Contoh: IoT; UMKM; Sistem Informasi">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Detail Pelaksanaan --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-calendar-check me-2"></i> Detail Pelaksanaan &
                                    Anggaran</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Mulai<span
                                                class="required-asterisk">*</span></label>
                                        <input type="date" name="mulai" class="form-control"
                                            value="{{ old('mulai', optional($project->mulai)->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Selesai<span
                                                class="required-asterisk">*</span></label>
                                        <input type="date" name="selesai" class="form-control"
                                            value="{{ old('selesai', optional($project->selesai)->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tahun Usulan</label>
                                        <input type="number" name="tahun_usulan" class="form-control" min="2010"
                                            max="{{ date('Y') + 1 }}"
                                            value="{{ old('tahun_usulan', $project->tahun_usulan) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tahun Pelaksanaan</label>
                                        <input type="number" name="tahun_pelaksanaan" class="form-control"
                                            min="2010" max="{{ date('Y') + 2 }}"
                                            value="{{ old('tahun_pelaksanaan', $project->tahun_pelaksanaan) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sumber Dana<span
                                                class="required-asterisk">*</span></label>
                                        <select name="sumber_dana" class="form-select" required>
                                            <option value="" disabled>Pilih sumber dana...</option>
                                            <option value="Mandiri" @selected(old('sumber_dana', $project->sumber_dana) == 'Mandiri')>Mandiri</option>
                                            <option value="Hibah" @selected(old('sumber_dana', $project->sumber_dana) == 'Hibah')>Hibah</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="biaya_display" class="form-label">Biaya (Rp)<span
                                                class="required-asterisk">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="text" id="biaya_display" class="form-control"
                                                placeholder="0"
                                                value="{{ old('biaya', number_format($project->biaya, 0, ',', '.')) }}"
                                                required>
                                        </div>
                                        <input type="hidden" name="biaya" id="biaya_real"
                                            value="{{ old('biaya', $project->biaya) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Lokasi Pelaksanaan</label>
                                        <input type="text" name="lokasi" class="form-control"
                                            value="{{ old('lokasi', $project->lokasi) }}"
                                            placeholder="Kota/Kabupaten, Provinsi">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mitra (Opsional)</label>
                                        <input type="text" name="mitra_nama" class="form-control"
                                            value="{{ old('mitra_nama', $project->mitra_nama) }}"
                                            placeholder="Nama mitra/instansi terkait">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Keanggotaan --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-people me-2"></i> Keanggotaan Tim</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="ketua-select" class="form-label">Ketua Proyek<span
                                                class="required-asterisk">*</span></label>
                                        <div class="position-relative mb-2">
                                            <select id="ketua-select" name="ketua_user_id"
                                                placeholder="Cari nama dosen..." autocomplete="off" required>
                                                <option value="">— Pilih Ketua —</option>
                                                @isset($lecturers)
                                                    @foreach ($lecturers as $l)
                                                        <option value="{{ $l->id }}"
                                                            {{ old('ketua_user_id', $project->ketua_id) == $l->id ? 'selected' : '' }}>
                                                            {{ $l->name }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>
                                        <div class="small text-muted"><i class="bi bi-info-circle me-1"></i> Ketua
                                            bertanggung jawab penuh atas data kegiatan.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Anggota Tim<span
                                                class="required-asterisk">*</span></label>

                                        <div class="member-controls p-2 rounded-2 mb-2">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text border-end-0 bg-white"><i
                                                        class="bi bi-search text-muted"></i></span>
                                                <input type="text" id="member-search-input"
                                                    class="form-control border-start-0" placeholder="Cari anggota...">
                                            </div>

                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="member-filter"
                                                    id="filter-semua" value="semua" autocomplete="off" checked>
                                                <label class="btn btn-outline-secondary btn-sm"
                                                    for="filter-semua">Semua</label>

                                                <input type="radio" class="btn-check" name="member-filter"
                                                    id="filter-dosen" value="dosen" autocomplete="off">
                                                <label class="btn btn-outline-secondary btn-sm"
                                                    for="filter-dosen">Dosen</label>

                                                <input type="radio" class="btn-check" name="member-filter"
                                                    id="filter-mahasiswa" value="mahasiswa" autocomplete="off">
                                                <label class="btn btn-outline-secondary btn-sm"
                                                    for="filter-mahasiswa">Mhs</label>
                                            </div>
                                        </div>

                                        <div id="member-list-container" class="member-list-container p-2"
                                            style="max-height: 250px; overflow-y: auto;">
                                            <div id="no-members-found" class="text-center text-muted py-4"
                                                style="display: none;">
                                                <i class="bi bi-person-x fs-4 d-block mb-1"></i>
                                                <span class="small">Tidak ditemukan</span>
                                            </div>

                                            @isset($lecturers)
                                                @if ($lecturers->isNotEmpty())
                                                    <div class="fw-bold text-primary small text-uppercase px-2 py-1 bg-light rounded mb-1 sticky-top"
                                                        data-role-header="dosen">Dosen</div>
                                                    @foreach ($lecturers as $l)
                                                        <div class="member-item d-flex align-items-center px-2 py-1 rounded hover-bg-light position-relative"
                                                            data-role="dosen" data-name="{{ strtolower($l->name) }}">
                                                            <div class="me-2">
                                                                <input class="form-check-input m-0" type="checkbox"
                                                                    name="anggota_user_ids[]" value="{{ $l->id }}"
                                                                    id="anggota-{{ $l->id }}"
                                                                    @checked(in_array($l->id, old('anggota_user_ids', $selectedAnggota)))>
                                                            </div>
                                                            <label
                                                                class="form-check-label w-100 stretched-link cursor-pointer m-0"
                                                                for="anggota-{{ $l->id }}">
                                                                {{ $l->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endisset

                                            @if (isset($lecturers) && $lecturers->isNotEmpty() && isset($students) && $students->isNotEmpty())
                                                <div class="my-2" data-role-header="separator"></div>
                                            @endif

                                            @isset($students)
                                                @if ($students->isNotEmpty())
                                                    <div class="fw-bold text-success small text-uppercase px-2 py-1 bg-light rounded mb-1 sticky-top"
                                                        data-role-header="mahasiswa">Mahasiswa</div>
                                                    @foreach ($students as $s)
                                                        <div class="member-item d-flex align-items-center px-2 py-1 rounded hover-bg-light position-relative"
                                                            data-role="mahasiswa" data-name="{{ strtolower($s->name) }}">
                                                            <div class="me-2">
                                                                <input class="form-check-input m-0" type="checkbox"
                                                                    name="anggota_user_ids[]" value="{{ $s->id }}"
                                                                    id="anggota-{{ $s->id }}"
                                                                    @checked(in_array($s->id, old('anggota_user_ids', $selectedAnggota)))>
                                                            </div>
                                                            <label
                                                                class="form-check-label w-100 stretched-link cursor-pointer m-0"
                                                                for="anggota-{{ $s->id }}">
                                                                {{ $s->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endisset
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Right Column --}}
                    <div class="col-lg-4">

                        {{-- Status & Lainnya --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-sliders me-2"></i> Status & Luaran</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="mb-3">
                                    <label class="form-label">Status Kegiatan</label>
                                    <select name="status" class="form-select">
                                        <option value="usulan" @selected(old('status', $project->status) == 'usulan')>Usulan</option>
                                        <option value="didanai" @selected(old('status', $project->status) == 'didanai')>Didanai</option>
                                        <option value="berjalan" @selected(old('status', $project->status) == 'berjalan')>Berjalan</option>
                                        <option value="selesai" @selected(old('status', $project->status) == 'selesai')>Selesai</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">TKT/TRL (1-9)</label>
                                    <input type="number" name="tkt" class="form-control" min="1"
                                        max="9" value="{{ old('tkt', $project->tkt) }}" placeholder="Opsional">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Target Luaran</label>
                                    <div class="card card-body bg-light border-0 p-2">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="jurnal_terakreditasi" id="tl1" @checked(in_array('jurnal_terakreditasi', old('target_luaran', $project->target_luaran ?? [])))>
                                            <label class="form-check-label" for="tl1">Jurnal Terakreditasi</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="prosiding" id="tl2" @checked(in_array('prosiding', old('target_luaran', $project->target_luaran ?? [])))>
                                            <label class="form-check-label" for="tl2">Prosiding</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="hki" id="tl3" @checked(in_array('hki', old('target_luaran', $project->target_luaran ?? [])))>
                                            <label class="form-check-label" for="tl3">HKI/Paten</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="buku" id="tl4" @checked(in_array('buku', old('target_luaran', $project->target_luaran ?? [])))>
                                            <label class="form-check-label" for="tl4">Buku</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tautan Pendukung</label>
                                    <input type="url" name="tautan" class="form-control"
                                        value="{{ old('tautan', $project->tautan) }}" placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        {{-- Uploads --}}
                        <div class="card form-card">
                            <div class="form-card-header">
                                <h5 class="form-card-title"><i class="bi bi-cloud-upload me-2"></i> Unggah Berkas</h5>
                            </div>
                            <div class="form-card-body">
                                <div class="mb-3">
                                    <label class="form-label">Surat Proposal</label>
                                    <input type="file" name="surat_proposal" accept="application/pdf"
                                        class="form-control">
                                    <div class="form-text small">Upload file PDF baru jika ingin mengganti.</div>
                                </div>

                                <!-- Google Drive -->
                                <div class="mb-0">
                                    <label class="form-label small text-muted fw-bold mb-1">Ambil dari
                                        Google Drive</label>
                                    <input type="hidden" name="gdrive_pdf_proposal_json" id="gdrive_pdf_proposal_json"
                                        value="{{ old('gdrive_pdf_proposal_json') }}">

                                    <div class="d-grid">
                                        <button type="button"
                                            class="btn btn-white border shadow-sm d-flex align-items-center justify-content-center gap-2 py-2 bg-white"
                                            id="btnPickPdf">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg"
                                                alt="Google Drive" style="width: 20px; height: 20px;">
                                            <span>Pilih File dari Google Drive</span>
                                        </button>
                                    </div>
                                    <div id="pdfPreview" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Dokumentasi</label>
                                    <input type="file" name="images[]" accept="image/*" class="form-control"
                                        multiple>

                                    <!-- Google Drive -->
                                    <div class="mb-0">
                                        <label class="form-label small text-muted fw-bold mb-1">Ambil dari
                                            Google Drive</label>
                                        <input type="hidden" name="gdrive_image_json" id="gdrive_image_json"
                                            value="{{ old('gdrive_image_json') }}">

                                        <div class="d-grid">
                                            <button type="button"
                                                class="btn btn-white border shadow-sm d-flex align-items-center justify-content-center gap-2 py-2 bg-white"
                                                id="btnPickImage">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg"
                                                    alt="Google Drive" style="width: 20px; height: 20px;">
                                                <span>Pilih File dari Google Drive</span>
                                            </button>
                                        </div>
                                        <div id="imagePreview" class="mt-2 row g-2"></div>
                                    </div>

                                    <div class="form-text small">Maksimal 5 gambar (JPG, PNG).</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kontrak (Opsional)</label>
                                    <div class="mb-2">
                                        <input type="text" name="nomor_kontrak" class="form-control mb-2"
                                            value="{{ old('nomor_kontrak', $project->nomor_kontrak) }}"
                                            placeholder="Nomor Kontrak/SPK">
                                        <input type="date" name="tanggal_kontrak" class="form-control"
                                            value="{{ old('tanggal_kontrak', optional($project->tanggal_kontrak)->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg fw-bold shadow-sm" type="submit">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ url()->previous() ?: route('projects.show', $project) }}"
                                class="btn btn-outline-secondary"
                                onclick="return confirm('Batalkan perubahan? Data yang belum disimpan akan hilang.');">
                                Batal
                            </a>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Biaya Formatting
            const biayaDisplay = document.getElementById('biaya_display');
            const biayaReal = document.getElementById('biaya_real');

            biayaDisplay.addEventListener('input', function(e) {
                let value = e.target.value;
                let cleanValue = value.replace(/[^0-9]/g, '');
                biayaReal.value = cleanValue;
                if (cleanValue) {
                    e.target.value = parseInt(cleanValue, 10).toLocaleString('id-ID');
                }
            });

            // TomSelect
            if (typeof TomSelect !== 'undefined') {
                new TomSelect('#ketua-select', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            }

            // Member Filter
            const searchInput = document.getElementById('member-search-input');
            const filterRadios = document.querySelectorAll('input[name="member-filter"]');
            const memberItems = document.querySelectorAll('.member-item');
            const noResultsMessage = document.getElementById('no-members-found');
            const headers = document.querySelectorAll('[data-role-header]');

            function filterAndSearchMembers() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const activeFilter = document.querySelector('input[name="member-filter"]:checked').value;
                let visibleCount = 0;
                let visibleRoles = new Set();

                memberItems.forEach(item => {
                    const name = item.dataset.name;
                    const role = item.dataset.role;
                    const isSearchMatch = name.includes(searchTerm);
                    const isFilterMatch = (activeFilter === 'semua' || activeFilter === role);

                    if (isSearchMatch && isFilterMatch) {
                        item.style.display = '';
                        visibleCount++;
                        visibleRoles.add(role);
                    } else {
                        item.style.display = 'none';
                    }
                });

                noResultsMessage.style.display = visibleCount === 0 ? '' : 'none';

                headers.forEach(header => {
                    const role = header.dataset.roleHeader;
                    if (role === 'separator') {
                        header.style.display = (visibleRoles.has('dosen') && visibleRoles.has(
                            'mahasiswa')) ? '' : 'none';
                    } else {
                        header.style.display = visibleRoles.has(role) ? '' : 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterAndSearchMembers);
            filterRadios.forEach(radio => radio.addEventListener('change', filterAndSearchMembers));
        });
    </script>

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
@endsection
