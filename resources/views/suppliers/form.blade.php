@extends('layouts.app')

@section('title', isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier Baru' }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" method="POST">
                    @csrf
                    @if(isset($supplier))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $supplier->name ?? '') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $supplier->phone ?? '') }}" placeholder="Contoh: 0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('suppliers.index') }}" data-form-modal-cancel class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi {{ isset($supplier) ? 'bi-arrow-repeat' : 'bi-save' }} me-1"></i>{{ isset($supplier) ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection