<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIMPATI</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">

  <style>
    * { box-sizing: border-box; }

    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    .wrapper {
      display: flex;
      min-height: 100vh;
    }

    /* ===== Sidebar ===== */
    .sidebar {
      width: 270px;
      background-color: #001F4D;
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
      to   { opacity: 1; transform: translateY(0); }
    }

    .required-asterisk { color: red; }

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

      .sidebar .logo { display: none; }

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
    {{-- Sidebar tidak muncul di halaman login & register --}}
    @if (!in_array(Route::currentRouteName(), ['login', 'register']))
      <div class="sidebar">
        <div class="logo">
          <i class="bi bi-mortarboard-fill"></i>
          <span>SIMPATI</span>
        </div>

        @auth
          @php $role = strtolower(auth()->user()->role ?? ''); @endphp

          {{-- Admin: Validasi Kegiatan --}}
          @if($role === 'admin')
            <a href="{{ route('projects.validation.index') }}"
               class="{{ request()->routeIs('projects.validation.*') ? 'active' : '' }}">
              <i class="bi bi-check2-square"></i>
              <span>Validasi Kegiatan</span>
            </a>
          @endif

          {{-- Admin: Kelola Permohonan Akun --}}
          @if($role === 'admin')
            <a href="{{ route('admin.registration-requests.index') }}"
               class="{{ request()->routeIs('admin.registration-requests.*') ? 'active' : '' }}">
              <i class="bi bi-person-check"></i>
              <span>Kelola Permohonan Akun</span>
            </a>
          @endif

          <a href="{{ route('dashboard') }}"
             class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i> Dasbor Utama
          </a>

          <a href="{{ route('projects.index') }}"
             class="{{ request()->is('projects*') ? 'active' : '' }}">
            <i class="bi bi-list-task"></i> Kegiatan
          </a>

          <a href="{{ route('publications.index') }}"
             class="{{ request()->is('publications*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Publikasi
          </a>

          <a href="{{ route('dosen.index') }}"
             class="{{ request()->is('dosen*') ? 'active' : '' }}">
            <i class="bi bi-person-lines-fill"></i> Dosen
          </a>

          <a href="{{ route('mahasiswa.index') }}"
             class="{{ request()->is('mahasiswa*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Mahasiswa
          </a>
        @endauth
      </div>
    @endif

    {{-- Konten utama --}}
    <div class="content">
      {{-- Navbar atas (kecuali login & register) --}}
      @if (!in_array(Route::currentRouteName(), ['login', 'register']))
        <nav class="navbar navbar-expand-lg sticky-top">
          <div class="container-fluid d-flex justify-content-end">
            @auth
              @php
                  // Kalau controller tidak mengirim $notifications, pakai array kosong
                  $notifItems = $notifications ?? [];
              @endphp

              <ul class="navbar-nav align-items-center">
                {{-- Lonceng notifikasi --}}
                <li class="nav-item dropdown me-3">
                  <a class="nav-link position-relative"
                     href="#"
                     id="notifDropdown"
                     role="button"
                     data-bs-toggle="dropdown"
                     aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    @if(count($notifItems))
                      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ count($notifItems) }}
                      </span>
                    @endif
                  </a>

                  <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0"
                       aria-labelledby="notifDropdown"
                       style="min-width: 360px; max-height: 420px; overflow-y: auto;">
                    <div class="px-3 py-2 border-bottom">
                      <strong>Notifikasi Terbaru</strong>
                    </div>

                    @if(count($notifItems))
                      @foreach($notifItems as $item)
                        <div class="dropdown-item small">
                          <div class="fw-semibold mb-1" style="white-space: normal;">
                            {{ $item['message'] ?? $item->message ?? '-' }}
                          </div>
                          <div class="text-muted" style="font-size: 0.75rem;">
                            {{ $item['time'] ?? (($item->created_at ?? null) ? $item->created_at->format('d M Y H:i') : '') }}
                          </div>
                        </div>
                        <div class="dropdown-divider m-0"></div>
                      @endforeach
                    @else
                      <div class="px-3 py-3 small text-muted">
                        Tidak ada notifikasi.
                      </div>
                    @endif
                  </div>
                </li>

                {{-- Dropdown profil --}}
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle fw-semibold text-dark"
                     href="#"
                     id="profileDropdown"
                     role="button"
                     data-bs-toggle="dropdown"
                     aria-expanded="false">
                    {{ Auth::user()->name }}
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm border-0"
                      aria-labelledby="profileDropdown">
                    <li>
                      <a class="dropdown-item d-flex align-items-center"
                         href="{{ route('profile.show') }}">
                        <i class="bi bi-person-circle me-2"></i> Lihat Profil
                      </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit"
                                class="dropdown-item d-flex align-items-center text-danger">
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
        {{-- Flash message global (misal untuk status setelah redirect) --}}
        @if (session('status'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    {!! session('status') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @yield('content')
      </main>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

      @stack('scripts')
      @yield('scripts')
    </div>
  </div>
</body>
</html>
