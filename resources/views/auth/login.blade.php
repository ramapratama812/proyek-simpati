@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f8faff;">
  <form method="POST" action="{{ route('login.post') }}"
        class="card p-5 shadow-lg border-0"
        style="max-width:520px; width:100%; border-radius: 20px;">
    @csrf

    <h4 class="mb-4 text-center fw-bold" style="color:#0062ff;">Masuk ke SIMPATI</h4>

    @error('login')
      <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    {{-- Username --}}
    <div class="mb-4">
      <label class="form-label fw-semibold">Username atau Email</label>
      <input name="login" class="form-control form-control-lg rounded-3 shadow-sm border-0"
             style="background-color:#f4f7fc;" required value="{{ old('login') }}">
    </div>

    {{-- Password --}}
    <div class="mb-4">
      <label class="form-label fw-semibold">Password</label>
      <div class="position-relative">
        <input type="password" name="password" id="password"
               class="form-control form-control-lg rounded-3 pe-5 shadow-sm border-0"
               style="background-color:#f4f7fc;" required>
        <span class="position-absolute top-50 end-0 translate-middle-y me-3"
              style="cursor:pointer;" onclick="togglePassword()">
          <i class="bi bi-eye-slash text-secondary" id="toggleIcon" style="font-size:1.2rem;"></i>
        </span>
      </div>
    </div>

    {{-- Remember Me --}}
    <div class="mb-3 form-check">
      <input type="checkbox" name="remember" class="form-check-input" id="remember">
      <label class="form-check-label" for="remember">Ingat saya</label>
    </div>

    {{-- Button --}}
    <button class="btn w-100 py-2 fs-6 fw-semibold text-white"
            style="background: linear-gradient(90deg, #007bff, #0056d2); border: none; border-radius: 10px;">
      Masuk
    </button>

    {{-- Register Link --}}
    <p class="mt-4 text-center text-secondary">
      Belum punya akun?
      <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none">Daftar</a>
    </p>

    {{-- Login sebagai Dosen dengan akun Google --}}
    <a href="{{ route('google.redirect', ['role' => 'dosen']) }}" class="btn btn-outline-danger w-100 mb-2">
        Login / Daftar Dosen dengan Google (@politala.ac.id)
    </a>

    {{-- Login sebagai Mahasiswa dengan akun Google --}}
    <a href="{{ route('google.redirect', ['role' => 'mahasiswa']) }}" class="btn btn-outline-danger w-100">
        Login / Daftar Mahasiswa dengan Google (@mhs.politala.ac.id)
    </a>
  </form>
</div>

{{-- Script toggle password --}}
<script>
function togglePassword() {
  const password = document.getElementById('password');
  const icon = document.getElementById('toggleIcon');
  if (password.type === 'password') {
    password.type = 'text';
    icon.classList.replace('bi-eye-slash', 'bi-eye');
  } else {
    password.type = 'password';
    icon.classList.replace('bi-eye', 'bi-eye-slash');
  }
}
</script>
@endsection
