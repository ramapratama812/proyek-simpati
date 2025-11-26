@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Permohonan Pendaftaran Akun</h4>

    @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Catatan</th>
                <th>Diaju­kan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($requests as $req)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $req->name }}</td>
                <td>{{ $req->email }}</td>
                <td>{{ ucfirst($req->role) }}</td>
                <td>{{ strtoupper($req->status) }}</td>
                <td>{{ $req->note }}</td>
                <td>{{ $req->created_at->format('d-m-Y H:i') }}</td>
                <td>
                    @if($req->status === 'pending')
                        <form action="{{ route('admin.registration-requests.approve', $req) }}"
                              method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm"
                                    onclick="return confirm('Setujui permohonan ini?')">
                                Setujui
                            </button>
                        </form>

                        <form action="{{ route('admin.registration-requests.reject', $req) }}"
                              method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tolak permohonan ini?')">
                                Tolak
                            </button>
                        </form>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada permohonan.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $requests->links() }}
</div>
@endsection
