@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Publikasi</h4>
    <a class="btn btn-primary" href="{{ route('publications.create') }}">Tambah</a>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <div><h6>Impor Publikasi Cepat</h6></div>
      <hr>
      <form class="row g-2" method="POST" action="{{ route('import.crossref') }}">
        @csrf
        <div class="col-md-8">
          <input name="doi" class="form-control" placeholder="Masukkan DOI untuk impor dari Crossref" required>
        </div>
        <div class="col-md-4">
          <button class="btn btn-outline-primary w-100">Impor DOI</button>
        </div>
      </form>
      <hr>
      <form class="row g-2" method="POST" action="{{ route('import.bibtex') }}" enctype="multipart/form-data">
        @csrf
        <div class="col-md-8">
          <input type="file" name="file" class="form-control" accept=".bib,.txt" required>
        </div>
        <div class="col-md-4">
          <button class="btn btn-outline-secondary w-100">Impor BibTeX</button>
        </div>
      </form>
      <hr>
      <form class="row g-2" method="POST" action="{{ route('import.oai') }}">
        @csrf
        <div class="col-md-5">
          <input name="base_url" class="form-control" placeholder="OAI Base URL (mis. https://journal.xxx/oai)" required>
        </div>
        <div class="col-md-3">
          <input name="set" class="form-control" placeholder="Set (opsional)">
        </div>
        <div class="col-md-2">
          <input name="author_like" class="form-control" placeholder="Filter penulis (ops)">
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-success w-100">Harvest OAI</button>
        </div>
      </form>
    </div>
  </div>

    {{-- Chart --}}
    <div class="card mb-3">
        <div class="card-body">
          <p>Grafik jumlah publikasi (per-tahun)</p>
          <canvas id="chartPublikasi" height="80"></canvas>
        </div>
    </div>

  {{-- Filter --}}
  <form method="GET" class="row g-2 align-items-end mb-3">
    <div class="col-md-5">
      <label class="form-label">Cari</label>
      <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Judul / Nama jurnal">
    </div>
    <div class="col-md-2">
      <label class="form-label">Tahun</label>
      <select name="year" class="form-select">
        <option value="">Semua</option>
        @foreach($years as $y)
          <option value="{{ $y }}" @selected(request('year')==$y)>{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Urutkan</label>
      <select name="sort" class="form-select">
        <option value="latest"   @selected(request('sort')=='latest')>Terakhir diposting</option>
        <option value="year_desc"@selected(request('sort')=='year_desc')>Tahun (baru → lama)</option>
        <option value="year_asc" @selected(request('sort')=='year_asc')>Tahun (lama → baru)</option>
        <option value="name"     @selected(request('sort')=='name')>Nama A→Z</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">&nbsp;</label>
      <button class="btn btn-primary w-100">Cari</button>
    </div>
  </form>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
  (() => {
    const rows = @json($chart);
    const labels = rows.map(r => r.y);
    const data   = rows.map(r => r.c);

    const el = document.getElementById('chartPublikasi');
    if (!el) return;

    new Chart(el, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Jumlah Publikasi per Tahun',
          data,
          borderWidth: 1
        }]
      },
      options: {
        plugins: { legend: { display: false }},
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });
  })();
  </script>
  @endpush

  <div>
    <div class="card">
        <div class="card-header">Daftar Artikel</div>
            <div class="list-group list-group-flush">
                @forelse($pubs as $r)
                <a class="list-group-item list-group-item-action" href="{{ route('publications.show',$r) }}">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-bold">{{ $r->judul }}</div>
                            <i class="text-bg-light">{{ $r->jurnal ?? '—' }}</i>
                            <div>
                              @if(isset($r->penulis) && is_array($r->penulis))
                                <small>{{ implode(', ', $r->penulis) }}</small>
                              @endif
                            </div>
                        </div>
                        <span class="badge text-dark d-flex align-items-center justify-content-center">{{ $r->tahun ?? '—' }}</span>
                    </div>
                </a>
                @empty
                <div class="list-group-item">Belum ada data.</div>
                @endforelse
            </div>
      </div>
  </div>

  <div class="mt-3">{{ $pubs->withQueryString()->links() }}</div>
</div>
@endsection
