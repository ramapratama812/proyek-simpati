@extends('layouts.portofolio')

@section('content')

<style>
    body {
        background: linear-gradient(135deg, #007bff 0%, #0056d2 50%, #003a9c 100%) !important;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
    }

    .float-login {
        position: fixed;
        top: 25px;
        right: 35px;
        z-index: 50;
        padding: 12px 25px;
        border-radius: 25px;
        border: 2px solid #ffffff;
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        background: linear-gradient(90deg, #003a9c, #0056d2);
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 58, 156, 0.3);
    }

    .float-login:hover {
        background: linear-gradient(90deg, #0056d2, #007bff);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 86, 210, 0.4);
    }

    .hero {
        padding: 180px 60px 120px 60px;
        width: 100%;
        margin: auto;
        text-align: left;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 255, 0.95) 100%);
        border-radius: 30px;
        box-shadow: 0 15px 40px rgba(0, 123, 255, 0.1);
        animation: fadeInUp 1.2s ease-in-out;
        margin: 20px;
    }

    .hero-title {
        font-size: 52px;
        font-weight: 800;
        background: linear-gradient(90deg, #007bff, #0056d2, #003a9c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
        margin-bottom: 25px;
        text-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
        animation: slideInLeft 1.2s ease-in-out 0.2s both;
    }

    .hero-desc {
        font-size: 20px;
        color: #555;
        line-height: 1.7;
        margin-bottom: 35px;
        max-width: 800px;
        animation: slideInLeft 1.2s ease-in-out 0.4s both;
    }

    .feature-list {
        animation: slideInLeft 1.2s ease-in-out 0.6s both;
    }

    /* Diperbaiki agar teks list tidak biru */
    .feature-list li {
        font-size: 18px;
        color: #333 !important;
        margin-bottom: 12px;
        list-style: none;
        position: relative;
        padding-left: 35px;
        font-weight: 500;

        /* hapus efek clip text biru */
        background: none !important;
        -webkit-background-clip: initial !important;
        -webkit-text-fill-color: #333 !important;
    }

    .feature-list li::before {
        content: "✔";
        color: #007bff;
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
        font-size: 20px;
    }

    .section {
        padding: 80px 60px;
        width: 100%;
        margin: auto;
        animation: fadeInUp 1.2s ease-in-out;
        margin: 20px;
    }

    .section:nth-child(odd) {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(0, 123, 255, 0.1) 100%);
        border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0, 123, 255, 0.2);
    }

    .section:nth-child(even) {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(0, 86, 210, 0.1) 100%);
        border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0, 86, 210, 0.2);
    }

    .section-title {
        font-size: 32px;
        font-weight: 700;
        background: linear-gradient(90deg, #007bff, #0056d2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 20px;
        border-bottom: 4px solid #0056d2;
        padding-bottom: 10px;
        display: inline-block;
    }

    .section-desc {
        font-size: 18px;
        color: #444;
        line-height: 1.8;
        max-width: 900px;
    }

    @media (max-width: 1199.98px) {
        .hero {
            padding: 150px 50px 100px 50px;
        }
        .hero-title {
            font-size: 44px;
        }
        .hero-desc {
            font-size: 18px;
            max-width: 100%;
        }
        .section {
            padding: 70px 50px;
        }
        .section-title {
            font-size: 28px;
        }
        .section-desc {
            font-size: 16px;
        }
    }

    @media (max-width: 991.98px) {
        .hero {
            padding: 120px 40px 80px 40px;
        }
        .hero-title {
            font-size: 36px;
        }
        .hero-desc {
            font-size: 16px;
        }
        .feature-list li {
            font-size: 16px;
        }
        .section {
            padding: 60px 40px;
        }
        .section-title {
            font-size: 24px;
        }
    }

    @media (max-width: 767.98px) {
        .float-login {
            top: 15px;
            right: 15px;
            padding: 10px 20px;
            font-size: 14px;
        }
        .hero {
            padding: 100px 20px 60px 20px;
            text-align: center;
            margin: 10px;
        }
        .hero-title {
            font-size: 28px;
        }
        .hero-desc {
            font-size: 14px;
        }
        .feature-list li {
            font-size: 14px;
            padding-left: 30px;
        }
        .section {
            padding: 50px 20px;
            margin: 10px;
        }
        .section-title {
            font-size: 22px;
        }
        .section-desc {
            font-size: 14px;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-60px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<a href="{{ route('login') }}" class="float-login">Login</a>

<div class="hero">
    <h1 class="hero-title">
        Sistem Informasi Manajemen 
        Kepegawaian<br> TI – SIMPATI
    </h1>

    <p class="hero-desc">
        Platform modern yang mempermudah dosen dan mahasiswa dalam
        mengelola penelitian, pengabdian masyarakat, secara terintegrasi, cepat, dan efisien.
    </p>

    <ul class="feature-list">
        <li>Kelola data penelitian dosen</li>
        <li>Pengajuan kegiatan pengabdian</li>
        <li>Pelaporan otomatis & terstruktur</li>
        <li>Dashboard ringkas & informatif</li>
        <li>Login aman dengan akun Google</li>
    </ul>
</div>

<div class="section">
    <h2 class="section-title">Mengapa SIMPATI?</h2>
    <p class="section-desc">
        SIMPATI dikembangkan untuk mendukung kegiatan akademik perguruan tinggi
        dengan menyediakan proses digital yang lebih teratur, mengurangi
        kesalahan input data, serta meningkatkan efisiensi administrasi.
        Dengan antarmuka yang intuitif, SIMPATI memastikan pengalaman pengguna yang optimal
        dan inovatif di era digital ini.
    </p>
</div>

<div class="section">
    <h2 class="section-title">Fitur Utama</h2>
    <p class="section-desc">
        - Dashboard informatif dan mudah dipahami<br>
        - Manajemen arsip penelitian & pengabdian<br>
        - Pelacakan status pengajuan otomatis<br>
    </p>
</div>

@endsection
