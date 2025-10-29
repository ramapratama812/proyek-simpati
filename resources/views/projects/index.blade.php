@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Daftar Kegiatan</h4>
    <a class="btn btn-primary" href="{{ route('projects.create') }}">Tambah</a>
  </div>

    {{-- Chart --}}
    <div class="card mb-3">
        <div class="card-body">
            <p>Grafik jumlah kegiatan (per-tahun)</p>
            <canvas id="chartKegiatan" height="80"></canvas>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    (() => {
        const rows = @json($chart);
        const labels = rows.map(r => r.y);
        const data   = rows.map(r => r.c);

        const el = document.getElementById('chartKegiatan');
        if (!el) return;

        new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
            label: 'Jumlah Kegiatan per Tahun',
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

    {{-- Form pencarian --}}
    {{-- Filter --}}
    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
          <label class="form-label">Cari</label>
          {{-- was: value="{{ $q }}" --}}
          <input name="q" value="{{ old('q', request('q')) }}" class="form-control"
                 placeholder="Judul / Skema kegiatan">
        </div>

        <div class="col-md-2">
          <label class="form-label">Tahun</label>
          <select name="year" class="form-select">
            <option value="">Semua</option>
            @foreach($years as $y)
              {{-- was: @selected($year == $y) --}}
              <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Jenis</label>
          <select name="type" class="form-select">
            <option value="">Semua</option>
            <option value="Penelitian" @selected(request('type')=='Penelitian')>Penelitian</option>
            <option value="Pengabdian" @selected(request('type')=='Pengabdian')>Pengabdian</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Urutkan</label>
          <select name="sort" class="form-select">
            <option value="latest"    @selected(request('sort')=='latest')>Terakhir diposting</option>
            <option value="year_desc" @selected(request('sort')=='year_desc')>Tahun (baru → lama)</option>
            <option value="year_asc"  @selected(request('sort')=='year_asc')>Tahun (lama → baru)</option>
            <option value="name"      @selected(request('sort')=='name')>Nama A→Z</option>
          </select>
        </div>

        <div class="col-md-1">
          <label class="form-label">&nbsp;</label>
          <button class="btn btn-primary w-100">Cari</button>
        </div>
    </form>

  <div class="list-group">
    @forelse($projects as $r)
      <a class="list-group-item list-group-item-action" href="{{ route('projects.show',$r) }}">
        <div class="d-flex justify-content-between">
          <div>
              <div class="fw-semibold">{{ $r->judul }}</div>
              <small class="text-muted">
                <span class="badge text-bg-secondary">Ketua: {{ $r->ketua->name }}</span>
                · {{ ucfirst($r->jenis) }} · {{ $r->skema ?? '—' }}
              </small>
          </div>
          <span class="badge text-dark d-flex align-items-center justify-content-center">{{ $r->mulai?->format('Y') ?? '—' }}</span>
        </div>
      </a>
    @empty
      <div class="list-group-item">Belum ada data.</div>
    @endforelse
  </div>
  <div class="mt-3">{{ $projects->withQueryString()->links() }}</div>
</div>
@endsection
