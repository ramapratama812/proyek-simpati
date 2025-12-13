@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 900px; width: 100%;">

            {{-- ================== HEADER ================== --}}
            <div class="py-4 text-center text-white header-mahasiswa">
                <i class="bi bi-person-badge-fill display-5 mb-2 d-block"></i>
                <h2 class="fw-bolder mb-0 text-uppercase">Profil Mahasiswa</h2>
                <p class="text-white-50 small mb-0">
                    NIM: {{ $mahasiswa->nim ?? '-' }}
                </p>
            </div>

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0 text-center">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- BODY --}}
            <div class="card-body" style="background-color: #fbfbff; padding: 3rem;">

                {{-- ==================== VIEW MODE ==================== --}}
                <div id="viewMode">

                    <h4 class="fw-bold mb-4" style="color:#001F4D;">
                        <i class="bi bi-person-lines-fill me-2"></i> Detail Mahasiswa
                    </h4>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bio-box">
                                <p class="text-muted small mb-0">Nama</p>
                                <p class="fw-bold mb-0">{{ $mahasiswa->nama ?? $user->name ?? '-' }}</p>
                            </div>

                            <div class="bio-box">
                                <p class="text-muted small mb-0">Email</p>
                                <p class="fw-bold mb-0">{{ $user->email ?? '-' }}</p>
                            </div>

                            <div class="bio-box">
                                <p class="text-muted small mb-0">NIM</p>
                                <p class="fw-bold mb-0">{{ $mahasiswa->nim ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bio-box">
                                <p class="text-muted small mb-0">Jenis Kelamin</p>
                                <p class="fw-bold mb-0">{{ $mahasiswa->jenis_kelamin ?? '-' }}</p>
                            </div>

                            <div class="bio-box">
                                <p class="text-muted small mb-0">Semester</p>
                                <p class="fw-bold mb-0">{{ $mahasiswa->semester ?? '-' }}</p>
                            </div>

                            <div class="bio-box">
                                <p class="text-muted small mb-0">Status Aktivitas</p>
                                @php $status = $mahasiswa->status_aktivitas ?? 'Aktif'; @endphp

                                @if($status == 'Aktif')
                                    <span class="badge bg-success px-3 py-2">Aktif</span>
                                @elseif($status == 'Cuti')
                                    <span class="badge bg-warning text-dark px-3 py-2">Cuti</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ================= KEGIATAN / PROYEK ================= --}}
                    <hr class="my-5">

                    <h4 class="fw-bold mb-4" style="color:#001F4D;">
                        <i class="bi bi-people-fill me-2 text-info"></i>
                        Kegiatan yang Diikuti
                    </h4>

                    @php
                        $memberships = $mahasiswa->user->projectMembers ?? collect();
                    @endphp

                    @if ($memberships->isEmpty())
                        <div class="alert alert-light border text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Mahasiswa ini belum mengikuti kegiatan atau proyek.
                        </div>
                    @else
                        <ul class="list-group mb-4">
                            @foreach ($memberships as $member)
                                @if ($member->project)
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                            <a href="{{ route('research-projects.show', $member->project->id) }}"
                                               class="fw-bold text-primary text-decoration-none">
                                                {{ $member->project->judul }}
                                            </a>

                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-person-badge"></i>
                                                Peran: {{ ucfirst($member->peran ?? 'anggota') }}
                                            </small>
                                        </div>

                                        <span class="badge bg-info text-dark">
                                            {{ ucfirst($member->project->status ?? 'aktif') }}
                                        </span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                    {{-- ================= BUTTON ================= --}}
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>

                        <div class="d-flex gap-2">
                            <button id="editBtn" type="button" class="btn btn-edit-profile rounded-pill px-4 fw-semibold">
                                <i class="bi bi-pencil-square"></i> Edit Profil
                            </button>

                            <form action="{{ route('profile.destroy') }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-semibold">
                                    <i class="bi bi-trash"></i> Hapus Akun
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ==================== EDIT MODE ==================== --}}
                <form id="editMode" action="{{ route('profile.update') }}" method="POST" style="display:none;">
                    @csrf
                    @method('PUT')

                    <h4 class="fw-bold mb-4" style="color:#001F4D;">
                        <i class="bi bi-pencil-square me-2"></i> Edit Profil
                    </h4>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <input type="text" name="name" class="form-control mb-3"
                                   value="{{ $mahasiswa->nama ?? $user->name }}">

                            <input type="email" class="form-control mb-3" value="{{ $user->email }}" disabled>

                            <input type="text" name="nim" class="form-control mb-3"
                                   value="{{ $mahasiswa->nim ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <select name="jenis_kelamin" class="form-select mb-3">
                                <option value="Laki-laki" {{ ($mahasiswa->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ ($mahasiswa->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>

                            <input type="number" min="1" name="semester" class="form-control mb-3"
                                   value="{{ $mahasiswa->semester ?? '' }}">

                            <select name="status_aktivitas" class="form-select">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                                <option value="Cuti">Cuti</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" id="cancelEdit" class="btn btn-outline-secondary rounded-pill px-4">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    editBtn.onclick = () => { viewMode.style.display='none'; editMode.style.display='block'; }
    cancelEdit.onclick = () => { editMode.style.display='none'; viewMode.style.display='block'; }
});
</script>
@endpush

<style>
.header-mahasiswa {
    background: linear-gradient(135deg, #001F4D, #0a3d62);
    border-bottom: 5px solid #ffc107;
}
.bio-box {
    background: #fff;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,.04);
}
.btn-edit-profile {
    background: #ffc107;
    border: none;
}
body { background: #f3f4ff; }
</style>
@endsection
