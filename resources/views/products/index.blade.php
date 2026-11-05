@extends('layouts.app')
@section('title', 'Produk')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari nama / SKU..." style="width: 200px;">
            <select name="category_id" class="form-select form-select-sm" style="width: 150px;">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat) <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option> @endforeach
            </select>
            <select name="status" class="form-select form-select-sm" style="width: 120px;">
                <option value="">Semua Status</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i> Filter</button>
        </form>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Produk</a>
        @endif
    </div>
    <div class="card"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Gambar</th><th>SKU</th><th>Nama</th><th>Kategori</th><th class="amount">Harga Jual</th><th class="text-center">Stok</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image) <img src="{{ asset('storage/' . $product->image) }}" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">
                            @else <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-image text-muted"></i></div> @endif
                        </td>
                        <td class="text-muted">{{ $product->sku }}</td>
                        <td class="fw-medium">{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td class="amount">@rupiah($product->selling_price)</td>
                        <td class="text-center">{{ $product->stock }} {{ $product->unit }} @if($product->isLowStock()) <span class="badge badge-soft-warning ms-1">!</span> @endif</td>
                        <td class="text-center">{!! $product->is_active ? '<span class="badge badge-soft-success">Aktif</span>' : '<span class="badge badge-soft-secondary">Nonaktif</span>' !!}</td>
                        <td class="text-center">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @else
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $products->links() }}</div>
@endsection