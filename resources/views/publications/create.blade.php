@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Tambah Publikasi</h4>

  <form method="POST" action="{{ route('publications.store') }}" class="card p-4 shadow-sm">
    @csrf

    {{-- simpan project_id jika datang dari halaman publikasi kegiatan --}}
    {{-- <input type="hidden" name="project_id" value="{{ $projectId ?? '' }}"> --}}

    <input type="hidden" name="project_id" value="{{ old('project_id', $projectId) }}">


    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label">Judul</label>
        <input name="judul" class="form-control" value="{{ old('judul') }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Jenis</label>
        <input name="jenis" class="form-control" placeholder="article, proceeding, dst" value="{{ old('jenis') }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Jurnal/Prosiding</label>
        <input name="jurnal" class="form-control" value="{{ old('jurnal_prosiding') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tahun</label>
        <input name="tahun" type="number" class="form-control" value="{{ old('tahun') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">DOI</label>
        <input name="doi" id="doiField" class="form-control" value="{{ old('doi') }}">
      </div>
    </div>

    {{-- ====== Opsi Impor DOI (Crossref) ====== --}}
    <div class="mt-4">
      <div class="input-group">
        <input id="doiImportInput" class="form-control" placeholder="Masukkan DOI untuk impor dari Crossref">
        <button type="button" id="btnImportDoi" class="btn btn-outline-primary">Impor DOI</button>
      </div>
      <div class="form-text">Contoh: 10.1038/nphys1170 atau https://doi.org/10.1038/nphys1170</div>
    </div>

    <div class="mt-4 text-end">
      <button class="btn btn-primary">Simpan</button>
    </div>
  </form>

  <div class="mt-3">
    <a href="{{ url()->previous() ?: url('/publications') }}" class="btn btn-outline-secondary">Kembali</a>
  </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
  function normDOI(s){
    if(!s) return '';
    s = s.trim();
    return s.replace(/^https?:\/\/(dx\.)?doi\.org\//i,''); // hilangkan prefix URL jika ada
  }
  function pickYear(msg){
    const src = (msg['published-print']?.['date-parts']?.[0]
              || msg['published-online']?.['date-parts']?.[0]
              || msg['issued']?.['date-parts']?.[0]) || [];
    return src[0] || '';
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('btnImportDoi');
    if(!btn) return;
    btn.addEventListener('click', async function(){
      const raw = document.getElementById('doiImportInput').value;
      const doi = normDOI(raw);
      if(!doi){ alert('Isi DOI terlebih dahulu.'); return; }

      try{
        const res = await fetch('https://api.crossref.org/works/' + encodeURIComponent(doi));
        if(!res.ok) throw new Error('Gagal menghubungi Crossref');
        const json = await res.json();
        const m = json.message || {};

        // isi field
        document.querySelector('[name=judul]').value = (m.title && m.title[0]) || '';
        document.querySelector('[name=jurnal]').value =
          (m['container-title'] && m['container-title'][0]) || (m.publisher || '');
        document.querySelector('[name=tahun]').value = pickYear(m);
        document.querySelector('[name=doi]').value = m.DOI || doi;
        document.querySelector('[name=jenis]').value = (m.type || '').replace(/_/g,' ');

        alert('Data berhasil diimpor dari Crossref. Silakan periksa kembali sebelum menyimpan.');
      }catch(e){
        console.error(e);
        alert('Impor DOI gagal. Periksa nilai DOI atau coba lagi.');
      }
    });
  });
})();
</script>
@endpush
