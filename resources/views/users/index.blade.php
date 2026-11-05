@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0">Daftar Pengguna</h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah User</a>
    </div>
    <div class="card"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th style="width: 120px;" class="text-center">Aksi</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td class="fw-medium">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge badge-soft-primary text-capitalize">{{ $u->role }}</span></td>
                        <td>{!! $u->is_active ? '<span class="badge badge-soft-success">Aktif</span>' : '<span class="badge badge-soft-danger">Nonaktif</span>' !!}</td>
                        <td class="text-center">
                            <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $users->links() }}</div>
@endsection