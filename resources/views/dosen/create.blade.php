@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Dosen</h3>
    <form action="{{ route('dosen.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Foto</label>
            <input type="file" name="foto" class="form-control">
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>NIDN</label>
            <input type="text" name="nidn" class="form-control">
        </div>
        <div class="mb-3">
            <label>Perguruan Tinggi</label>
            <input type="text" name="perguruan_tinggi" class="form-control">
        </div>
        <div class="mb-3">
            <label>Status Ikatan Kerja</label>
            <input type="text" name="status_ikatan_kerja" class="form-control">
        
        </div>
        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control">
                <option value="">-- Pilih --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Program Studi</label>
            <input type="text" name="program_studi" class="form-control">
        </div>
        <div class="mb-3">
            <label>Pendidikan Terakhir</label>
            <input type="text" name="pendidikan_terakhir" class="form-control">
        
        </div>
        <div class="mb-3">
            <label>Status Aktivitas</label>
            <input type="text" name="status_aktivitas" class="form-control">
           
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('dosen.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
