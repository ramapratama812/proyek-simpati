@php
  $role = strtolower(auth()->user()->role ?? '');
@endphp

@if($role === 'admin')
<li class="nav-item">
    <a href="{{ route('projects.validation.index') }}" class="nav-link">
        <i class="bi bi-check2-square"></i>
        <span>Validasi Kegiatan</span>
    </a>
</li>
@endif


<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
     href="{{ route('dashboard') }}">
    <i class="bi bi-speedometer2 me-2"></i>Dasbor Utama
  </a>
</li>

<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}"
     href="{{ route('projects.index') }}">
    <i class="bi bi-folder2-open me-2"></i>Kegiatan
  </a>
</li>

@if($role === 'dosen')
  <li class="nav-item ms-3">
    <a class="nav-link {{ request()->routeIs('projects.my') ? 'active' : '' }}"
       href="{{ route('projects.my') }}">
      <i class="bi bi-caret-right-fill me-1 small"></i>Kelola Kegiatan Saya
    </a>
  </li>
@endif

<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('publications.index') ? 'active' : '' }}"
     href="{{ route('publications.index') }}">
    <i class="bi bi-journal-text me-2"></i>Publikasi
  </a>
</li>

@if($role === 'dosen')
  <li class="nav-item ms-3">
    <a class="nav-link {{ request()->routeIs('publications.my') ? 'active' : '' }}"
       href="{{ route('publications.my') }}">
      <i class="bi bi-caret-right-fill me-1 small"></i>Kelola Publikasi Saya
    </a>
  </li>
@endif
