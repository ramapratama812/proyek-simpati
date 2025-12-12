@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Header judul --}}
        <div class="mb-3">
            <h4 class="mb-3">{{ $pub->judul }}</h4>
        </div>

        {{-- Kontainer Flexbox untuk Tombol Edit/Hapus (Kiri) dan DOI (Kanan) --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">

            {{-- Kiri: Hak edit/hapus hanya untuk owner --}}
            @php
                $user = auth()->user();
                $isAdmin = strtolower($user->role ?? '') === 'admin';
                $canManage = \Illuminate\Support\Facades\Schema::hasColumn('publications', 'owner_id')
                    ? $isAdmin || $pub->owner_id === ($user->id ?? null)
                    : $isAdmin;
            @endphp

            {{-- Hapus class d-flex dari container utama agar Alert bisa turun ke bawah --}}
            <div>
                @if ($canManage)
                    {{-- Bungkus tombol-tombol dalam container flex tersendiri --}}
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('publications.edit', $pub) }}" class="btn btn-warning">Edit</a>

                        <form method="POST" action="{{ route('publications.destroy', $pub) }}"
                            onsubmit="return confirm('Hapus publikasi ini? Tindakan tidak bisa dibatalkan.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>

                        {{-- Tombol Ajukan Validasi --}}
                        @if ($pub->validation_status === 'draft' || $pub->validation_status === 'revision_requested')
                            <form method="POST" action="{{ route('publications.submit', $pub->id) }}">
                                @csrf
                                <button class="btn btn-primary">Ajukan Validasi ke Admin</button>
                            </form>
                        @endif
                    </div>

                    {{-- Alert diletakkan di luar div tombol agar muncul rapi di bawahnya --}}
                    @if ($pub->validation_status === 'approved')
                        <div class="alert alert-success mt-2">
                            Publikasi ini sudah divalidasi dan tidak dapat diedit lagi.
                        </div>
                    @endif
                @endif
            </div>

            {{-- Kanan: DOI (Diposisikan di pojok kanan, sejajar dengan tombol) --}}
            <div class="text-end">
                @if ($pub->doi)
                    <a href="https://doi.org/{{ $pub->doi }}" target="_blank" class="text-decoration-none">
                        <strong style="color: #007bff;">DOI: {{ $pub->doi }}</strong>
                    </a>
                @else
                    <span class="d-block text-muted" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-1"></i>
                        DOI: —</span>
                @endif

                {{-- ===== FILE PDF ARTIKEL ===== --}}
                <div class="mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <strong>File Artikel (PDF):</strong>
                            @if (!empty($pub->file))
                                <span class="text-success fw-semibold">Tersedia</span>
                            @else
                                <span class="text-muted">Belum ada</span>
                            @endif
                        </div>

                        @if (!empty($pub->file))
                            <div class="d-flex gap-2">
                                <a href="{{ asset('storage/' . $pub->file) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat PDF
                                </a>

                                <a href="{{ asset('storage/' . $pub->file) }}" download
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </div>
                        @endif
                    </div>

                    @if (!empty($pub->file))
                        {{-- Optional: preview embed (hapus kalau nggak mau) --}}
                        <div class="mt-3">
                            <div class="ratio ratio-16x9 border rounded-3 overflow-hidden">
                                <iframe src="{{ asset('storage/' . $pub->file) }}" title="PDF Preview"></iframe>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Rincian publikasi (Card) --}}
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <h5>
                        @php
                            $info = [];
                            if ($pub->volume) {
                                $info[] = "Vol {$pub->volume}";
                            }
                            if ($pub->nomor) {
                                $info[] = "No {$pub->nomor}";
                            }
                            if ($pub->tahun) {
                                $info[] = "({$pub->tahun})";
                            }
                            $display = implode(' ', $info);
                        @endphp

                        <strong>{{ $pub->jurnal ?? '—' }}:
                            @if ($display)
                                <span class="text-muted">{{ $display }}</span>
                            @endif
                        </strong>

                    </h5>
                </div>

                {{-- Bagian informasi lainnya --}}
                <div class="mb-2"><strong>Jenis:</strong> {{ $pub->jenis ?? '—' }}</div>
                <div class="mb-2"><strong>Jumlah Halaman:</strong> {{ $pub->jumlah_halaman ?? '—' }}</div>

                @if (isset($pub->penulis) && is_array($pub->penulis))
                    <div class="mb-2"><strong>Penulis:</strong> {{ implode(', ', $pub->penulis) }}</div>
                @else
                    <div class="mb-2"><strong>Penulis:</strong> —</div>
                @endif
                <div class="mb-2"><strong>Pengunggah:</strong> {{ $pub->owner->name ?? '—' }}</div>

                @if ($pub->abstrak)
                    <div class="mb-2"><strong>Abstrak:</strong>
                        <p>{{ $pub->abstrak }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('publications.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>
@endsection
