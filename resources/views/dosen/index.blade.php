@extends('layouts.app')

@push('styles')
<style>
    body {
        background-color: #f4f6f9;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        background: #fff;
        padding: 25px;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    /* Header dengan logo */
    .page-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
    }

    .page-header img {
        height: 50px;
        width: auto;
    }

    .page-header h3 {
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
    }

    /* Search box */
    .input-group {
        max-width: 400px;
    }

    .form-control {
        border-radius: 10px 0 0 10px;
        border: 1px solid #ced4da;
    }

    .btn-primary {
        border-radius: 0 10px 10px 0;
        font-weight: 600;
        background: #0d6efd;
        border: none;
    }

    .btn-primary:hover {
        background: #0b5ed7;
    }

    .btn-success {
        border-radius: 8px;
        font-weight: 600;
        padding: 7px 16px;
        margin-bottom: 15px;
    }

    /* Table style */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 10px;
    }

    .table thead {
        background: #0d6efd;
        color: #fff;
        font-size: 14px;
    }

    .table thead th {
        padding: 12px;
        text-align: center;
    }

    .table tbody tr {
        background: #fff;
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .table td {
        padding: 12px;
        vertical-align: middle;
        font-size: 14px;
        color: #2c3e50;
    }

    /* Foto dosen */
    .table img {
        border-radius: 50%;
        border: 2px solid #e9ecef;
    }

    /* Tombol kecil */
    .btn-sm {
        border-radius: 6px;
        padding: 5px 12px;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .btn-info.btn-sm {
        background-color: #0dcaf0;
        border: none;
        color: #fff;
    }

    .btn-info.btn-sm:hover {
        background-color: #0bb0d6;
    }

    .btn-danger.btn-sm {
        background-color: #dc3545;
        border: none;
        color: #fff;
    }

    .btn-danger.btn-sm:hover {
        background-color: #bb2d3b;
    }

    /* Empty text */
    .text-muted {
        font-style: italic;
        color: #6c757d !important;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Header dengan logo -->
    <div class="page-header">
        <h3>📋 Daftar Dosen - SIMPATI</h3>
    </div>

    <!-- Search -->
    <form action="{{ route('dosen.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="cari" class="form-control" placeholder="🔍 Cari nama / NIDN..." value="{{ request('cari') }}">
            <button class="btn btn-primary">Cari</button>
        </div>
    </form>

    <!-- Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Perguruan Tinggi</th>
                <th>Status Ikatan Kerja</th>
                <th>Jenis Kelamin</th>
                <th>Program Studi</th>
                <th>Pendidikan Terakhir</th>
                <th>Status Aktivitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dosens as $dosen)
            <tr>
                <td class="text-center">
                    @if ($dosen->foto)
                        <img src="{{ asset('storage/'.$dosen->foto) }}" width="50">
                    @else
                        <span class="text-muted">No Image</span>
                    @endif
                </td>
                <td>{{ $dosen->nama }}</td>
                <td>{{ $dosen->perguruan_tinggi }}</td>
                <td>{{ $dosen->status_ikatan_kerja }}</td>
                <td>{{ $dosen->jenis_kelamin }}</td>
                <td>{{ $dosen->program_studi }}</td>
                <td>{{ $dosen->pendidikan_terakhir }}</td>
                <td>{{ $dosen->status_aktivitas }}</td>
                <td>
                    <a href="{{ route('dosen.show', $dosen->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <form action="{{ route('dosen.destroy', $dosen->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted">Data tidak tersedia</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
