@extends('layouts.app')

@section('title', isset($customer) ? 'Edit Pelanggan' : 'Tambah Pelanggan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ isset($customer) ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ isset($customer) ? route('customers.update', $customer->id) : route('customers.store') }}" method="POST">
                    @csrf
                    @if(isset($customer))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name ?? '') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone ?? '') }}" placeholder="Contoh: 0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email ?? '') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $customer->address ?? '') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('customers.index') }}" data-form-modal-cancel class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi {{ isset($customer) ? 'bi-arrow-repeat' : 'bi-save' }} me-1"></i>{{ isset($customer) ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection