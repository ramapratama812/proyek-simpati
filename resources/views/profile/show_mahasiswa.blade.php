@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 rounded-4 overflow-hidden gradient-shadow">

        {{-- HEADER --}}
        <div class="text-center text-white py-4"
             style="background: linear-gradient(135deg, #001F4D 0%, #003580 100%);">
            <h3 class="fw-bold mb-0 text-uppercase">Profile Mahasiswa</h3>
        </div>

        {{-- BODY --}}
        <div class="card-body bg-light py-5 px-5">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success text-center mb-4 rounded-3 shadow-sm py-2">
                    {{ session('success') }}
                </div>
            @endif
            
            {{-- ==== MODE VIEW ==== --}}
            <div id="viewMode">
                <div class="row">
                    {{-- Kolom kiri --}}
                    <div class="col-md-6 mb-4">
                        <div class="mb-3">
                            <label class="text-muted d-block">Nama</label>
                            <span class="fw-semibold">{{ $mahasiswa->nama ?? $user->name }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted d-block">Email</label>
                            <span class="fw-semibold">{{ $user->email }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted d-block">NIM</label>
                            <span class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Kolom kanan --}}
                    <div class="col-md-6 mb-4">
                        <div class="mb-3">
                            <label class="text-muted d-block">Jenis Kelamin</label>
                            <span class="fw-semibold">{{ $mahasiswa->jenis_kelamin ?? '-' }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted d-block">Semester</label>
                            <span class="fw-semibold">{{ $mahasiswa->semester ?? '-' }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted d-block">Status Aktivitas</label>
                            @php $status = $mahasiswa->status_aktivitas ?? 'Aktif'; @endphp
                            @if($status === 'Aktif')
                                <span class="badge bg-success px-3 py-2">Aktif</span>
                            @elseif($status === 'Cuti')
                                <span class="badge bg-warning text-dark px-3 py-2">Cuti</span>
                            @elseif($status === 'Tidak Aktif')
                                <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                            @else
                                <span class="badge bg-light text-dark px-3 py-2">Belum Diatur</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Tombol --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('mahasiswa.index') }}"
                        class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold btn-animated">
                        ← Kembali
                    </a>

                    <div>
                        <button id="editBtn" type="button"
                            class="btn text-dark fw-semibold px-4 py-2 rounded-pill me-2 btn-animated"
                            style="background-color: #ffca28;">
                            <i class="bi bi-pencil-square"></i> Edit Profil
                        </button>

                        <form action="{{ route('profile.destroy') }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Yakin ingin hapus akun?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-outline-danger rounded-pill px-4 py-2 btn-animated">
                                Hapus Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ==== MODE EDIT ==== --}}
            <form id="editMode" action="{{ route('profile.update') }}" method="POST" style="display:none;">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Form kiri --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ $mahasiswa->nama ?? $user->name }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIM</label>
                            <input type="text" name="nim" class="form-control" value="{{ $mahasiswa->nim ?? '' }}">
                        </div>
                    </div>

                    {{-- Form kanan --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="Laki-laki" {{ $mahasiswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $mahasiswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Semester</label>
                            <input type="number" name="semester" class="form-control" value="{{ $mahasiswa->semester ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Aktivitas</label>
                            <select name="status_aktivitas" class="form-select">
                                <option value="Aktif">Aktif</option>
                                <option value="Cuti">Cuti</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="button" id="cancelEdit"
                            class="btn btn-outline-secondary rounded-pill px-4">Batal</button>

                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT TOGGLE --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('editBtn').onclick = () => {
        document.getElementById('viewMode').style.display = 'none';
        document.getElementById('editMode').style.display = 'block';
    };
    document.getElementById('cancelEdit').onclick = () => {
        document.getElementById('editMode').style.display = 'none';
        document.getElementById('viewMode').style.display = 'block';
    };
});
</script>

{{-- STYLE TAMBAHAN --}}
<style>
    body { background-color: #f5f6fa; }
    label { font-size: 0.9rem; color: #6c757d; }
    span.fw-semibold { font-size: 1rem; color: #212121; }
    .btn-animated { transition: all 0.25s ease-in-out; }
    .btn-animated:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }

    .badge { border-radius: 8px; font-size: 0.85rem; }

    .gradient-shadow {
        box-shadow: 0 10px 30px rgba(0,31,77,0.2),
                    0 20px 50px rgba(0,53,128,0.1);
        transition: all 0.3s ease-in-out;
    }
    .gradient-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 45px rgba(0,31,77,0.25),
                    0 25px 70px rgba(0,53,128,0.15);
    }
</style>
@endsection
