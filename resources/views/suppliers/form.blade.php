@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit Supplier' : 'Tambah Supplier')
@section('content')
    <div class="card" style="max-width: 600px;">
        <div class="card-header">{{ $supplier->exists ? 'Edit Supplier' : 'Tambah Supplier' }}</div>
        <div class="card-body">
            <form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
                @csrf @if($supplier->exists) @method('PUT') @endif
                <div class="mb-3"><label class="form-label">Nama Perusahaan</label><input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="form-control @error('name') is-invalid @enderror">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Nama Kontak (Opsional)</label><input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="form-control"></div>
                <div class="mb-3"><label class="form-label">No. HP / Telp</label><input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-control @error('phone') is-invalid @enderror">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Alamat (Opsional)</label><textarea name="address" rows="3" class="form-control">{{ old('address', $supplier->address) }}</textarea></div>
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection