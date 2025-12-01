<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPATI – Lengkapi Pendaftaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* GLOBAL */
        body {
            margin: 0;
            background: #ffffff; /* BACKGROUND PUTIH */
            font-family: "Inter", sans-serif;
            color: #222;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        /* CARD */
        .register-container {
            width: 100%;
            max-width: 650px; /* LEBAR */
            background: #ffffff;
            padding: 45px 50px;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.18); /* BAYANGAN DIPERTEBAL */
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* TITLE */
        .title {
            font-size: 34px;
            font-weight: 800; /* Dibuat lebih tebal */
            text-align: center;
            color: #222;
        }

        .subtitle {
            text-align: center;
            font-size: 16px;
            color: #666;
            margin-bottom: 35px;
        }

        /* USER INFO BOX */
        .info-box {
            background: #f7f7f7;
            border-left: 4px solid #87CEEB; /* Biru Muda Aksen */
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 35px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .info-box strong {
            font-weight: 700;
        }

        /* FORM INPUT */
        .form-label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px;
            font-size: 15px;
            border: 1px solid #d6d6d6;
            transition: 0.2s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05); /* SHADOW PADA INPUT */
        }

        .form-control:focus {
            border-color: #87CEEB; /* Fokus border Biru Muda */
            /* Shadow fokus menggunakan warna Biru Muda dengan transparansi */
            box-shadow: 0 0 0 4px rgba(135, 206, 235, .25);
        }

        .btn-toggle {
            border: 1px solid #d6d6d6;
            border-left: none;
            border-radius: 0 12px 12px 0;
            background: #fff;
            color: #666;
        }

        /* SUBMIT BUTTON */
        .btn-submit {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            /* Gradient menggunakan Biru Muda */
            background: linear-gradient(135deg, #87CEEB, #63B8D8);
            color: #FFFFFF; /* DIPASTIKAN PUTIH MURNI */
            font-weight: 600;
            font-size: 16px;
            border: none;
            transition: 0.2s;
            /* Bayangan tombol yang tegas menggunakan Biru Muda dengan transparansi */
            box-shadow: 0 4px 15px rgba(135, 206, 235, 0.6);
        }

        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

<div class="register-container">

    <h1 class="title">SIMPATI</h1>
    <p class="subtitle">Lengkapi Informasi untuk Pendaftaran</p>

    <div class="info-box">
        <strong>Nama: {{ $google['name'] ?? 'Pengguna' }}</strong><br>
        Email: {{ $google['email'] ?? 'Tidak diketahui' }}<br>
        Role: {{ ucfirst($google['role'] ?? 'Tidak diketahui') }}
    </div>

    <form method="POST" action="{{ route('register.google.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password baru" required>
                <button type="button" class="btn btn-toggle" onclick="toggleVisibility('passwordInput', 'passwordIcon')">
                    <i class="bi bi-eye-slash" id="passwordIcon"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="confirmInput" class="form-control" placeholder="Ulangi password" required>
                <button type="button" class="btn btn-toggle" onclick="toggleVisibility('confirmInput', 'confirmIcon')">
                    <i class="bi bi-eye-slash" id="confirmIcon"></i>
                </button>
            </div>
        </div>

        @php
            $label = ($google['role'] ?? '') === 'dosen' ? 'NIDN/NIP' : 'NIM';
        @endphp

        <div class="mb-4">
            <label class="form-label">{{ $label }}</label>
            <input type="text" name="identity" class="form-control" placeholder="Masukkan {{ $label }}" required>
        </div>

        <button class="btn-submit" type="submit">Kirim Permohonan</button>

    </form>

</div>

<script>
/**
 * Fungsi untuk mengubah tipe input password (toggle show/hide)
 */
function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye-slash", "bi-eye");
    } else {
        input.type = "password";
        icon.classList.replace("bi-eye", "bi-eye-slash");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
