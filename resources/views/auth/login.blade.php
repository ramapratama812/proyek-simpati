@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh">
  <form method="POST" action="{{ route('login.post') }}" class="card p-4" style="max-width:420px; width:100%">
    @csrf
    <h5 class="mb-3 text-center">Masuk ke SIMPATI</h5>
    @error('login')<div class="alert alert-danger">{{ $message }}</div>@enderror
    <div class="mb-3">
      <label class="form-label">Username atau Email</label>
      <input name="login" class="form-control" required value="{{ old('login') }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3 form-check">
      <input type="checkbox" name="remember" class="form-check-input" id="remember">
      <label class="form-check-label" for="remember">Ingat saya</label>
    </div>
    <button class="btn btn-primary w-100">Masuk</button>
    <p class="mt-3 text-center">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
  </form>
</div>
@endsection
