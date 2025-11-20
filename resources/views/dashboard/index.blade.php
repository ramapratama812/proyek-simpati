@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Dasbor Utama</h4>

  @if($pendingValidation > 0 || $needRevision > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
      <div>
        @if($pendingValidation > 0)
          Anda memiliki <strong>{{ $pendingValidation }}</strong> kegiatan yang
          <strong>menunggu validasi</strong> admin.
        @endif
        @if($needRevision > 0)
          @if($pendingValidation > 0)<br>@endif
          Ada <strong>{{ $needRevision }}</strong> kegiatan yang
          <strong>memerlukan revisi</strong>.
        @endif
      </div>
      <div>
        <a href="{{ route('projects.my', ['status' => 'pending']) }}"
           class="btn btn-sm btn-outline-dark">
          Lihat Kegiatan
        </a>
      </div>
    </div>
  @endif

  @if(strtolower(auth()->user()->role ?? '') !== 'mahasiswa')
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Kegiatan (Penelitian + Pengabdian)</h6>
                    <div class="display-5 fw-bold">{{ $totalKegiatan }}</div>
                    <canvas id="chartKegiatan"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Publikasi</h6>
                    <div class="display-5 fw-bold">{{ $totalPublikasi }}</div>
                    <canvas id="chartPublikasi"></canvas>
                </div>
            </div>
        </div>
    </div>
  @endif

  @if(strtolower(auth()->user()->role ?? '') !== 'mahasiswa')
    <div class="row mb-3">
        <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Kegiatan yang Saya Ketua</span>
            <a href="{{ route('projects.my') }}" class="btn btn-sm btn-outline-secondary">Kelola</a>
            </div>
            <div class="card-body">
            @if($kegiatanSayaKetua->isEmpty())
                <p class="text-muted mb-0">Belum ada data.</p>
            @else
                <ul class="list-group">
                @foreach($kegiatanSayaKetua as $p)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('projects.show', $p) }}">{{ $p->judul }}</a>
                        <div class="small text-muted">
                        {{ ucfirst($p->jenis) }} • Tahun {{ $p->tahun_pelaksanaan ?? $p->tahun_usulan ?? '-' }}
                        </div>
                    </div>
                    @include('projects._validation_badge', ['project' => $p])
                    </li>
                @endforeach
                </ul>
            @endif
            </div>
        </div>
        </div>

        <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Publikasi yang Saya Unggah</span>
            <a href="{{ route('publications.my') }}" class="btn btn-sm btn-outline-secondary">Kelola</a>
            </div>
            <div class="card-body">
            @if($publikasiSaya->isEmpty())
                <p class="text-muted mb-0">Belum ada data.</p>
            @else
                <ul class="list-group">
                @foreach($publikasiSaya as $pub)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('publications.show', $pub) }}">{{ $pub->judul }}</a>
                        <div class="small text-muted">
                        {{ $pub->jenis ?? '-' }} • {{ $pub->tahun ?? '-' }}
                        </div>
                    </div>
                    </li>
                @endforeach
                </ul>
            @endif
            </div>
        </div>
        </div>
    </div>
  @endif

  <div class="row mb-3">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Kegiatan Yang Saya Ikuti Sebagai Anggota</div>
        <div class="card-body">
          @if($kegiatanSebagaiAnggota->isEmpty())
            <p class="text-muted mb-0">Belum ada data.</p>
          @else
            <ul class="list-group">
              @foreach($kegiatanSebagaiAnggota as $p)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <a href="{{ route('projects.show', $p) }}">{{ $p->judul }}</a>
                    <div class="small text-muted">
                      Ketua: {{ optional($p->ketua)->name }} • {{ ucfirst($p->jenis) }}
                    </div>
                  </div>
                  @include('projects._validation_badge', ['project' => $p])
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Notifikasi Terbaru</span>
        </div>
        <div class="card-body">
          @if($notifications->isEmpty())
            <p class="text-muted mb-0">Tidak ada notifikasi.</p>
          @else
            <ul class="list-group list-group-flush">
              @foreach($notifications as $notif)
                <li class="list-group-item px-0">
                  <div class="small">{{ $notif->message }}</div>
                  <div class="text-muted small">
                    {{ $notif->created_at?->format('d M Y H:i') }}
                  </div>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const kegiatan = @json($activityByYear);
  const publikasi = @json($publicationByYear);

  const labelsKegiatan = kegiatan.map(x => x.tahun);
  const dataKegiatan   = kegiatan.map(x => x.total);

  const labelsPublikasi = publikasi.map(x => x.tahun);
  const dataPublikasi   = publikasi.map(x => x.total);

  if (document.getElementById('chartKegiatan')) {
    new Chart(document.getElementById('chartKegiatan').getContext('2d'), {
      type: 'bar',
      data: {
        labels: labelsKegiatan,
        datasets: [{ label: 'Jumlah Kegiatan', data: dataKegiatan }]
      }
    });
  }

  if (document.getElementById('chartPublikasi')) {
    new Chart(document.getElementById('chartPublikasi').getContext('2d'), {
      type: 'bar',
      data: {
        labels: labelsPublikasi,
        datasets: [{ label: 'Jumlah Publikasi', data: dataPublikasi }]
      }
    });
  }
</script>
@endpush
