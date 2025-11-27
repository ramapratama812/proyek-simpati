@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">{{ $project->judul }}</h3>

  <div class="row">
    <div class="col-md-8">
      <div class="card p-3 mb-3">

        <div>
            <span class="badge bg-secondary text-capitalize">{{ $project->jenis }}</span>
        </div>

        {{-- Status validasi --}}
        <p class="mt-2 mb-1">
          <strong>Status Validasi:</strong>
          @include('projects._validation_badge', ['project' => $project])
        </p>

        @if ($project->validation_note)
          <div class="alert alert-info mt-2">
            <strong>Catatan Admin:</strong><br>
            {{ $project->validation_note }}
          </div>
        @endif

        @if ($project->surat_persetujuan)
          <p class="mt-2">
            <strong>Surat Persetujuan P3M:</strong>
            <a href="{{ asset('storage/' . $project->surat_persetujuan) }}" target="_blank">
              Lihat Surat Persetujuan
            </a>
          </p>
        @endif

        <hr>
        <p><strong>Skema:</strong> {{ $project->skema }}</p>
        <p><strong>Kategori:</strong> {{ $project->kategori_kegiatan }}</p>
        <p><strong>Bidang Ilmu:</strong> {{ $project->bidang_ilmu }}</p>

        <hr>
        @if ($project->tahun_usulan)
            <p><strong>Tahun Usulan:</strong> {{ $project->tahun_usulan }}</p>
        @endif
        <p>
            <strong>Periode:</strong> {{ $project->mulai ? $project->mulai->format('d M Y') : '-' }}
           — {{ $project->selesai ? $project->selesai->format('d M Y') : '-' }}
        </p>
        @if ($project->status)
            <p><strong>Status:</strong> {{ $project->status }}</p>
        @endif
        @if ($project->tkt)
            <p><strong>TKT:</strong> {{ $project->tkt }}</p>
        @endif

        <hr>
        <p><strong>Biaya:</strong> Rp {{ number_format($project->biaya,0,',','.') }}</p>
        <p><strong>Sumber Dana:</strong> {{ $project->sumber_dana }}</p>
        @if ($project->mitra_nama)
            <p><strong>Mitra/Instansi:</strong> {{ $project->mitra_nama }}</p>
        @endif

        <hr>
        @if ($project->target_luaran)
            <p><strong>Target Luaran:</strong></p>
            <ul>
                @foreach($project->target_luaran as $luaran)
                    <li>{{ $luaran }}</li>
                @endforeach
            </ul>
        @endif

        @if ($project->tautan)
            <p><strong>Tautan Pendukung:</strong> <a href="{{ $project->tautan }}" target="_blank">{{ $project->tautan }}</a></p>
        @endif

        <hr>
        <p><strong>Abstrak:</strong><br>{{ $project->abstrak }}</p>
        @if($project->keywords)
            <p><strong>Kata kunci:</strong><br>{{ $project->keywords }}</p>
        @endif

        <hr>
        <h5 class="mb-2">Tim Pelaksana</h5>
        <div><strong>Ketua:</strong> {{ optional($project->ketua)->name ?? '-' }}</div>
        <div><strong>Anggota:</strong>
          @if($project->members && $project->members->count())
            <ul class="mb-0">
              @foreach($project->members as $m)
                @if($m->pivot && $m->pivot->peran === 'anggota')
                  <li>{{ $m->name }}</li>
                @endif
              @endforeach
            </ul>
          @else
            <span>-</span>
          @endif
        </div>

        <hr>
        <h5 class="mb-2">Dokumentasi Kegiatan</h5>
        @if($project->images && $project->images->count())
            <div class="mt-3 d-flex flex-wrap gap-2">
                @foreach($project->images as $img)
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-img-src="{{ asset('storage/'.$img->path) }}">
                        <img src="{{ asset('storage/'.$img->path) }}" class="img-fluid rounded" style="max-width:250px; height: auto; cursor: pointer;">
                    </a>
                    @if(auth()->id() && (
                        // Batasi tombol hapus hanya untuk ketua atau pembuat (opsional)
                        // auth()->id() == $project->created_by ||
                        auth()->id() == $project->ketua_id)
                    )
                    <form class="position-absolute top-0 end-0 m-1" method="POST"
                            action="{{ route('projects.images.destroy', [$project, $img]) }}"
                            onsubmit="return confirm('Hapus gambar ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">&times;</button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <p>Belum ada dokumentasi.</p>
        @endif

      </div>
    </div>

    {{-- Update blok kartu aksi --}}
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">

          <h6 class="mb-2">Aksi</h6>

          <div class="d-grid gap-1 mb-2">
            <a href="{{ route('projects.publications.index', $project) }}" class="btn btn-primary">
              Lihat Publikasi
            </a>
            <a href="{{ asset('storage/' . $project->surat_proposal) }}" target="_blank" class="btn btn-secondary">
              Lihat PDF Surat Proposal
            </a>
          </div>

          @php
            $userId          = auth()->id();
            $isKetuaOrCreator = $userId && ($userId == $project->ketua_id || $userId == $project->created_by);
          @endphp

          {{-- Tombol Ajukan Validasi (hanya ketua/pembuat, dan hanya saat draft / revisi) --}}
          @if($isKetuaOrCreator && in_array($project->validation_status, ['draft','revision_requested']))
            <form method="POST"
                  action="{{ route('projects.submitValidation', $project) }}"
                  class="mb-2"
                  onsubmit="return confirm('Ajukan kegiatan ini untuk divalidasi admin?');">
              @csrf
              <button type="submit" class="btn btn-success w-100">
                Ajukan Validasi Kegiatan
              </button>
            </form>
          @endif

          {{-- Tombol Edit/Hapus (tidak muncul lagi jika sudah approved) --}}
          @if($isKetuaOrCreator && $project->validation_status !== 'approved')
            <hr>
            <div class="d-grid gap-1 mb-2">
              <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-warning">
                Edit
              </a>
              <form method="POST"
                    action="{{ route('projects.destroy', $project) }}"
                    onsubmit="return confirm('Hapus kegiatan ini? Tindakan tidak bisa dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100">
                  Hapus
                </button>
              </form>
            </div>
          @endif

          <hr>

          <div class="d-grid gap-2">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
              Kembali
            </a>
          </div>

        </div>
      </div>

      @php
          $userId = auth()->id();
          $isParticipant = $userId && ($userId == $project->ketua_id || $project->members->contains('id', $userId));
      @endphp

      @if($isParticipant)
        <div class="card p-3 mt-3">
          <h6 class="mb-2">Tambah Dokumentasi</h6>
          <form method="POST" action="{{ route('projects.images.store', $project) }}"
                enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
              <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Unggah Gambar</button>
          </form>
        </div>
      @endif

    </div>


  </div>
</div>

<!-- Modal untuk Tampilan Gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="" id="modalImage" class="img-fluid w-100">
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const imageModal = document.getElementById('imageModal');
  imageModal.addEventListener('show.bs.modal', event => {
    const triggerElement = event.relatedTarget;
    const imgSrc = triggerElement.getAttribute('data-bs-img-src');
    const modalImage = imageModal.querySelector('#modalImage');
    modalImage.src = imgSrc;
  });
</script>
@endpush
