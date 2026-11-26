@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <span class="d-inline-flex rounded-circle bg-primary-subtle text-primary p-2"><i class="bi bi-sliders"></i></span>
                <div>
                    <h5 class="mb-0 fw-bold">Pengaturan Operasional</h5>
                    <small class="text-muted">Sesuaikan identitas toko dan perilaku aplikasi.</small>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-5">
                            <div class="rounded-4 border p-4 text-center h-100" style="background: var(--pos-surface-muted);">
                                <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center overflow-hidden mb-3" style="width: 112px; height: 112px;" data-image-preview>
                                    <img
                                        src="{{ $settings['store_logo'] ? asset('storage/' . $settings['store_logo']) : '' }}"
                                        alt="Preview logo toko"
                                        class="w-100 h-100 object-fit-cover"
                                        data-image-preview-image
                                        data-original-src="{{ $settings['store_logo'] ? asset('storage/' . $settings['store_logo']) : '' }}"
                                        @if(! $settings['store_logo']) hidden @endif
                                    >
                                    <span class="text-primary" data-image-preview-placeholder @if($settings['store_logo']) hidden @endif>
                                        <i class="bi bi-shop fs-1" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <label for="settings-logo" class="form-label fw-bold d-block">Logo Toko</label>
                                <input type="file" id="settings-logo" name="logo" class="form-control" accept="image/jpeg,image/png" data-image-preview-input>
                                <small class="text-muted d-block mt-2">JPG/PNG, maksimal 2 MB. Dipakai sebagai logo struk dan favicon.</small>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="store-name" class="form-label fw-bold">Nama Toko</label>
                                    <input type="text" id="store-name" name="store_name" class="form-control" value="{{ $settings['store_name'] }}" maxlength="100" required>
                                </div>
                                <div class="col-12">
                                    <label for="store-address" class="form-label fw-bold">Alamat</label>
                                    <textarea id="store-address" name="store_address" class="form-control" rows="2" maxlength="255" required>{{ $settings['store_address'] }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="store-phone" class="form-label fw-bold">No. Telepon</label>
                                    <input type="tel" id="store-phone" name="store_phone" class="form-control" value="{{ $settings['store_phone'] }}" maxlength="20" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="default-payment" class="form-label fw-bold">Pembayaran Utama</label>
                                    <select id="default-payment" name="default_payment_method" class="form-select" required>
                                        <option value="tunai" @selected($settings['default_payment_method'] === 'tunai')>Tunai</option>
                                        <option value="qris" @selected($settings['default_payment_method'] === 'qris')>QRIS</option>
                                        <option value="transfer" @selected($settings['default_payment_method'] === 'transfer')>Transfer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="receipt-footer" class="form-label fw-bold">Pesan Bawah Struk</label>
                            <input type="text" id="receipt-footer" name="receipt_footer" class="form-control" value="{{ $settings['receipt_footer'] }}" maxlength="150">
                        </div>
                        <div class="col-md-5 d-flex align-items-end">
                            <div class="d-flex flex-column gap-2 w-100">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="show-logo-receipt" name="show_logo_on_receipt" value="1" @checked($settings['show_logo_on_receipt'])>
                                    <label class="form-check-label" for="show-logo-receipt">Tampilkan logo pada struk</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="low-stock-alert" name="low_stock_alert" value="1" @checked($settings['low_stock_alert'])>
                                    <label class="form-check-label" for="low-stock-alert">Aktifkan peringatan stok menipis</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i>Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <span class="d-inline-flex rounded-circle bg-success-subtle text-success p-3 mb-3"><i class="bi bi-lightning-charge fs-4"></i></span>
                <h5 class="fw-bold">Pengaturan Aktif</h5>
                <p class="text-muted small">Perubahan langsung diterapkan pada transaksi dan tampilan aplikasi.</p>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-shop me-2 text-primary"></i>Identitas toko</span>
                        <span class="badge text-bg-success">Aktif</span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-receipt me-2 text-primary"></i>Logo struk</span>
                        <span class="badge {{ $settings['show_logo_on_receipt'] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $settings['show_logo_on_receipt'] ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bell me-2 text-primary"></i>Peringatan stok</span>
                        <span class="badge {{ $settings['low_stock_alert'] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $settings['low_stock_alert'] ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection