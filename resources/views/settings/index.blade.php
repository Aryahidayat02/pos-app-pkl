@extends('layouts.app')
@section('title', 'Pengaturan Toko')
@section('content')
    <div class="card" style="max-width: 700px;">
        <div class="card-header">Profil Toko</div>
        <div class="card-body">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                <div class="mb-3"><label class="form-label">Nama Toko</label><input type="text" name="store_name" value="{{ $settings['store_name'] ?? '' }}" class="form-control @error('store_name') is-invalid @enderror">@error('store_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Alamat</label><input type="text" name="store_address" value="{{ $settings['store_address'] ?? '' }}" class="form-control @error('store_address') is-invalid @enderror">@error('store_address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">No. Telepon</label><input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '' }}" class="form-control @error('store_phone') is-invalid @enderror">@error('store_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Footer Struk</label><input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? '' }}" class="form-control" placeholder="Terima kasih telah berbelanja"></div>
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Pengaturan</button>
            </form>
        </div>
    </div>
@endsection