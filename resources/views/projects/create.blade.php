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
                    <a href="{{ route('projects.index') }}"
                        class="text-white-50 text-decoration-none small fw-bold text-uppercase">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <h1 class="fw-bold mb-0">Tambah Kegiatan Baru</h1>
                <p class="text-white-50 mb-0 mt-2">Isi formulir di bawah ini untuk mengajukan kegiatan penelitian atau
                    pengabdian baru.</p>
            </div>
        </div>

        <div class="container pb-5">
            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                @csrf

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
                                            <option value="penelitian">Penelitian</option>
                                            <option value="pengabdian">Pengabdian</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Judul Kegiatan<span
                                                class="required-asterisk">*</span></label>
                                        <input type="text" name="judul" class="form-control"
                                            placeholder="Masukkan judul lengkap kegiatan..." required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kategori Kegiatan<span
                                                class="required-asterisk">*</span></label>
                                        <input type="text" name="kategori_kegiatan" class="form-control"
                                            placeholder="Contoh: Penelitian Dasar" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bidang Ilmu<span
                                                class="required-asterisk">*</span></label>
                                        <input type="text" name="bidang_ilmu" class="form-control"
                                            placeholder="Contoh: Teknik Informatika" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Skema<span class="required-asterisk">*</span></label>
                                        <input type="text" name="skema" class="form-control"
                                            placeholder="Contoh: Penelitian Dosen Pemula" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Abstrak<span class="required-asterisk">*</span></label>
                                        <textarea name="abstrak" rows="5" class="form-control" placeholder="Ringkasan singkat mengenai kegiatan..."
                                            required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Kata Kunci</label>
                                        <input type="text" name="keywords" class="form-control"
                                            placeholder="Contoh: IoT; UMKM; Sistem Informasi (pisahkan dengan titik koma)">
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
                                        <input type="date" name="mulai" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Selesai<span
                                                class="required-asterisk">*</span></label>
                                        <input type="date" name="selesai" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tahun Usulan</label>
                                        <input type="number" name="tahun_usulan" class="form-control" min="2010"
                                            max="{{ date('Y') + 1 }}" value="{{ date('Y') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tahun Pelaksanaan</label>
                                        <input type="number" name="tahun_pelaksanaan" class="form-control" min="2010"
                                            max="{{ date('Y') + 2 }}" value="{{ date('Y') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sumber Dana<span
                                                class="required-asterisk">*</span></label>
                                        <select name="sumber_dana" class="form-select" required>
                                            <option value="" disabled selected>Pilih sumber dana...</option>
                                            <option value="Mandiri">Mandiri</option>
                                            <option value="Hibah">Hibah</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="biaya_display" class="form-label">Biaya (Rp)<span
                                                class="required-asterisk">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="text" id="biaya_display" class="form-control"
                                                placeholder="0" required>
                                        </div>
                                        <input type="hidden" name="biaya" id="biaya_real">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Lokasi Pelaksanaan<span
                                                class="required-asterisk">*</span></label>
                                        <input type="text" name="lokasi" class="form-control"
                                            placeholder="Kota/Kabupaten, Provinsi" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mitra (Opsional)</label>
                                        <input type="text" name="mitra_nama" class="form-control"
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
                                                            {{ old('ketua_user_id') == $l->id ? 'selected' : '' }}>
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
                                                                    id="anggota-{{ $l->id }}">
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
                                                                    id="anggota-{{ $s->id }}">
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
                                        <option value="usulan">Usulan</option>
                                        <option value="didanai">Didanai</option>
                                        <option value="berjalan">Berjalan</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">TKT/TRL (1-9)</label>
                                    <input type="number" name="tkt" class="form-control" min="1"
                                        max="9" placeholder="Opsional">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Target Luaran</label>
                                    <div class="card card-body bg-light border-0 p-2">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="jurnal_terakreditasi" id="tl1">
                                            <label class="form-check-label" for="tl1">Jurnal Terakreditasi</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="prosiding" id="tl2">
                                            <label class="form-check-label" for="tl2">Prosiding</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="hki" id="tl3">
                                            <label class="form-check-label" for="tl3">HKI/Paten</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_luaran[]"
                                                value="buku" id="tl4">
                                            <label class="form-check-label" for="tl4">Buku</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tautan Pendukung</label>
                                    <input type="url" name="tautan" class="form-control" placeholder="https://...">
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
                                    <label class="form-label">Surat Proposal<span
                                            class="required-asterisk">*</span></label>
                                    <input type="file" name="surat_proposal" accept="application/pdf"
                                        class="form-control" required>
                                    <div class="form-text small">Format PDF. Wajib diisi.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dokumentasi</label>
                                    <input type="file" name="images[]" accept="image/*" class="form-control"
                                        multiple>
                                    <div class="form-text small">Maksimal 5 gambar (JPG, PNG).</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kontrak (Opsional)</label>
                                    <div class="mb-2">
                                        <input type="text" name="nomor_kontrak" class="form-control mb-2"
                                            placeholder="Nomor Kontrak/SPK">
                                        <input type="date" name="tanggal_kontrak" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg fw-bold shadow-sm" type="submit">
                                <i class="bi bi-save me-2"></i> Simpan Kegiatan
                            </button>
                            <a href="{{ url()->previous() ?: route('projects.index') }}"
                                class="btn btn-outline-secondary"
                                onclick="return confirm('Batalkan pengisian? Data yang belum disimpan akan hilang.');">
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
@endsection
