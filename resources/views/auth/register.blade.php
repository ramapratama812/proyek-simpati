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
        <input type="password" name="password" class="form-control rounded-3 shadow-sm border-0"
               style="background-color:#f4f7fc;" required>
        @error('password')<small class="text-danger">{{ $message }}</small>@enderror
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control rounded-3 shadow-sm border-0"
               style="background-color:#f4f7fc;" required>
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
    <div class="mb-3 d-none" id="nimField">
      <label class="form-label fw-semibold">NIM</label>
      <input name="nim" class="form-control rounded-3 shadow-sm border-0"
             style="background-color:#f4f7fc;" value="{{ old('nim') }}">
      @error('nim')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 d-none" id="nidnField">
      <label class="form-label fw-semibold">NIDN/NIP</label>
      <input name="nidn" class="form-control rounded-3 shadow-sm border-0"
             style="background-color:#f4f7fc;" value="{{ old('nidn') }}">
      @error('nidn')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    {{-- Tombol --}}
    <button class="btn w-100 py-2 fw-semibold text-white"
            style="background: linear-gradient(90deg, #007bff, #0056d2); border: none; border-radius: 10px;">
      Daftar
    </button>

    <p class="mt-4 text-center text-secondary">
      Sudah punya akun?
      <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none">Masuk</a>
    </p>
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
</script>
@endsection
