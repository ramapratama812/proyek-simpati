@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Dasbor Utama</h4>
  </div>

  {{-- Notifikasi sekali tampil --}}
  @if(isset($notifications) && $notifications->count())
    @foreach($notifications as $n)
        <div class="alert alert-info alert-dismissible fade show mt-2" role="alert">
        {{ $n->message }}
        @if($n->project_id)
            <a href="{{ route('projects.show', $n->project_id) }}" class="ms-2">Lihat</a>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
  @endif

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">

            <div class="h6 text-muted">Total Kegiatan (Penelitian + Pengabdian)</div>
            <div class="display-6">{{ $projectCount }}</div>

            <hr>
            <div class="card">
                <div class="card-header">Grafik Kegiatan per Tahun</div>
                <div class="card-body">
                    <canvas id="projectsChart" width="400" height="200"></canvas>
                </div>
            </div>

        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">

            <div class="h6 text-muted">Total Publikasi</div>
            <div class="display-6">{{ $publicationCount }}</div>

            <hr>
            <div class="card">
                <div class="card-header">Grafik Publikasi per Tahun</div>
                <div class="card-body">
                    <canvas id="publicationsChart" width="400" height="200"></canvas>
                </div>
            </div>

        </div>
      </div>
    </div>
  </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Daftar Kegiatan yang Saya Ketuai</div>
                <ul class="list-group list-group-flush">
                @forelse($projects as $project)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                    <a href="{{ route('projects.show', $project) }}" class="fw-semibold">{{ $project->judul }}</a>
                    <span class="badge text-bg-light ms-2">{{ ucfirst($project->jenis) }}</span>
                    @if($isAdmin && $project->ketua)
                        <span class="badge bg-secondary ms-2">Ketua: {{ $project->ketua->name }}</span>
                    @endif
                    </div>
                </li>
                @empty
                <li class="list-group-item">Belum ada data.</li>
                @endforelse
                </ul>
            </div>
        </div>
    <div class="col-md-6">

    <div class="col-md-12">
      <div class="card">
        <div class="card-header">Daftar Publikasi yang Saya Unggah</div>
        <ul class="list-group list-group-flush">
          @forelse($pubs as $x)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <a href="{{ route('publications.show',$x) }}" class="fw-semibold">{{ $x->judul }}</a>
                @if($isAdmin && isset($x->owner))
                  <span class="badge bg-secondary ms-2">Oleh: {{ $x->owner->name }}</span>
                @endif
              </div>
              @if($x->tahun)
                <span class="badge text-bg-light">{{ $x->tahun }}</span>
              @endif
            </li>
          @empty
            <li class="list-group-item">Belum ada data.</li>
          @endforelse
        </ul>
      </div>
    </div>

</div>

<div>
    <hr>
</div>

{{-- Kegiatan yang saya ikuti sebagai anggota --}}
<div>
  <div class="col-12">
    <div class="card">
      <div class="card-header">Kegiatan Yang Saya Ikuti Sebagai Anggota</div>
      <ul class="list-group list-group-flush">
        @forelse($memberProjects as $x)
          <li class="list-group-item">
            <a href="{{ route('projects.show',$x) }}" class="fw-semibold">{{ $x->judul }}</a>
            @if($x->ketua)<span class="badge text-bg-light ms-2">Ketua: {{ $x->ketua->name }}</span>@endif
            <span class="badge bg-secondary ms-2 text-capitalize">{{ $x->jenis }}</span>
          </li>
        @empty
          <li class="list-group-item">Belum ada data.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

@if($isAdmin)
    <div class="col-md-12 mb-4">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-check"></i> Validasi Usulan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3>{{ \App\Models\ResearchProject::where('validation_status', 'submitted')->count() }}</h3>
                            <p>Menunggu Review</p>
                            <a href="{{ route('admin.validations.index', ['status' => 'submitted']) }}"
                               class="btn btn-sm btn-warning">Lihat</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3>{{ \App\Models\ResearchProject::where('validation_status', 'under_review')->count() }}</h3>
                            <p>Sedang Review</p>
                            <a href="{{ route('admin.validations.index', ['status' => 'under_review']) }}"
                               class="btn btn-sm btn-info">Lihat</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3>{{ \App\Models\ResearchProject::where('validation_status', 'revision_requested')->count() }}</h3>
                            <p>Perlu Revisi</p>
                            <a href="{{ route('admin.validations.index', ['status' => 'revision_requested']) }}"
                               class="btn btn-sm btn-danger">Lihat</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3>{{ \App\Models\ResearchProject::where('validation_status', 'approved')
                                ->whereMonth('approved_at', now()->month)->count() }}</h3>
                            <p>Disetujui (Bulan Ini)</p>
                            <a href="{{ route('admin.validations.index', ['status' => 'approved']) }}"
                               class="btn btn-sm btn-success">Lihat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data for projects chart
    const projectYears = @json($projectCountsByYear->keys());
    const projectCounts = @json($projectCountsByYear->values());

    const ctxProjects = document.getElementById('projectsChart').getContext('2d');
    new Chart(ctxProjects, {
        type: 'bar',
        data: {
            labels: projectYears,
            datasets: [{
                label: 'Jumlah Kegiatan',
                data: projectCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Data for publications chart
    const pubYears = @json($publicationCountsByYear->keys());
    const pubCounts = @json($publicationCountsByYear->values());

    const ctxPubs = document.getElementById('publicationsChart').getContext('2d');
    new Chart(ctxPubs, {
        type: 'bar',
        data: {
            labels: pubYears,
            datasets: [{
                label: 'Jumlah Publikasi',
                data: pubCounts,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
