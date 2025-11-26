@extends('layouts.app')

@section('content')
<div class="card p-4 shadow-sm mx-auto" style="max-width:500px;">
    <h4 class="text-center mb-3 text-primary">Lengkapi Pendaftaran SIMPATI</h4>
    <p class="text-center text-muted">
        Halo, {{ $google['name'] }}<br>
        Email kamu: <strong>{{ $google['email'] }}</strong><br>
        Teridentifikasi sebagai <strong>{{ ucfirst($google['role']) }}</strong>
    </p>

    <form method="POST" action="{{ route('register.google.store') }}">
        @csrf
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>{{ $google['role'] === 'dosen' ? 'NIDN/NIP' : 'NIM' }}</label>
            <input type="text" name="identity" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Kirim Permohonan</button>
    </form>
</div>
@endsection
