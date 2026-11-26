@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</div>
        <div class="card-body">
            <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" enctype="multipart/form-data" data-minimum-loading-ms="500">
                @csrf @if($product->exists) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        @php($selectedCategoryId = old('category_id', $product->category_id))
                        @php($selectedCategory = $categories->firstWhere('id', $selectedCategoryId))
                        <label for="category-search" class="form-label">Kategori</label>
                        <div data-category-search>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                                <input type="search" id="category-search" class="form-control @error('category_id') is-invalid @enderror" list="product-categories" data-category-search-input placeholder="Cari kategori..." autocomplete="off" value="{{ $selectedCategory?->name }}" required>
                            </div>
                            <datalist id="product-categories">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" data-category-id="{{ $cat->id }}"></option>
                            @endforeach
                            </datalist>
                            <input type="hidden" name="category_id" data-category-search-value value="{{ $selectedCategoryId }}">
                        </div>
                        @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="product-sku" class="form-label">SKU</label>
                        <input type="text" id="product-sku" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control @error('sku') is-invalid @enderror" required>
                        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="product-barcode" class="form-label">Barcode (Opsional)</label>
                        <input type="text" id="product-barcode" name="barcode" inputmode="numeric" value="{{ old('barcode', $product->barcode) }}" class="form-control @error('barcode') is-invalid @enderror" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="product-name" class="form-label">Nama Produk</label>
                        <input type="text" id="product-name" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="product-unit" class="form-label">Satuan</label>
                        <input type="search" id="product-unit" name="unit" list="product-unit-options" value="{{ old('unit', $product->unit) }}" class="form-control @error('unit') is-invalid @enderror" placeholder="Ketik atau cari satuan..." autocomplete="off" required>
                        <datalist id="product-unit-options">
                            @foreach($units as $unit)
                                <option value="{{ $unit }}"></option>
                            @endforeach
                            @foreach(['pcs', 'kg', 'gram', 'liter', 'ml', 'box', 'pak', 'bungkus', 'botol', 'sak', 'lusin'] as $unit)
                                <option value="{{ $unit }}"></option>
                            @endforeach
                        </datalist>
                        @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6"><label class="form-label">Harga Modal (Rp)</label><input type="text" name="purchase_price" inputmode="decimal" data-number-format="id" data-number-decimals="2" data-number-min="0" value="{{ number_format((float) old('purchase_price', $product->purchase_price), 2, ',', '.') }}" class="form-control product-purchase-price text-end @error('purchase_price') is-invalid @enderror">@error('purchase_price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Harga Jual (Rp)</label><input type="text" name="selling_price" inputmode="decimal" data-number-format="id" data-number-decimals="2" data-number-min="0" value="{{ number_format((float) old('selling_price', $product->selling_price), 2, ',', '.') }}" class="form-control product-selling-price text-end @error('selling_price') is-invalid @enderror">@error('selling_price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-12"><div class="product-price-feedback form-text" aria-live="polite"></div></div>
                    <div class="col-md-6"><label class="form-label">Stok Awal</label><input type="text" name="stock" inputmode="numeric" data-number-format="id" data-number-min="0" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control text-end @error('stock') is-invalid @enderror" {{ $product->exists ? 'readonly' : '' }}>@if($product->exists)<div class="form-text text-danger">Stok tidak bisa diubah di sini.</div>@endif</div>
                    <div class="col-md-6"><label class="form-label">Batas Minimal Stok</label><input type="text" name="min_stock" inputmode="numeric" data-number-format="id" data-number-min="0" value="{{ old('min_stock', $product->min_stock ?? 5) }}" class="form-control text-end @error('min_stock') is-invalid @enderror">@error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Gambar Produk</label><input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png">@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror @if($product->image)<img src="{{ asset('storage/' . $product->image) }}" style="height: 60px;" class="mt-2 rounded border" alt="Gambar {{ $product->name }}">@endif</div>
                    <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}><label class="form-check-label">Produk Aktif</label></div></div>
                </div>
                <hr class="my-4">
                <button type="submit" class="btn btn-primary"><i class="bi {{ $product->exists ? 'bi-arrow-repeat' : 'bi-save' }} me-1"></i>{{ $product->exists ? 'Update' : 'Simpan' }}</button>
                <a href="{{ route('products.index') }}" data-form-modal-cancel class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Batal</a>
            </form>
        </div>
    </div>
@endsection
