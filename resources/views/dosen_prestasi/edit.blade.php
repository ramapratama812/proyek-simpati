@extends('layouts.app')

@section('content')
<h3>Edit Prestasi</h3>

<form action="{{ route('dosen-prestasi.update',$dosenPrestasi->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

@include('dosen_prestasi.form', ['dosenPrestasi'=>$dosenPrestasi])

<button class="btn btn-primary">Update</button>
</form>
@endsection
