@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Pengaturan Toko</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4 text-center">
                        @if($settings['store_logo'])
                            <img src="{{ asset('storage/' . $settings['store_logo']) }}" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-2" style="width: 100px; height: 100px;">
                                <i class="bi bi-shop text-white fs-1"></i>
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control form-control-sm mt-2">
                        <small class="text-muted">Upload logo toko (JPG/PNG, max 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Toko</label>
                        <input type="text" name="store_name" class="form-control" value="{{ $settings['store_name'] }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <input type="text" name="store_address" class="form-control" value="{{ $settings['store_address'] }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">No. Telepon</label>
                        <input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone'] }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Footer Struk</label>
                        <input type="text" name="receipt_footer" class="form-control" value="{{ $settings['receipt_footer'] }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection