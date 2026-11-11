@extends('layouts.app')
@section('title', 'Produk')
@section('content')

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4">
        <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama atau SKU...">
    </div>
    <div class="col-md-3">
        <select name="category_id" class="form-select">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="1" @selected(request('status') === '1')>Aktif</option>
            <option value="0" @selected(request('status') === '0')>Nonaktif</option>
        </select>
    </div>
    <div class="col-md-1">
        <button class="btn btn-outline-secondary w-100"><i class="bi bi-funnel"></i></button>
    </div>
    <div class="col-md-2 text-end">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Produk</a>
        @endif
    </div>
</form>

@if(request()->hasAny(['search', 'category_id', 'status']))
    <div class="mb-2">
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-light"><i class="bi bi-x-lg"></i> Reset Filter</a>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width:60px"></th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="amount">Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th>Status</th>
                    <th class="text-end" style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        @if($p->image)
                            <img src="{{ asset('storage/'.$p->image) }}" width="36" height="36" style="object-fit:cover;border-radius:6px" alt="">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px">
                                <i class="bi bi-box text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td><code class="small">{{ $p->sku }}</code></td>
                    <td>
                        <div class="fw-semibold">{{ $p->name }}</div>
                        <div class="text-muted small">{{ $p->unit }}</div>
                    </td>
                    <td>{{ $p->category->name }}</td>
                    <td class="amount">{{ rupiah($p->selling_price) }}</td>
                    <td class="text-center">
                        <span class="{{ $p->stock <= $p->min_stock ? 'text-danger fw-bold' : '' }}">{{ $p->stock }}</span>
                        @if($p->stock <= $p->min_stock)
                            <span class="badge badge-low ms-1">Menipis</span>
                        @endif
                    </td>
                    <td>
                        {{-- BAGIAN STATUS AKTIF / NONAKTIF --}}
                        @if($p->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">Aktif</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('products.show', $p) }}" class="btn btn-sm btn-outline-secondary" title="Lihat"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk {{ $p->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="bi bi-box-seam"></i>
                        @if(request()->hasAny(['search', 'category_id', 'status'])) Tidak ada produk yang cocok dengan filter.
                        @else Belum ada produk. Tambah produk pertama. @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center">
    <small class="text-muted">
        Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
        dari {{ $products->total() }} produk
    </small>
    <div>
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection