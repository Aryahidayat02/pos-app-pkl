@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Daftar Produk</h5>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </a>
        @endif
    </div>
    
    <div class="card-body">
        <!-- Form Filter & Pencarian -->
        <form action="{{ route('products.index') }}" method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / SKU..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary btn-sm w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Gambar</th>
                        <th>SKU</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    @if(filter_var($product->image, FILTER_VALIDATE_URL))
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td class="fw-bold">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td>
                                @if($product->stock <= $product->min_stock)
                                    <span class="text-danger fw-bold">{{ $product->stock }} {{ $product->unit }}</span>
                                @else
                                    {{ $product->stock }} {{ $product->unit }}
                                @endif
                            </td>
                            <td>
                                <!-- LOGIKA STATUS DINAMIS -->
                                @if($product->is_active == 1)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <!-- LOGIKA HAPUS -->
                                    @if($product->saleItems()->count() > 0 || $product->purchaseItems()->count() > 0)
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak bisa dihapus karena sudah punya riwayat transaksi">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($products->hasPages())
        <div class="card-footer bg-white">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection