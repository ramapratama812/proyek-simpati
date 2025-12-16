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
        * {
            box-sizing: border-box;
            /* Menambahkan transisi global yang halus pada properti tertentu */
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .wrapper {
            display: flex;
            height: 100vh;
            /* Changed from min-height to height */
            overflow: hidden;
            /* Supaya gak ikut ke-scroll */
        }

        /* WARNA UTAMA SESUAI TEMA */
        :root {
            --blue-dark: #001F4D;
            --blue-medium: #0050a0;
            --blue-light: #e8f0ff;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 270px;
            background-color: var(--blue-dark);
            padding: 35px 24px;
            color: #fff;
            display: flex;
            flex-direction: column;
            border-top-right-radius: 14px;
            border-bottom-right-radius: 14px;
            box-shadow: 4px 0 16px rgba(0, 0, 0, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            flex-shrink: 0;
            overflow-y: auto;
            /* Allow sidebar scroll */
            overflow-x: hidden;
        }

        /* Hide scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar.collapsed {
            width: 0;
            padding-left: 0;
            padding-right: 0;
            opacity: 0;
            visibility: hidden;
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

        /* Section Dividers */
        .sidebar-divider {
            display: flex;
            align-items: center;
            margin: 15px 0;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-divider hr {
            flex-grow: 1;
            border-color: rgba(255, 255, 255, 0.2);
            margin: 0 10px;
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
            /* Hapus transisi di sini karena sudah ada di selector * */
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
            width: 100%;
            height: 100%;
            /* Full height */
            overflow: hidden;
            /* Contain children */
            transition: margin-left 0.3s ease;
        }

        nav.navbar {
            border-bottom: 1px solid #e5e5e5;
            background-color: #ffffff;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            z-index: 999;
        }

        /* Styling untuk Avatar di Navbar */
        .profile-avatar-container {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 8px;
            border-radius: 50px;
            /* Hapus transisi di sini */
        }

        /* Efek Hover/Klik */
        .profile-avatar-container:hover,
        .profile-avatar-container[aria-expanded="true"] {
            background-color: #f0f4f8;
            box-shadow: 0 0 5px rgba(0, 80, 160, 0.2);
        }

        /* UKURAN AVATAR */
        .navbar-avatar {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .navbar-initial {
            width: 32px;
            height: 32px;
            font-size: 1rem;
            background-color: var(--blue-light);
            color: var(--blue-medium);
            border: 1px solid #cce0ff;
        }

        /* Style untuk nama di Navbar */
        .navbar-user-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--blue-dark);
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #333;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            margin-right: 15px;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }

        .toggle-btn:hover {
            background-color: #f0f0f0;
            color: #001F4D;
        }

        /* Hilangkan panah dropdown jika hanya ikon */
        .nav-link.no-caret::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }

        main {
            flex: 1;
            padding: 25px 35px;
            overflow-y: auto;
            /* Terapkan animasi saat halaman dimuat */
            animation: fadeIn 0.4s ease-out;
            /* Ditingkatkan durasinya */
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            /* Jarak start lebih besar */
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .required-asterisk {
            color: red;
        }

        /* Back to Top Button */
        #backToTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #001F4D;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1050;
            border: none;
        }

        #backToTop:hover {
            background-color: #00c2ff;
            transform: translateY(-3px);
        }

        #backToTop.show {
            opacity: 1;
            visibility: visible;
        }

        /* ===== Responsif ===== */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                height: 100vh;
                width: 270px;
                border-radius: 0;
                transition: left 0.3s ease;
            }

            .sidebar.mobile-show {
                left: 0;
            }

            .sidebar.collapsed {
                width: 270px;
                /* Reset collapsed width for mobile logic */
                opacity: 1;
                visibility: visible;
            }

            /* Overlay for mobile sidebar */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 998;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="wrapper">
        {{-- Sidebar tidak muncul di halaman login & register --}}
        @if (!in_array(Route::currentRouteName(), ['login', 'register']))
            <div class="sidebar">
                <div class="logo">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>SIMPATI</span>
                </div>

                @auth
                    @php
                        $user = Auth::user();
                        $role = strtolower($user->role ?? '');

                        $user_photo_path = null;

                        // LOGIC AMBIL FOTO DARI MODEL TERKAIT
                        if ($role === 'dosen' && method_exists($user, 'lecturerProfile')) {
                            $modelData = $user->lecturerProfile;
                            if ($modelData && isset($modelData->foto) && $modelData->foto) {
                                $user_photo_path = $modelData->foto;
                            }
                        } elseif ($role === 'mahasiswa' && method_exists($user, 'studentProfile')) {
                            $modelData = $user->studentProfile;
                            if ($modelData && isset($modelData->foto) && $modelData->foto) {
                                $user_photo_path = $modelData->foto;
                            }
                        }

                        // Fallback ke kolom 'foto' tabel users
                        if (!$user_photo_path && isset($user->foto) && $user->foto) {
                            $user_photo_path = $user->foto;
                        }

                        $initial = strtoupper(substr($user->name, 0, 1));
                    @endphp

                    <div class="sidebar-divider">
                        <hr><span>Menu Awal</span>
                        <hr>
                    </div>

                    <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill"></i> Dasbor Utama
                    </a>

                    <div class="sidebar-divider">
                        <hr><span>Menu Unggahan</span>
                        <hr>
                    </div>

                    <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') ? 'active' : '' }}">
                        <i class="bi bi-list-task"></i> Kegiatan
                    </a>

                    <a href="{{ route('publications.index') }}"
                        class="{{ request()->is('publications*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Publikasi
                    </a>

                    <div class="sidebar-divider">
                        <hr><span>Daftar Personel</span>
                        <hr>
                    </div>

                    <a href="{{ route('dosen.index') }}" class="{{ request()->is('dosen*') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill"></i> Dosen
                    </a>

                    <a href="{{ route('mahasiswa.index') }}" class="{{ request()->is('mahasiswa*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Mahasiswa
                    </a>

                    <div class="sidebar-divider">
                        <hr><span>Lainnya</span>
                        <hr>
                    </div>

                    <a href="{{ route('tpk.dosen_berprestasi.index') }}"
                        class="{{ request()->is('tpk/dosen_berprestasi*') ? 'active' : '' }}">
                        <i class="bi bi-trophy-fill"></i> Ranking Dosen Berprestasi
                    </a>

                    @if ($role === 'admin')
                        <div class="sidebar-divider">
                            <hr><span>Zona Admin</span>
                            <hr>
                        </div>
                    @endif

                    {{-- Adimn: kelola tpk ahp --}}
                    @if ($role === 'admin')
                        <a href="{{ route('ahp.criteria_comparisons.edit') }}"
                            class="{{ request()->routeIs('ahp.criteria_comparisons.*') ? 'active' : '' }}">
                            <i class="bi bi-calculator"></i>
                            <span>Kelola Hitung TPK AHP</span>
                        </a>
                    @endif

                    {{-- Admin: Kelola Permohonan Akun --}}
                    @if ($role === 'admin')
                        <a href="{{ route('admin.registration-requests.index') }}"
                            class="{{ request()->routeIs('admin.registration-requests.*') ? 'active' : '' }}">
                            <i class="bi bi-person-plus-fill"></i>
                            <span>Kelola Permohonan Akun</span>
                        </a>
                    @endif

                    {{-- Admin: Validasi Kegiatan --}}
                    @if ($role === 'admin')
                        <a href="{{ route('projects.validation.index') }}"
                            class="{{ request()->routeIs('projects.validation.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check"></i>
                            <span>Validasi Kegiatan</span>
                        </a>
                    @endif

                    {{-- Admin: Validasi Publikasi --}}
                    @if ($role === 'admin')
                        <a href="{{ route('admin.publications.validation.index') }}"
                            class="{{ request()->routeIs('validation.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-check"></i>
                            <span>Validasi Publikasi</span>
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        {{-- Konten utama --}}
        <div class="content">
            {{-- Navbar atas (kecuali login & register) --}}
            @if (!in_array(Route::currentRouteName(), ['login', 'register']))
                <nav class="navbar navbar-expand-lg sticky-top">
                    <div class="container-fluid">
                        <button class="toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
                            <i class="bi bi-list"></i>
                        </button>

                        <div class="d-flex justify-content-end w-100">
                            @auth
                                @php
                                    // Pastikan selalu jadi Collection supaya gampang diolah
                                    $notifItems = $notifications ?? collect();

                                    if (is_array($notifItems)) {
                                        $notifItems = collect($notifItems);
                                    }

                                    // Hitung jumlah notif yang BELUM dibaca (read_at = null)
                                    $unreadNotificationCount =
                                        $unreadNotificationCount ??
                                        (method_exists($notifItems, 'where')
                                            ? $notifItems->where('read_at', null)->count()
                                            : 0);

                                    // --- LOGIC FOTO PROFIL UNTUK NAVBAR ---
                                    $user = Auth::user();
                                    $role = strtolower($user->role ?? '');
                                    $user_photo_path = null;
                                    $initial = strtoupper(substr($user->name, 0, 1));

                                    if ($role === 'dosen' && method_exists($user, 'lecturerProfile')) {
                                        $modelData = $user->lecturerProfile;
                                        if ($modelData && isset($modelData->foto) && $modelData->foto) {
                                            $user_photo_path = $modelData->foto;
                                        }
                                    } elseif ($role === 'mahasiswa' && method_exists($user, 'studentProfile')) {
                                        $modelData = $user->studentProfile;
                                        if ($modelData && isset($modelData->foto) && $modelData->foto) {
                                            $user_photo_path = $modelData->foto;
                                        }
                                    }

                                    if (!$user_photo_path && isset($user->foto) && $user->foto) {
                                        $user_photo_path = $user->foto;
                                    }
                                @endphp

                                <ul class="navbar-nav align-items-center">
                                    {{-- Lonceng notifikasi --}}
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link position-relative" href="#" id="notifDropdown"
                                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-bell fs-5"></i>
                                            @if ($unreadNotificationCount > 0)
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $unreadNotificationCount }}
                                                </span>
                                            @endif
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0"
                                            aria-labelledby="notifDropdown"
                                            style="min-width: 360px; max-height: 420px; overflow-y: auto;">
                                            <div
                                                class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                                <strong>Notifikasi Terbaru</strong>
                                                @if ($unreadNotificationCount > 0)
                                                    <span class="badge bg-danger small">
                                                        {{ $unreadNotificationCount }} belum dibaca
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($notifItems->count())
                                                @foreach ($notifItems as $item)
                                                    @php
                                                        // Item bisa berupa array atau model UserNotification
                                                        $message = $item['message'] ?? ($item->message ?? '-');
                                                        $created =
                                                            $item['time'] ??
                                                            (isset($item->created_at)
                                                                ? $item->created_at->format('d M Y H:i')
                                                                : '');
                                                        $isRead = !empty($item['read_at'] ?? ($item->read_at ?? null));
                                                    @endphp

                                                    <div class="dropdown-item small {{ $isRead ? 'bg-light' : '' }}">
                                                        <div class="fw-semibold mb-1" style="white-space: normal;">
                                                            {{ $message }}
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                                {{ $created }}
                                                            </div>

                                                            {{-- Tombol tandai dibaca / belum dibaca hanya kalau ini instance model --}}
                                                            @if ($item instanceof \App\Models\UserNotification)
                                                                <div class="ms-2">
                                                                    @if (!$isRead)
                                                                        <form
                                                                            action="{{ route('notifications.mark_read', $item) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-link btn-sm p-0 text-decoration-none text-primary"
                                                                                data-bs-toggle="tooltip"
                                                                                title="Tandai telah dibaca">
                                                                                <i class="bi bi-envelope-check fs-5"></i>
                                                                            </button>
                                                                        </form>
                                                                    @else
                                                                        <form
                                                                            action="{{ route('notifications.mark_unread', $item) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-link btn-sm p-0 text-decoration-none text-muted"
                                                                                data-bs-toggle="tooltip"
                                                                                title="Tandai belum dibaca">
                                                                                <i class="bi bi-envelope fs-5"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            @endif
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

                                    {{-- Dropdown profil DENGAN NAMA --}}
                                    <li class="nav-item dropdown">
                                        <a class="nav-link profile-avatar-container" href="#" id="profileDropdown"
                                            role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            title="{{ $user->name }}">

                                            {{-- LOGIKA FOTO/INISIAL NAVBAR (MENGGUNAKAN $user_photo_path) --}}
                                            @if ($user_photo_path)
                                                <img src="{{ asset('storage/' . $user_photo_path) }}"
                                                    alt="{{ $user->name }}" class="rounded-circle navbar-avatar">
                                            @else
                                                <div
                                                    class="rounded-circle d-flex align-items-center justify-content-center fw-bold navbar-initial">
                                                    {{ $initial }}
                                                </div>
                                            @endif

                                            {{-- NAMA PENGGUNA DI SAMPING AVATAR --}}
                                            <span class="navbar-user-name d-none d-md-inline">{{ $user->name }}</span>

                                        </a>

                                        {{-- Dropdown Menu --}}
                                        <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm border-0"
                                            aria-labelledby="profileDropdown">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('profile.show') }}">
                                                    <i class="bi bi-person-circle me-2"></i> Lihat Profil
                                                    ({{ $user->name }})
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
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
                    </div>
                </nav>
            @endif

            <main>
                {{-- Flash message global (tidak diubah) --}}
                @if (session('status'))
                    <div class="container mt-3">
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            {!! session('status') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="container mt-3">
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            {!! session('error') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if (session('ok'))
                    <div class="container mt-3">
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            {!! session('ok') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if (session('popup_error'))
                    <div class="container mt-3">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('popup_error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
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

    {{-- Back to Top Button --}}
    <button id="backToTop" title="Kembali ke atas">
        <i class="bi bi-arrow-up"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const backToTopBtn = document.getElementById('backToTop');
            const wrapper = document.querySelector('.wrapper');

            // Initialize Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // --- Sidebar Toggle Logic ---

            // Check localStorage for sidebar state
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed && window.innerWidth > 992) {
                sidebar.classList.add('collapsed');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (window.innerWidth > 992) {
                        // Desktop toggle
                        sidebar.classList.toggle('collapsed');
                        // Save state
                        localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                    } else {
                        // Mobile toggle
                        sidebar.classList.toggle('mobile-show');
                        sidebarOverlay.classList.toggle('show');
                    }
                });
            }

            // Close sidebar when clicking overlay (mobile)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            // --- Back to Top Logic ---

            const mainContent = document.querySelector('main'); // Scroll listener on main, not window

            mainContent.addEventListener('scroll', function() {
                if (mainContent.scrollTop > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });

            backToTopBtn.addEventListener('click', function() {
                mainContent.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>
