@extends('layouts.app')

@section('content')
<h3>Tambah Prestasi</h3>

<form action="{{ route('dosen-prestasi.store') }}" method="POST" enctype="multipart/form-data">
@csrf

@include('dosen_prestasi.form')

<button class="btn btn-primary">Simpan</button>
</form>
@endsection
