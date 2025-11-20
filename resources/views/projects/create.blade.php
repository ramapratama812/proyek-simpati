@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Tambah Kegiatan</h4>
  <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="card p-4">
    @csrf
    <div class="row g-3">
      <label class="form-label fw-semibold">Data Akademik</label>

      <div class="col-md-4">
        <label class="form-label">Jenis Kegiatan<span class="required-asterisk">*</span></label>
        <select name="jenis" class="form-select" required>
          <option value="penelitian">Penelitian</option>
          <option value="pengabdian">Pengabdian</option>
        </select>
      </div>

      <div class="col-md-8">
        <label class="form-label">Judul<span class="required-asterisk">*</span></label>
        <input type="text" name="judul" class="form-control" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Kategori Kegiatan<span class="required-asterisk">*</span></label>
        <input type="text" name="kategori_kegiatan" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Bidang Ilmu<span class="required-asterisk">*</span></label>
        <input type="text" name="bidang_ilmu" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Skema<span class="required-asterisk">*</span></label>
        <input type="text" name="skema" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Tanggal Mulai<span class="required-asterisk">*</span></label>
        <input type="date" name="mulai" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Selesai<span class="required-asterisk">*</span></label>
        <input type="date" name="selesai" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Sumber Dana<span class="required-asterisk">*</span></label>
        <select name="sumber_dana" class="form-select" required>
            <option value="" disabled selected>Pilih sumber dana kegiatan</option>
            <option value="Mandiri">Mandiri</option>
            <option value="Hibah">Hibah</option>
        </select>
      </div>

      <div class="col-md-6">
        <label for="biaya_display" class="form-label">Biaya (Rp)<span class="required-asterisk">*</span></label>
        <input type="text" id="biaya_display" class="form-control" placeholder="Contoh: 500.000" required>
        <input type="hidden" name="biaya" id="biaya_real">
      </div>

      {{-- Script buat tampilan "Biaya" biar ada titik2nya --}}
      <script>
        // Ambil elemen input berdasarkan ID
        const biayaDisplay = document.getElementById('biaya_display');
        const biayaReal = document.getElementById('biaya_real');

        // Tambahkan event listener saat pengguna mengetik
        biayaDisplay.addEventListener('input', function(e) {
            // 1. Ambil nilai dari input yang terlihat
            let value = e.target.value;

            // 2. Hapus semua karakter kecuali angka (menghilangkan titik atau huruf)
            let cleanValue = value.replace(/[^0-9]/g, '');

            // 3. Simpan nilai angka murni ke input yang tersembunyi
            biayaReal.value = cleanValue;

            // 4. Format nilai dengan titik sebagai pemisah ribuan
            //    dan tampilkan kembali di input yang terlihat.
            //    Jika input kosong, jangan format apa-apa.
            if (cleanValue) {
                let formattedValue = parseInt(cleanValue, 10).toLocaleString('id-ID');
                e.target.value = formattedValue;
            }
        });
      </script>

      <div class="col-12">
        <label class="form-label">Abstrak<span class="required-asterisk">*</span></label>
        <textarea name="abstrak" rows="4" class="form-control"></textarea required>
      </div>

      <div class="col-12">
        <hr>
        <label class="form-label fw-semibold">Keanggotaan</label>
      </div>
        {{-- Pilihan Ketua dan Anggota Tim --}}
        <div class="col-md-6">
            <label for="ketua-select" class="form-label">Ketua Proyek<span class="required-asterisk">*</span></label>
            <p class="form-text mt-0 mb-3">Pilih dosen yang akan memimpin kegiatan ini.</p>

            {{-- Tom-Select akan mengubah <select> ini menjadi dropdown yang bisa dicari --}}
            <div class="position-relative">
                <select id="ketua-select" name="ketua_user_id" placeholder="Ketik untuk mencari nama dosen..." autocomplete="off" required>
                    <option value="">— Pilih Ketua —</option>
                    @isset($lecturers)
                        @foreach($lecturers as $l)
                            {{-- Atribut `data-old` ditambahkan untuk memulihkan nilai jika ada error validasi --}}
                            <option value="{{ $l->id }}" {{ old('ketua_user_id') == $l->id ? 'selected' : '' }}>
                                {{ $l->name }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="form-text mt-1">Hanya satu ketua yang bisa dipilih.</div>
            <div class="form-text mt-1">Ketua dapat mengelola data-data kegiatan.</div>

            {{-- Bagian buat uplod surat proposal --}}
            <hr>
            <label class="form-label">Surat Proposal<span class="required-asterisk">*</span></label>
            <input type="file" name="surat_proposal" accept="application/pdf" class="form-control" required>
            <div class="form-text">Upload file proposal dalam format PDF. Wajib diisi.</div>
        </div>

        {{-- Skrip untuk inisialisasi Tom-Select pada elemen #ketua-select --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Cek jika TomSelect sudah terdefinisi sebelum digunakan
                if (typeof TomSelect !== 'undefined') {
                    new TomSelect('#ketua-select',{
                        // Opsi untuk mencegah pengguna membuat entri baru
                        create: false,
                        // Mengurutkan item secara otomatis berdasarkan abjad
                        sortField: {
                            field: "text",
                            direction: "asc"
                        }
                    });
                } else {
                    console.error('Tom-Select.js belum dimuat. Pastikan Anda sudah memasukkan script-nya.');
                }
            });
        </script>


        <div class="col-md-6">
            <label class="form-label">Pilih Anggota Tim<span class="required-asterisk">*</span></label>
            <p class="form-text mt-0 mb-3">Pilih dosen/mahasiswa yang ikut serta dalam kegiatan.</p>

            {{-- Kontrol Pencarian dan Filter --}}
            <div class="member-controls bg-light p-2 rounded-2 mb-2 border">
                <div class="input-group mb-2">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="member-search-input" class="form-control" placeholder="Cari nama anggota...">
                </div>

                <div class="btn-group w-100" role="group" aria-label="Filter Tipe Anggota">
                    <input type="radio" class="btn-check" name="member-filter" id="filter-semua" value="semua" autocomplete="off" checked>
                    <label class="btn btn-outline-secondary btn-sm" for="filter-semua">Semua</label>

                    <input type="radio" class="btn-check" name="member-filter" id="filter-dosen" value="dosen" autocomplete="off">
                    <label class="btn btn-outline-secondary btn-sm" for="filter-dosen">Dosen</label>

                    <input type="radio" class="btn-check" name="member-filter" id="filter-mahasiswa" value="mahasiswa" autocomplete="off">
                    <label class="btn btn-outline-secondary btn-sm" for="filter-mahasiswa">Mahasiswa</label>
                </div>
            </div>

            {{-- Daftar Anggota dengan Wrapper untuk Scrolling --}}
            <div id="member-list-container" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">

                {{-- Pesan jika tidak ada hasil --}}
                <div id="no-members-found" class="text-center text-muted p-3" style="display: none;">
                    <i class="bi bi-person-exclamation" style="font-size: 1.5rem;"></i>
                    <p class="mb-0 mt-1">Anggota tidak ditemukan.</p>
                </div>

                {{-- Daftar Dosen --}}
                @isset($lecturers)
                    @if($lecturers->isNotEmpty())
                        <p class="fw-bold text-muted mb-1 small text-uppercase" data-role-header="dosen">Dosen</p>
                        @foreach($lecturers as $l)
                        <div class="form-check member-item" data-role="dosen" data-name="{{ strtolower($l->name) }}">
                            <input class="form-check-input" type="checkbox" name="anggota_user_ids[]" value="{{ $l->id }}" id="anggota-{{ $l->id }}">
                            <label class="form-check-label" for="anggota-{{ $l->id }}">
                                {{ $l->name }}
                            </label>
                        </div>
                        @endforeach
                    @endif
                @endisset

                {{-- Pemisah Visual --}}
                @if(isset($lecturers) && $lecturers->isNotEmpty() && isset($students) && $students->isNotEmpty())
                    <hr class="my-2" data-role-header="separator">
                @endif

                {{-- Daftar Mahasiswa --}}
                @isset($students)
                    @if($students->isNotEmpty())
                        <p class="fw-bold text-muted mb-1 small text-uppercase" data-role-header="mahasiswa">Mahasiswa</p>
                        @foreach($students as $s)
                        <div class="form-check member-item" data-role="mahasiswa" data-name="{{ strtolower($s->name) }}">
                            <input class="form-check-input" type="checkbox" name="anggota_user_ids[]" value="{{ $s->id }}" id="anggota-{{ $s->id }}">
                            <label class="form-check-label" for="anggota-{{ $s->id }}">
                                {{ $s->name }}
                            </label>
                        </div>
                        @endforeach
                    @endif
                @endisset
            </div>
            <div class="form-text mt-1">Anda bisa memilih satu atau lebih anggota.</div>
        </div>

        {{-- Skrip untuk fungsionalitas filter dan pencarian --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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

                    // Tampilkan/sembunyikan pesan "tidak ditemukan"
                    noResultsMessage.style.display = visibleCount === 0 ? '' : 'none';

                    // Tampilkan/sembunyikan header (Dosen/Mahasiswa)
                    headers.forEach(header => {
                        const role = header.dataset.roleHeader;
                        if (role === 'separator') {
                             // Tampilkan pemisah jika kedua role ada yang visible
                             header.style.display = (visibleRoles.has('dosen') && visibleRoles.has('mahasiswa')) ? '' : 'none';
                        } else {
                             header.style.display = visibleRoles.has(role) ? '' : 'none';
                        }
                    });
                }

                searchInput.addEventListener('input', filterAndSearchMembers);
                filterRadios.forEach(radio => radio.addEventListener('change', filterAndSearchMembers));
            });
        </script>

      <div class="col-md-3">
        <label class="form-label">Tahun Usulan</label>
        <input type="number" name="tahun_usulan" class="form-control" min="2010" max="{{ date('Y')+1 }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tahun Pelaksanaan</label>
        <input type="number" name="tahun_pelaksanaan" class="form-control" min="2010" max="{{ date('Y')+2 }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="usulan">Usulan</option>
          <option value="didanai">Didanai</option>
          <option value="berjalan">Berjalan</option>
          <option value="selesai">Selesai</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">TKT/TRL (1-9, opsional)</label>
        <input type="number" name="tkt" class="form-control" min="1" max="9">
      </div>

      <div class="col-md-6">
        <label class="form-label">Mitra (opsional)</label>
        <input type="text" name="mitra_nama" class="form-control" placeholder="Nama mitra/instansi">
      </div>
      <div class="col-md-6">
        <label class="form-label">Lokasi<span class="required-asterisk">*</span></label>
        <input type="text" name="lokasi" class="form-control" placeholder="Kota/Kabupaten, Provinsi" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Nomor Kontrak/SPK</label>
        <input type="text" name="nomor_kontrak" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Kontrak</label>
        <input type="date" name="tanggal_kontrak" class="form-control">
      </div>

      <div class="col-md-6">
        <label class="form-label">Target Luaran</label>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="jurnal_terakreditasi" id="tl1">
          <label class="form-check-label" for="tl1">Jurnal Terakreditasi</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="prosiding" id="tl2">
          <label class="form-check-label" for="tl2">Prosiding</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="hki" id="tl3">
          <label class="form-check-label" for="tl3">HKI/Paten</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="buku" id="tl4">
          <label class="form-check-label" for="tl4">Buku</label>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Kata Kunci</label>
        <input type="text" name="keywords" class="form-control" placeholder="contoh: IoT; UMKM; sistem informasi">
        <label class="form-label mt-3">Tautan Pendukung</label>
        <input type="url" name="tautan" class="form-control" placeholder="https://...">
      </div>

      <div class="col-12">
        <label class="form-label">Dokumentasi (maks 5 gambar)</label>
        <input type="file" name="images[]" accept="image/*" class="form-control" multiple>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-between">
      <a href="{{ url()->previous() ?: route('projects.index',$project) }}"
        class="btn btn-outline-secondary"
        onclick="return confirm('Batalkan perubahan dan kembali? Perubahan yang belum disimpan akan hilang.');">
        Batal
      </a>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
@endsection