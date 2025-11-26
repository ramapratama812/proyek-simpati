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
            <div class="position-relative">
                <input type="password" name="password" id="passwordInput" class="form-control" required autocomplete="new-password">

                <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer;" onclick="toggleVisibility('passwordInput', 'passwordIcon')">
                    <i class="bi bi-eye-slash text-secondary" id="passwordIcon" style="font-size:1.2rem;"></i>
                </span>
            </div>
        </div>

        <div class="mb-3">
            <label>Konfirmasi Password</label>
            <div class="position-relative">
                <input type="password" name="password_confirmation" id="confirmInput" class="form-control" required>

                <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer;" onclick="toggleVisibility('confirmInput', 'confirmIcon')">
                    <i class="bi bi-eye-slash text-secondary" id="confirmIcon" style="font-size:1.2rem;"></i>
                </span>
            </div>
        </div>

        <div class="mb-3">
            <label>{{ $google['role'] === 'dosen' ? 'NIDN/NIP' : 'NIM' }}</label>
            <input type="text" name="identity" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Kirim Permohonan</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
/**
 * Fungsi tunggal untuk menangani toggle password
 * @param {string} inputId - ID dari elemen input
 * @param {string} iconId - ID dari elemen icon
 */
function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === "password") {
        // Ubah ke text (show)
        input.type = "text";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        // Kembalikan ke password (hide)
        input.type = "password";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
}
</script>
@endsection
