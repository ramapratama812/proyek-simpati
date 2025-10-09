@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Detail Dosen</h2>
    <p><strong>Nama:</strong> {{ $dosen->nama }}</p>
    <p><strong>NIDN:</strong> {{ $dosen->nidn }}</p>
    <p><strong>Program Studi:</strong> {{ $dosen->program_studi }}</p>

    <hr>

    {{-- Riwayat Pendidikan --}}
    <h4>Riwayat Pendidikan</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Perguruan Tinggi</th>
                <th>Gelar Akademik</th>
                <th>Tahun</th>
                <th>Jenjang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dosen->pendidikans ?? [] as $p)
                <tr>
                    <td>{{ $p->perguruan_tinggi }}</td>
                    <td>{{ $p->gelar_akademik }}</td>
                    <td>{{ $p->tahun }}</td>
                    <td>{{ $p->jenjang }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada data pendidikan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <hr>

    {{-- Penelitian --}}
    <h4>Penelitian</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dosen->penelitian ?? [] as $pen)
                <tr>
                    <td>{{ $pen->judul }}</td>
                    <td>{{ $pen->tahun }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Belum ada data penelitian</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <hr>

    {{-- Pengabdian --}}
    <h4>Pengabdian Masyarakat</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dosen->pengabdian ?? [] as $abdi)
                <tr>
                    <td>{{ $abdi->judul }}</td>
                    <td>{{ $abdi->tahun }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Belum ada data pengabdian</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <hr>

    {{-- Publikasi --}}
    <h4>Publikasi</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Jurnal</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dosen->publikasi ?? [] as $pub)
                <tr>
                    <td>{{ $pub->judul }}</td>
                    <td>{{ $pub->jurnal }}</td>
                    <td>{{ $pub->tahun }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada data publikasi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
