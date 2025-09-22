@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh">
  <form method="POST" action="{{ route('register.post') }}" class="card p-4" style="max-width:520px; width:100%">
    @csrf
    <h5 class="mb-3 text-center">Daftar Akun SIMPATI</h5>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input name="name" class="form-control" required value="{{ old('name') }}">
        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" required value="{{ old('username') }}">
        @error('username')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
      @error('email')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
        @error('password')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Daftar sebagai</label>
      <select class="form-select" name="role" required>
        <option value="mahasiswa" {{ old('role')==='mahasiswa'?'selected':'' }}>Mahasiswa</option>
        <option value="dosen" {{ old('role')==='dosen'?'selected':'' }}>Dosen</option>
      </select>
    </div>
    <button class="btn btn-primary w-100">Daftar</button>
    <p class="mt-3 text-center">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
  </form>
</div>
@endsection
