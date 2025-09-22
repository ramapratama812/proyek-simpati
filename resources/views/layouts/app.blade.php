<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPATI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">SIMPATI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            @auth
              <li class="nav-item"><a class="nav-link" href="{{ route('projects.index') }}">Kegiatan</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ route('publications.index') }}">Publikasi</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ route('dosen.index') }}">Dosen</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ route('mahasiswa.index') }}">Mahasiswa</a></li>
            @endauth
          </ul>
          @auth
          <form method="POST" action="{{ route('logout') }}" class="d-flex">
            @csrf
            <button class="btn btn-outline-danger">Keluar</button>
          </form>
          @endauth
        </div>
      </div>
    </nav>
    <main class="py-4">
      @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
