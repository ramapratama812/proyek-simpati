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

  <form method="POST"
        action="{{ route('projects.update', $project) }}"
        class="card p-4"
        onsubmit="return confirm('Simpan perubahan pada data kegiatan ini?');">
    @csrf
    @method('PUT')

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Jenis</label>
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
        <input type="text" name="kategori_kegiatan" class="form-control" value="{{ old('kategori_kegiatan',$project->kategori_kegiatan) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Bidang Ilmu</label>
        <input type="text" name="bidang_ilmu" class="form-control" value="{{ old('bidang_ilmu',$project->bidang_ilmu) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Skema</label>
        <input type="text" name="skema" class="form-control" value="{{ old('skema',$project->skema) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="mulai" class="form-control" value="{{ old('mulai', optional($project->mulai)->format('Y-m-d')) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="selesai" class="form-control" value="{{ old('selesai', optional($project->selesai)->format('Y-m-d')) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Sumber Dana</label>
        <input type="text" name="sumber_dana" class="form-control" value="{{ old('sumber_dana',$project->sumber_dana) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Biaya (Rp)</label>
        <input type="number" name="biaya" min="0" step="1000" class="form-control" value="{{ old('biaya',$project->biaya) }}">
      </div>

      <div class="col-12">
        <label class="form-label">Abstrak</label>
        <textarea name="abstrak" rows="4" class="form-control">{{ old('abstrak',$project->abstrak) }}</textarea>
      </div>

      <div class="col-12"><hr></div>

      <div class="col-md-6">
        <label class="form-label">Ketua</label>
        <select name="ketua_user_id" class="form-select">
          <option value="">— Pilih Ketua —</option>
          <optgroup label="Dosen">
            @foreach($lecturers as $l)
              <option value="{{ $l->id }}" @selected(old('ketua_user_id',$project->ketua_id)==$l->id)>{{ $l->name }}</option>
            @endforeach
          </optgroup>
          <optgroup label="Mahasiswa">
            @foreach($students as $s)
              <option value="{{ $s->id }}" @selected(old('ketua_user_id',$project->ketua_id)==$s->id)>{{ $s->name }}</option>
            @endforeach
          </optgroup>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Anggota</label>
        <div class="mb-2">
          <strong class="d-block">Dosen</strong>
          <div class="row">
            @foreach($lecturers as $l)
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="anggota_user_ids[]" value="{{ $l->id }}"
                         id="anggota_l{{ $l->id }}"
                         @checked(in_array($l->id, old('anggota_user_ids',$selectedAnggota)))>
                  <label class="form-check-label" for="anggota_l{{ $l->id }}">{{ $l->name }}</label>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        <div class="mb-2">
          <strong class="d-block">Mahasiswa</strong>
          <div class="row">
            @foreach($students as $s)
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="anggota_user_ids[]" value="{{ $s->id }}"
                         id="anggota_s{{ $s->id }}"
                         @checked(in_array($s->id, old('anggota_user_ids',$selectedAnggota)))>
                  <label class="form-check-label" for="anggota_s{{ $s->id }}">{{ $s->name }}</label>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        <div class="form-text">Centang anggota yang ikut dalam kegiatan.</div>
      </div>

      <div class="col-md-3">
        <label class="form-label">Tahun Usulan</label>
        <input type="number" name="tahun_usulan" min="1990" max="{{ date('Y')+1 }}" class="form-control"
               value="{{ old('tahun_usulan',$project->tahun_usulan) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tahun Pelaksanaan</label>
        <input type="number" name="tahun_pelaksanaan" min="1990" max="{{ date('Y')+2 }}" class="form-control"
               value="{{ old('tahun_pelaksanaan',$project->tahun_pelaksanaan) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          @foreach(['usulan','didanai','berjalan','selesai'] as $st)
            <option value="{{ $st }}" @selected(old('status',$project->status)==$st)>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">TKT/TRL (1–9, opsional)</label>
        <input type="number" name="tkt" min="1" max="9" class="form-control" value="{{ old('tkt',$project->tkt) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Mitra (opsional)</label>
        <input type="text" name="mitra_nama" class="form-control" value="{{ old('mitra_nama',$project->mitra_nama) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Lokasi</label>
        <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi',$project->lokasi) }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Nomor Kontrak/SPK</label>
        <input type="text" name="nomor_kontrak" class="form-control" value="{{ old('nomor_kontrak',$project->nomor_kontrak) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Tanggal Kontrak</label>
        <input type="date" name="tanggal_kontrak" class="form-control" value="{{ old('tanggal_kontrak', optional($project->tanggal_kontrak)->format('Y-m-d')) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Lama Kegiatan (bulan)</label>
        <input type="number" name="lama_kegiatan_bulan" min="1" max="60" class="form-control"
               value="{{ old('lama_kegiatan_bulan',$project->lama_kegiatan_bulan) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Kata Kunci</label>
        <input type="text" name="keywords" class="form-control" value="{{ old('keywords',$project->keywords) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tautan Pendukung</label>
        <input type="url" name="tautan" class="form-control" value="{{ old('tautan',$project->tautan) }}">
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
