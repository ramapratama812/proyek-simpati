@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="mb-3">Tambah Publikasi</h4>

        <form method="POST" action="{{ route('publications.store') }}" class="card p-4 shadow-sm"
            enctype="multipart/form-data">
            @csrf

            {{-- simpan project_id jika datang dari halaman publikasi kegiatan --}}
            {{-- <input type="hidden" name="project_id" value="{{ $projectId ?? '' }}"> --}}

            <input type="hidden" name="project_id" value="{{ old('project_id', $projectId) }}">


            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Judul</label>
                    <input name="judul" class="form-control" value="{{ old('judul') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis</label>
                    <input name="jenis" class="form-control" placeholder="article, proceeding, dst"
                        value="{{ old('jenis') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jurnal/Prosiding</label>
                    <input name="jurnal" class="form-control" value="{{ old('jurnal') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <input name="tahun" type="number" min="1980" class="form-control" value="{{ old('tahun') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">DOI</label>
                    <input name="doi" id="doiField" class="form-control" value="{{ old('doi') }}">
                </div>

                {{-- opsi tambah pdf artikel --}}
                <div class="mb-3">
                    <label for="file" class="form-label fw-semibold">File Artikel (PDF)</label>
                    <input type="file" name="file" id="file" accept="application/pdf"
                        class="form-control @error('file') is-invalid @enderror">
                    <div class="form-text">Opsional. Format: PDF. Maks 10MB.</div>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Volume</label>
                    <input type="number" name="volume" class="form-control" min="1" value="{{ old('volume') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor (issue)</label>
                    <input type="number" name="nomor" class="form-control" min="1" value="{{ old('nomor') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Halaman</label>
                    <input name="jumlah_halaman" type="number" class="form-control" value="{{ old('jumlah_halaman') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Penulis</label>
                    <textarea name="penulis" class="form-control" rows="3"
                        placeholder="Masukkan nama penulis, pisahkan dengan koma atau baris baru untuk menambahkan lebih dari satu">{{ old('penulis') }}</textarea>
                    <div class="form-text">Contoh: John Doe, Jane Smith</div>
                </div>

                <div class="col-12">
                    <label class="form-label">Abstrak</label>
                    <textarea name="abstrak" class="form-control" rows="4">{{ old('abstrak') }}</textarea>
                </div>
            </div>

            {{-- ====== Opsi Impor DOI (Crossref) ====== --}}
            <div class="mt-4">
                <div class="input-group">
                    <input id="doiImportInput" class="form-control" placeholder="Masukkan DOI untuk impor dari Crossref">
                    <button type="button" id="btnImportDoi" class="btn btn-outline-primary">Impor DOI</button>
                </div>
                <div class="form-text">Contoh: 10.1038/nphys1170 atau https://doi.org/10.1038/nphys1170</div>
            </div>

            <div class="mt-4 text-end">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>

        <div class="mt-3">
            <a href="{{ url()->previous() ?: url('/publications') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>

    </div>
@endsection

{{-- Buat integrasi crossref (impor DOI) --}}
@push('scripts')
    <script>
        (function() {
            function normDOI(s) {
                if (!s) return '';
                s = s.trim();
                return s.replace(/^https?:\/\/(dx\.)?doi\.org\//i, ''); // hilangkan prefix URL jika ada
            }

            function pickYear(msg) {
                const src = (msg['published-print']?.['date-parts']?.[0] ||
                    msg['published-online']?.['date-parts']?.[0] ||
                    msg['issued']?.['date-parts']?.[0]) || [];
                return src[0] || '';
            }

            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('btnImportDoi');
                if (!btn) return;
                btn.addEventListener('click', async function() {
                    const raw = document.getElementById('doiImportInput').value;
                    const doi = normDOI(raw);
                    if (!doi) {
                        alert('Isi DOI terlebih dahulu.');
                        return;
                    }

                    try {
                        const res = await fetch('https://api.crossref.org/works/' +
                            encodeURIComponent(doi));
                        if (!res.ok) throw new Error('Gagal menghubungi Crossref');
                        const json = await res.json();
                        const m = json.message || {};

                        // isi field
                        document.querySelector('[name=judul]').value = (m.title && m.title[0]) ||
                            '';
                        document.querySelector('[name=jurnal]').value =
                            (m['container-title'] && m['container-title'][0]) || (m.publisher ||
                                '');
                        document.querySelector('[name=tahun]').value = pickYear(m);
                        document.querySelector('[name=volume]').value = m.volume || '';
                        document.querySelector('[name=nomor]').value = m.issue || '';
                        document.querySelector('[name=abstrak]').value = m.abstract || '';
                        document.querySelector('[name=jumlah_halaman]').value = calculatePageCount(m
                            .page);
                        document.querySelector('[name=doi]').value = m.DOI || doi;
                        document.querySelector('[name=jenis]').value = (m.type || '').replace(/_/g,
                            ' ');

                        // Populate authors
                        if (m.author && Array.isArray(m.author)) {
                            const authors = m.author.map(a => `${a.given || ''} ${a.family || ''}`
                                .trim()).join(', ');
                            document.querySelector('[name=penulis]').value = authors;
                        }

                        function calculatePageCount(page) {
                            if (!page) return '';
                            const match = page.match(/(\d+)-(\d+)/);
                            if (match) {
                                return parseInt(match[2]) - parseInt(match[1]) + 1;
                            }
                            return '';
                        }

                        alert(
                            'Data berhasil diimpor dari Crossref. Silakan periksa kembali sebelum menyimpan.'
                            );
                    } catch (e) {
                        console.error(e);
                        alert('Impor DOI gagal. Periksa nilai DOI atau coba lagi.');
                    }
                });
            });
        })();
    </script>
@endpush
