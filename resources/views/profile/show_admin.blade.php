@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-center">PROFILE ADMIN</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted d-block">Nama</label>
                        <span class="fw-semibold">{{ $user->name }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Email</label>
                        <span class="fw-semibold">{{ $user->email }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Username</label>
                        <span class="fw-semibold">{{ $user->username ?? '-' }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted d-block">Role</label>
                        <span class="fw-semibold">{{ strtoupper($role) }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Status Akun</label>
                        <span class="fw-semibold">
                            {{ $user->status ?? 'Aktif' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">Terdaftar Sejak</label>
                        <span class="fw-semibold">
                            {{ optional($user->created_at)->format('d-m-Y H:i') ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
