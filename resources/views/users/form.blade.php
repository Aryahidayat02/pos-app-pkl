@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'Tambah User')
@section('content')
    <div class="card" style="max-width: 600px;">
        <div class="card-header">{{ $user->exists ? 'Edit User' : 'Tambah User' }}</div>
        <div class="card-body">
            <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
                @csrf @if($user->exists) @method('PUT') @endif
                <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password {{ $user->exists ? '(Kosongkan jika tidak ganti)' : '' }}</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $user->exists ? '' : 'required' }}>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Akun Aktif</label>
                </div>
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection