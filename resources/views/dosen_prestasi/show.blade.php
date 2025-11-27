@extends('layouts.app')

@section('content')
<h3>Detail Prestasi</h3>

<p><b>Dosen:</b> {{ $dosenPrestasi->dosen->nama }}</p>
<p><b>Judul:</b> {{ $dosenPrestasi->judul }}</p>
<p><b>Kategori:</b> {{ $dosenPrestasi->kategori }}</p>
<p><b>Tahun:</b> {{ $dosenPrestasi->tahun }}</p>
<p><b>Tingkat:</b> {{ $dosenPrestasi->tingkat }}</p>

@if($dosenPrestasi->file_bukti)
<p><b>File Bukti:</b></p>
<a href="{{ $dosenPrestasi->file_url }}" target="_blank">Download File</a>
@endif

<a href="{{ route('dosen-prestasi.index') }}" class="btn btn-secondary mt-3">Kembali</a>
@endsection
