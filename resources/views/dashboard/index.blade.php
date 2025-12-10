@extends('layouts.app')

@section('content')
<div class="container">

  @if($needsProfile)
    <div class="alert alert-warning">
        Profil {{ $role === 'dosen' ? 'dosen' : 'mahasiswa' }} Anda belum lengkap.
        Silakan lengkapi data di menu
        <a href="{{ route('profile.edit') }}" class="alert-link">Edit Profil</a>
        agar semua fitur SIMPATI dapat digunakan.
    </div>
  @endif

  <h4 class="mb-3">Dasbor Utama</h4>

  @if($pendingValidation > 0 || $needRevision > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
      <div>
        @if($pendingValidation > 0)
          Ada <strong>{{ $pendingValidation }}</strong> kegiatan yang
          <strong>menunggu validasi</strong> admin.
          <br>
        @endif
        @if($needRevision > 0)
          @if($needRevision > 0)@endif
          Ada <strong>{{ $needRevision }}</strong> kegiatan yang
          <strong>memerlukan revisi</strong>.
        @endif
      </div>
      <div>
        <a href="{{ route('projects.my', ['status' => 'pending']) }}"
            class="btn btn-sm btn-outline-dark">
            Lihat Pending
        </a>
        <a href="{{ route('projects.my', ['status' => 'revision_requested']) }}"
            class="btn btn-sm btn-outline-dark">
            Lihat Revisi
        </a>
      </div>
    </div>
  @endif

  @if($pubPending > 0 || $pubNeedRevision > 0)
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
      <div>
        @if($pubPending > 0)
          Ada <strong>{{ $pubPending }}</strong> publikasi yang
          <strong>menunggu validasi</strong> admin.
          <br>
        @endif
        @if($pubNeedRevision > 0)
          @if($pubNeedRevision > 0)@endif
          Ada <strong>{{ $pubNeedRevision }}</strong> publikasi yang
          <strong>memerlukan revisi</strong>.
        @endif
      </div>
      <div>
        <a href="{{ route('publications.my', ['status' => 'pending']) }}"
            class="btn btn-sm btn-outline-primary">
            Lihat Pending
        </a>
        <a href="{{ route('publications.my', ['status' => 'revision_requested']) }}"
            class="btn btn-sm btn-outline-primary">
            Lihat Revisi
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
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Publikasi</h6>
                    <div class="display-5 fw-bold">{{ $totalPublikasi }}</div>
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
                        @php
                            $status = $pub->validation_status ?? 'draft';
                            [$badgeClass, $label] = match ($status) {
                                'approved'           => ['bg-success', 'Disetujui'],
                                'pending'            => ['bg-secondary', 'Pending'],
                                'revision_requested' => ['bg-warning text-dark', 'Perlu Revisi'],
                                'rejected'           => ['bg-danger', 'Ditolak'],
                                'draft'              => ['bg-light text-dark', 'Draft'],
                                default              => ['bg-light text-muted', ucfirst($status)],
                            };
                        @endphp

                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('publications.show', $pub) }}">{{ $pub->judul }}</a>
                                <div class="small text-muted">
                                    {{ $pub->jenis }} • {{ $pub->tahun ?? '-' }}
                                </div>
                            </div>
                            <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                        </div>
                  @endforeach

                </ul>
            @endif
            </div>
        </div>
        </div>
    </div>
  @endif

  <div class="row mb-3">
    <div class="col-md-12">
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
