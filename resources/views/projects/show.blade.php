@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">{{ $project->judul }}</h3>

  <div class="row">
    <div class="col-md-8">
      <div class="card p-3 mb-3">

        <div class="mb-3">
            <span class="badge bg-secondary text-capitalize">{{ $project->jenis }}</span>
        </div>
        <p><strong>Skema:</strong> {{ $project->skema }}</p>
        <p><strong>Bidang Ilmu:</strong> {{ $project->bidang_ilmu }}</p>
        <p><strong>Periode:</strong> {{ $project->mulai ? $project->mulai->format('d M Y') : '-' }}
           — {{ $project->selesai ? $project->selesai->format('d M Y') : '-' }}</p>
        <p><strong>Biaya:</strong> Rp {{ number_format($project->biaya,0,',','.') }}</p>
        <p><strong>Sumber Dana:</strong> {{ $project->sumber_dana }}</p>
        <p><strong>Abstrak:</strong><br>{{ $project->abstrak }}</p>

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
             @if(auth()->id() && (auth()->id() == $project->created_by || auth()->id() == $project->ketua_id))
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

    <div class="col-md-4">
      <div class="card">
        <div class="card-body">

          <h6 class="mb-2">Aksi</h6>

          <div class="d-grid gap-2">
            <a href="{{ route('projects.publications.index', $project) }}" class="btn btn-primary">
                Lihat Publikasi
            </a>
          </div>

          @if(auth()->id() && (auth()->id() == $project->created_by || auth()->id() == $project->ketua_id))
          <hr>
            <div class="d-grid gap-1">
              <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-warning">Edit</a>
              <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Hapus kegiatan ini? Tindakan tidak bisa dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100">Hapus</button>
              </form>
            </div>
          @endif

          <hr>

          <div class="d-grid gap-2">
              <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Kembali</a>
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
            enctype="multipart/form-data"
            onsubmit="return confirm('Unggah gambar sebagai dokumentasi kegiatan?');">
                @csrf
                <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
                <div class="form-text">Maks 10MB per gambar. Anda bisa memilih lebih dari satu file.</div>
                <button class="btn btn-primary mt-2">Unggah</button>
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
