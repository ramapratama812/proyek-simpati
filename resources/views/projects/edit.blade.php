@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Edit Kegiatan</h4>

  {{-- Error summary --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold mb-1">Gagal menyimpan. Periksa isian berikut:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data" class="card p-4">
    @csrf
    @method('PUT')
    <div class="row g-3">
      <label class="form-label fw-semibold">Data Akademik</label>

      <div class="col-md-4">
        <label class="form-label">Jenis Kegiatan</label>
        <select name="jenis" class="form-select" required>
          <option value="penelitian" @selected(old('jenis',$project->jenis)=='penelitian')>Penelitian</option>
          <option value="pengabdian" @selected(old('jenis',$project->jenis)=='pengabdian')>Pengabdian</option>
        </select>
      </div>

      <div class="col-md-8">
        <label class="form-label">Judul</label>
        <input type="text" name="judul" class="form-control" value="{{ old('judul',$project->judul) }}" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Kategori Kegiatan</label>
        <input type="text" name="kategori_kegiatan" class="form-control" value="{{ old('kategori_kegiatan',$project->kategori_kegiatan) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Bidang Ilmu</label>
        <input type="text" name="bidang_ilmu" class="form-control" value="{{ old('bidang_ilmu',$project->bidang_ilmu) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Skema</label>
        <input type="text" name="skema" class="form-control" value="{{ old('skema',$project->skema) }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="mulai" class="form-control" value="{{ old('mulai', optional($project->mulai)->format('Y-m-d')) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="selesai" class="form-control" value="{{ old('selesai', optional($project->selesai)->format('Y-m-d')) }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Sumber Dana</label>
        <select name="sumber_dana" class="form-select" required>
            <option value="" disabled>Pilih sumber dana kegiatan</option>
            <option value="Mandiri" @selected(old('sumber_dana',$project->sumber_dana)=='Mandiri')>Mandiri</option>
            <option value="Hibah" @selected(old('sumber_dana',$project->sumber_dana)=='Hibah')>Hibah</option>
        </select>
      </div>

      <div class="col-md-6">
        <label for="biaya_display" class="form-label">Biaya (Rp)</label>
        <input type="text" id="biaya_display" class="form-control" placeholder="Contoh: 500.000" value="{{ old('biaya', number_format($project->biaya, 0, ',', '.')) }}" required>
        <input type="hidden" name="biaya" id="biaya_real" value="{{ old('biaya',$project->biaya) }}">
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
        <label class="form-label">Abstrak</label>
        <textarea name="abstrak" rows="4" class="form-control" required>{{ old('abstrak',$project->abstrak) }}</textarea>
      </div>

      <div class="col-12">
        <hr>
        <label class="form-label fw-semibold">Keanggotaan</label>
      </div>
        {{-- Pilihan Ketua dan Anggota Tim --}}
        <div class="col-md-6">
            <label for="ketua-select" class="form-label">Ketua Proyek</label>
            <p class="form-text mt-0 mb-3">Pilih dosen yang akan memimpin kegiatan ini.</p>

            {{-- Tom-Select akan mengubah <select> ini menjadi dropdown yang bisa dicari --}}
            <div class="position-relative">
                <select id="ketua-select" name="ketua_user_id" placeholder="Ketik untuk mencari nama dosen..." autocomplete="off" required>
                    <option value="">— Pilih Ketua —</option>
                    @isset($lecturers)
                        @foreach($lecturers as $l)
                            {{-- Atribut `data-old` ditambahkan untuk memulihkan nilai jika ada error validasi --}}
                            <option value="{{ $l->id }}" {{ old('ketua_user_id', $project->ketua_id) == $l->id ? 'selected' : '' }}>
                                {{ $l->name }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="form-text mt-1">Hanya satu ketua yang bisa dipilih.</div>
            <div class="form-text mt-1">Ketua dapat mengelola data-data kegiatan.</div>

            {{-- Bagian buat upload surat proposal --}}
            <hr>
            <label class="form-label">Surat Proposal</label>
            <input type="file" name="surat_proposal" accept="application/pdf" class="form-control">
            <div class="form-text">Upload file proposal dalam format PDF jika ingin mengganti.</div>
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
            <label class="form-label">Pilih Anggota Tim</label>
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
                            <input class="form-check-input" type="checkbox" name="anggota_user_ids[]" value="{{ $l->id }}" id="anggota-{{ $l->id }}"
                                   @checked(in_array($l->id, old('anggota_user_ids',$selectedAnggota)))>
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
                            <input class="form-check-input" type="checkbox" name="anggota_user_ids[]" value="{{ $s->id }}" id="anggota-{{ $s->id }}"
                                   @checked(in_array($s->id, old('anggota_user_ids',$selectedAnggota)))>
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
        <input type="number" name="tahun_usulan" class="form-control" min="2010" max="{{ date('Y')+1 }}" value="{{ old('tahun_usulan',$project->tahun_usulan) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tahun Pelaksanaan</label>
        <input type="number" name="tahun_pelaksanaan" class="form-control" min="2010" max="{{ date('Y')+2 }}" value="{{ old('tahun_pelaksanaan',$project->tahun_pelaksanaan) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="usulan" @selected(old('status',$project->status)=='usulan')>Usulan</option>
          <option value="didanai" @selected(old('status',$project->status)=='didanai')>Didanai</option>
          <option value="berjalan" @selected(old('status',$project->status)=='berjalan')>Berjalan</option>
          <option value="selesai" @selected(old('status',$project->status)=='selesai')>Selesai</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">TKT/TRL (1-9, opsional)</label>
        <input type="number" name="tkt" class="form-control" min="1" max="9" value="{{ old('tkt',$project->tkt) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Mitra (opsional)</label>
        <input type="text" name="mitra_nama" class="form-control" placeholder="Nama mitra/instansi" value="{{ old('mitra_nama',$project->mitra_nama) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Lokasi</label>
        <input type="text" name="lokasi" class="form-control" placeholder="Kota/Kabupaten, Provinsi" value="{{ old('lokasi',$project->lokasi) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Nomor Kontrak/SPK</label>
        <input type="text" name="nomor_kontrak" class="form-control" value="{{ old('nomor_kontrak',$project->nomor_kontrak) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Kontrak</label>
        <input type="date" name="tanggal_kontrak" class="form-control" value="{{ old('tanggal_kontrak', optional($project->tanggal_kontrak)->format('Y-m-d')) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Target Luaran</label>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="jurnal_terakreditasi" id="tl1"
                 @checked(in_array('jurnal_terakreditasi', old('target_luaran', $project->target_luaran ?? [])))>
          <label class="form-check-label" for="tl1">Jurnal Terakreditasi</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="prosiding" id="tl2"
                 @checked(in_array('prosiding', old('target_luaran', $project->target_luaran ?? [])))>
          <label class="form-check-label" for="tl2">Prosiding</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="hki" id="tl3"
                 @checked(in_array('hki', old('target_luaran', $project->target_luaran ?? [])))>
          <label class="form-check-label" for="tl3">HKI/Paten</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="target_luaran[]" value="buku" id="tl4"
                 @checked(in_array('buku', old('target_luaran', $project->target_luaran ?? [])))>
          <label class="form-check-label" for="tl4">Buku</label>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Kata Kunci</label>
        <input type="text" name="keywords" class="form-control" placeholder="contoh: IoT; UMKM; sistem informasi" value="{{ old('keywords',$project->keywords) }}">
        <label class="form-label mt-3">Tautan Pendukung</label>
        <input type="url" name="tautan" class="form-control" placeholder="https://..." value="{{ old('tautan',$project->tautan) }}">
      </div>

      <div class="col-12">
        <label class="form-label">Dokumentasi (maks 5 gambar)</label>
        <input type="file" name="images[]" accept="image/*" class="form-control" multiple>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-between">
      <a href="{{ url()->previous() ?: route('projects.show',$project) }}"
         class="btn btn-outline-secondary"
         onclick="return confirm('Batalkan perubahan dan kembali? Perubahan yang belum disimpan akan hilang.');">
         Batal
      </a>
      <button class="btn btn-primary">Simpan Perubahan</button>
    </div>
  </form>
</div>
@endsection
