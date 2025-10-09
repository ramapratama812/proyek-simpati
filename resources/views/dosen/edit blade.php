@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Dosen</h3>
    <form action="{{ route('dosen.update', $dosen->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Foto</label><br>
            @if ($dosen->foto)
                <img src="{{ asset('storage/'.$dosen->foto) }}" width="70" class="mb-2"><br>
            @endif
            <input type="file" name="foto" class="form-control">
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" value="{{ $dosen->nama }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>NIDN</label>
            <input type="text" name="nidn" value="{{ $dosen->nidn }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Perguruan Tinggi</label>
            <input type="text" name="perguruan_tinggi" value="{{ $dosen->perguruan_tinggi }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Status Ikatan Kerja</label>
            <input type="text" name="status_ikatan_kerja" value="{{ $dosen->status_ikatan_kerja }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control">
                <option value="">-- Pilih --</option>
                <option value="Laki-laki" {{ $dosen->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ $dosen->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Program Studi</label>
            <input type="text" name="program_studi" value="{{ $dosen->program_studi }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Pendidikan Terakhir</label>
            <input type="text" name="pendidikan_terakhir" value="{{ $dosen->pendidikan_terakhir }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Status Aktivitas</label>
            <input type="text" name="status_aktivitas" value="{{ $dosen->status_aktivitas }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-warning">Update</button>
        <a href="{{ route('dosen.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
