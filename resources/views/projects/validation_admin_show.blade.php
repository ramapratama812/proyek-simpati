@extends('layouts.app')

@section('content')
<div class="container">
  <h4 class="mb-3">Validasi Kegiatan</h4>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-3">
        <div class="card-body">
          <h5>{{ $project->judul }}</h5>
          <p>
            <span class="badge bg-secondary text-capitalize">{{ $project->jenis }}</span>
            @include('projects._validation_badge', ['project' => $project])
          </p>

          <p><strong>Skema:</strong> {{ $project->skema }}</p>
          <p><strong>Kategori:</strong> {{ $project->kategori_kegiatan }}</p>
          <p><strong>Bidang Ilmu:</strong> {{ $project->bidang_ilmu }}</p>
          <p><strong>Ketua:</strong> {{ optional($project->ketua)->name }}</p>
          <p><strong>Tahun Usulan:</strong> {{ $project->tahun_usulan }}</p>
          <p><strong>Tahun Pelaksanaan:</strong> {{ $project->tahun_pelaksanaan }}</p>
          <p><strong>Status Kegiatan:</strong> {{ $project->status }}</p>

          <hr>
          <p><strong>Abstrak:</strong></p>
          <p>{{ $project->abstrak }}</p>

          @if($project->validation_note)
            <hr>
            <p><strong>Catatan Validasi Terakhir:</strong></p>
            <p>{{ $project->validation_note }}</p>
          @endif
        </div>
      </div>

      {{-- ✅ CARD PUBLIKASI TERKAIT --}}
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="mb-3">Publikasi Terkait</h6>

          @if($project->publications->isEmpty())
            <p class="text-muted mb-0">
              Belum ada publikasi yang dikaitkan dengan kegiatan ini.
            </p>
          @else
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Tahun</th>
                    <th>Jenis</th>
                    <th>Link</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($project->publications as $pub)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        {{ $pub->title ?? $pub->judul ?? 'Tanpa judul' }}
                      </td>
                      <td>
                        {{ $pub->year ?? $pub->tahun ?? '-' }}
                      </td>
                      <td class="text-capitalize">
                        {{ $pub->type ?? $pub->jenis ?? '-' }}
                      </td>
                      <td>
                        @php
                          $url = $pub->url
                              ?? $pub->tautan
                              ?? $pub->link
                              ?? $pub->doi
                              ?? null;
                        @endphp

                        @if($url)
                          <a href="{{ route('publications.show', $pub) }}" target="_blank">Buka</a>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="mb-2">Berkas</h6>
          @if($project->surat_proposal)
            <p>
              <a href="{{ asset('storage/'.$project->surat_proposal) }}" target="_blank" class="btn btn-sm btn-secondary w-100 mb-2">
                Lihat Surat Proposal
              </a>
            </p>
          @endif

          @if($project->surat_persetujuan)
            <p>
              <a href="{{ asset('storage/'.$project->surat_persetujuan) }}" target="_blank" class="btn btn-sm btn-success w-100 mb-2">
                Lihat Surat Persetujuan P3M
              </a>
            </p>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h6 class="mb-2">Aksi Validasi</h6>

          {{-- Setujui --}}
          <form method="POST" action="{{ route('projects.validation.approve', $project) }}" enctype="multipart/form-data" class="mb-2">
            @csrf
            <div class="mb-2">
              <label class="form-label">Catatan (opsional)</label>
              <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
            </div>
            <div class="mb-2">
              <label class="form-label">Surat Persetujuan P3M (PDF) <span class="text-danger">*</span></label>
              <input type="file" name="surat_persetujuan" class="form-control" accept="application/pdf" required>
            </div>
            <button type="submit" class="btn btn-success w-100"
                    onclick="return confirm('Setujui usulan kegiatan ini?');">
              Setujui
            </button>
          </form>

          {{-- Minta revisi --}}
          <form method="POST" action="{{ route('projects.validation.revision', $project) }}" class="mb-2">
            @csrf
            <div class="mb-2">
              <label class="form-label">Catatan Revisi <span class="text-danger">*</span></label>
              <textarea name="note" class="form-control" rows="2" required>{{ old('note') }}</textarea>
            </div>
            <button type="submit" class="btn btn-warning w-100"
                    onclick="return confirm('Kirim permintaan revisi ke dosen?');">
              Minta Revisi
            </button>
          </form>

          {{-- Tolak --}}
          <form method="POST" action="{{ route('projects.validation.reject', $project) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
              <textarea name="note" class="form-control" rows="2" required>{{ old('note') }}</textarea>
            </div>
            <button type="submit" class="btn btn-danger w-100"
                    onclick="return confirm('Tolak usulan kegiatan ini?');">
              Tolak
            </button>
          </form>

          <hr>
          <a href="{{ route('projects.validation.index') }}" class="btn btn-outline-secondary w-100">
            Kembali ke Daftar Validasi
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
