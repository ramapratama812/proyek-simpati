@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f8faff;">
  <form method="POST" action="{{ route('register.post') }}"
        class="card p-5 shadow-lg border-0"
        style="max-width: 760px; width: 100%; border-radius: 22px;">
    @csrf

    <h4 class="mb-4 text-center fw-bold" style="color:#0062ff;">Daftar Akun SIMPATI</h4>

    {{-- Nama & Username --}}
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Nama Lengkap</label>
        <input name="name" class="form-control rounded-3 shadow-sm border-0"
               style="background-color:#f4f7fc;" required value="{{ old('name') }}">
        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Username</label>
        <input name="username" class="form-control rounded-3 shadow-sm border-0"
               style="background-color:#f4f7fc;" required value="{{ old('username') }}">
        @error('username')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
    </div>

    {{-- Email --}}
    <div class="mb-3">
      <label class="form-label fw-semibold">Email</label>
      <input type="email" name="email" class="form-control rounded-3 shadow-sm border-0"
             style="background-color:#f4f7fc;" required value="{{ old('email') }}">
      @error('email')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    {{-- Password --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="position-relative">
                <input type="password" name="password" id="passwordInput"
                    class="form-control rounded-3 shadow-sm border-0"
                    style="background-color:#f4f7fc;" required>

                @error('password')<small class="text-danger">{{ $message }}</small>@enderror

                <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer;" onclick="toggleVisibility('passwordInput', 'passwordIcon')">
                    <i class="bi bi-eye-slash text-secondary" id="passwordIcon" style="font-size:1.2rem;"></i>
                </span>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Konfirmasi Password</label>
            <div class="position-relative">
                <input type="password" name="password_confirmation" id="confirmInput"
                    class="form-control rounded-3 shadow-sm border-0"
                    style="background-color:#f4f7fc;" required>

                @error('password_confirmation')<small class="text-danger">{{ $message }}</small>@enderror

                <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer;" onclick="toggleVisibility('confirmInput', 'confirmIcon')">
                    <i class="bi bi-eye-slash text-secondary" id="confirmIcon" style="font-size:1.2rem;"></i>
                </span>
            </div>
        </div>
    </div>

    {{-- Role --}}
    <div class="mb-4">
      <label class="form-label fw-semibold">Daftar Sebagai</label>
      <select name="role" id="roleSelect" class="form-select rounded-3 shadow-sm border-0"
              style="background-color:#f4f7fc;" required>
        <option value="">-- Pilih Role --</option>
        <option value="mahasiswa" {{ old('role')==='mahasiswa'?'selected':'' }}>Mahasiswa</option>
        <option value="dosen" {{ old('role')==='dosen'?'selected':'' }}>Dosen</option>
      </select>
      @error('role')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    {{-- NIM / NIDN --}}
    <div class="mb-3 {{ old('role') === 'mahasiswa' ? '' : 'd-none' }}" id="nimField">
      <label class="form-label fw-semibold">NIM</label>
      <input name="nim" class="form-control rounded-3 shadow-sm border-0"
             style="background-color:#f4f7fc;" value="{{ old('nim') }}">
      @error('nim')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 {{ old('role') === 'dosen' ? '' : 'd-none' }}" id="nidnField">
      <label class="form-label fw-semibold">NIDN/NIP</label>
      <input name="nidn" class="form-control rounded-3 shadow-sm border-0"
             style="background-color:#f4f7fc;" value="{{ old('nidn') }}">
      @error('nidn')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    {{-- Tombol --}}
    <button class="btn w-100 py-2 fw-semibold text-white"
            style="background: linear-gradient(90deg, #007bff, #0056d2); border: none; border-radius: 10px;">
      Ajukan Pendaftaran
    </button>

    <p class="mt-4 text-center text-secondary">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none">Masuk</a>
    </p>

    <div class="d-flex align-items-center my-3">
        <hr class="flex-grow-1">
        <span class="px-2 text-muted">atau</span>
        <hr class="flex-grow-1">
    </div>

    {{-- Google Auth --}}
    <a href="{{ route('auth.google.redirect') }}"
        class="btn w-100 py-2 fw-semibold d-flex align-items-center justify-content-center border"
        style="background-color:rgb(231, 231, 231); border-radius:10px; border-color:#ddd;">
       <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google" width="22" class="me-2">
       <span class="text-secondary">Daftar dengan Google</span>
    </a>
  </form>
</div>

{{-- Script untuk menampilkan field sesuai role --}}
<script>
document.getElementById('roleSelect').addEventListener('change', function() {
  const role = this.value;
  const nimField = document.getElementById('nimField');
  const nidnField = document.getElementById('nidnField');

  nimField.classList.add('d-none');
  nidnField.classList.add('d-none');

  if (role === 'mahasiswa') {
    nimField.classList.remove('d-none');
  } else if (role === 'dosen') {
    nidnField.classList.remove('d-none');
  }
});

// Fungsi tunggal untuk menangani toggle password
function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        input.type = "password";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
}
</script>
@endsection
