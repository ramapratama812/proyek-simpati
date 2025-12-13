@extends('layouts.app')

@section('content')
<a href="{{ route('dosen-prestasi.create') }}" class="btn btn-primary mb-3">Tambah Prestasi</a>

<table class="table table-bordered">
    <tr>
        <th>Dosen</th>
        <th>Judul</th>
        <th>Tahun</th>
        <th>Aksi</th>
    </tr>

    @foreach($prestasis as $item)
    <tr>
        <td>{{ $item->dosen->nama }}</td>
        <td>{{ $item->judul }}</td>
        <td>{{ $item->tahun }}</td>
        <td>
            <a href="{{ route('dosen-prestasi.show',$item->id) }}" class="btn btn-info btn-sm">Lihat</a>
            <a href="{{ route('dosen-prestasi.edit',$item->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route('dosen-prestasi.destroy',$item->id) }}" method="post" style="display:inline">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

{{ $prestasis->links() }}
@endsection
