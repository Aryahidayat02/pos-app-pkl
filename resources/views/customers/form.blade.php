@extends('layouts.app')
@section('title', $customer->exists ? 'Edit Pelanggan' : 'Tambah Pelanggan')
@section('content')
    <div class="card" style="max-width: 600px;">
        <div class="card-header">{{ $customer->exists ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</div>
        <div class="card-body">
            <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
                @csrf @if($customer->exists) @method('PUT') @endif
                <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-control @error('name') is-invalid @enderror">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">No. HP (Opsional)</label><input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control @error('phone') is-invalid @enderror">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Email (Opsional)</label><input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control @error('email') is-invalid @enderror">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Alamat (Opsional)</label><textarea name="address" rows="3" class="form-control">{{ old('address', $customer->address) }}</textarea></div>
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection