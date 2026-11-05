@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</div>
        <div class="card-body">
            <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" enctype="multipart/form-data">
                @csrf @if($product->exists) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Kategori</label><select name="category_id" class="form-select @error('category_id') is-invalid @enderror"><option value="">Pilih Kategori</option>@foreach($categories as $cat) <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option> @endforeach</select>@error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">SKU / Barcode</label><input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control @error('sku') is-invalid @enderror">@error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-8"><label class="form-label">Nama Produk</label><input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-4"><label class="form-label">Satuan</label><input type="text" name="unit" value="{{ old('unit', $product->unit) }}" class="form-control @error('unit') is-invalid @enderror">@error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Harga Modal (Rp)</label><input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" class="form-control @error('purchase_price') is-invalid @enderror" min="0">@error('purchase_price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Harga Jual (Rp)</label><input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="form-control @error('selling_price') is-invalid @enderror" min="0">@error('selling_price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Stok Awal</label><input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control @error('stock') is-invalid @enderror" min="0" {{ $product->exists ? 'readonly' : '' }}>@if($product->exists)<div class="form-text text-danger">Stok tidak bisa diubah di sini.</div>@endif</div>
                    <div class="col-md-6"><label class="form-label">Batas Minimal Stok</label><input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 5) }}" class="form-control @error('min_stock') is-invalid @enderror" min="0">@error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Gambar Produk</label><input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">@if($product->image)<img src="{{ asset('storage/' . $product->image) }}" style="height: 60px;" class="mt-2 rounded border">@endif</div>
                    <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}><label class="form-check-label">Produk Aktif</label></div></div>
                </div>
                <hr class="my-4">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection