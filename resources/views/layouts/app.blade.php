{{-- Layout baru pakai navigasi sidebar plus styling CSS --}}

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIMPATI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    .wrapper {
      display: flex;
      height: 100vh;
    }

    /* ===== Sidebar ===== */
    .sidebar {
      width: 270px;
      background-color: #001F4D; /* 🔹 Biru tua solid */
      padding: 35px 24px;
      color: #fff;
      display: flex;
      flex-direction: column;
      border-top-right-radius: 14px;
      border-bottom-right-radius: 14px;
      box-shadow: 4px 0 16px rgba(0, 0, 0, 0.25);
    }

    .sidebar .logo {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      margin-bottom: 50px;
      padding-left: 8px;
    }

    .sidebar .logo i {
      font-size: 2rem;
      margin-right: 10px;
      color: #ffffff;
    }

    .sidebar .logo span {
      font-size: 1.6rem;
      font-weight: 700;
      letter-spacing: 0.7px;
      color: #ffffff;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 18px;
      color: #ffffff;
      text-decoration: none;
      border-radius: 12px;
      margin-bottom: 12px;
      font-weight: 500;
      font-size: 1rem;
      position: relative;
      transition: all 0.25s ease;
    }

    /* Neon bar kiri menu aktif */
    .sidebar a.active::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 4px;
      background-color: #00c2ff;
      border-top-right-radius: 2px;
      border-bottom-right-radius: 2px;
      box-shadow: 0 0 8px #00c2ff;
    }

    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateX(6px);
    }

    .sidebar a.active {
      background: rgba(255, 255, 255, 0.25);
      font-weight: 600;
    }

    /* ===== Konten ===== */
    .content {
      flex: 1;
      display: flex;
      flex-direction: column;
      background-color: #f8f9fa;
    }

    nav.navbar {
      border-bottom: 1px solid #e5e5e5;
      background-color: #ffffff;
      padding: 0.75rem 1.5rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    main {
      flex: 1;
      padding: 25px 35px;
      overflow-y: auto;
      animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ===== Responsif ===== */
    @media (max-width: 992px) {
      .sidebar {
        width: 100%;
        height: auto;
        flex-direction: row;
        justify-content: space-around;
        border-radius: 0;
        box-shadow: none;
      }

      .sidebar .logo {
        display: none;
      }

      .sidebar a {
        flex-direction: column;
        font-size: 0.85rem;
        margin-bottom: 0;
        padding: 10px;
      }
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    @if (!in_array(Route::currentRouteName(), ['login', 'register']))
      <div class="sidebar">
        <div class="logo">
          <i class="bi bi-mortarboard-fill"></i>
          <span>SIMPATI</span>
        </div>

        @auth
          <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i> Dasbor Utama
          </a>
          <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') ? 'active' : '' }}">
            <i class="bi bi-list-task"></i> Kegiatan
          </a>
          <a href="{{ route('publications.index') }}" class="{{ request()->is('publications*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Publikasi
          </a>
          <a href="{{ route('dosen.index') }}" class="{{ request()->is('dosen*') ? 'active' : '' }}">
            <i class="bi bi-person-lines-fill"></i> Dosen
          </a>
          <a href="{{ route('mahasiswa.index') }}" class="{{ request()->is('mahasiswa*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Mahasiswa
          </a>
        @endauth
      </div>
    @endif

    <!-- Konten Utama -->
    <div class="content">
      @if (!in_array(Route::currentRouteName(), ['login', 'register']))
        <nav class="navbar navbar-expand-lg sticky-top">
          <div class="container-fluid d-flex justify-content-end">
            @auth
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle fw-semibold text-dark" href="#" id="navbarDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ Auth::user()->name }}
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm border-0" aria-labelledby="navbarDropdown">
                    <li>
                      <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.show') }}">
                        <i class="bi bi-person-circle me-2"></i> Lihat Profil
                      </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                          <i class="bi bi-box-arrow-right me-2"></i> Keluar
                        </button>
                      </form>
                    </li>
                  </ul>
                </li>
              </ul>
            @endauth
          </div>
        </nav>
      @endif

      <main>
        @yield('content')
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
