@extends('layouts.app')
@section('title', 'Detail Produk')
@section('content')
    <div class="card" style="max-width: 600px;">
        <div class="card-header d-flex justify-content-between"><span>Detail Produk</span><a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a></div>
        <div class="card-body">
            <div class="d-flex gap-4 mb-4">
                @if($product->image) <img src="{{ asset('storage/' . $product->image) }}" style="width: 120px; height: 120px; object-fit: cover;" class="rounded border">
                @else <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 120px; height: 120px; font-size: 2rem;"><i class="bi bi-image text-muted"></i></div> @endif
                <div><h4 class="mb-1">{{ $product->name }}</h4><div class="text-muted mb-2">{{ $product->sku }} · {{ $product->category->name }}</div><h5 class="text-primary mb-0">@rupiah($product->selling_price)</h5></div>
            </div>
            <table class="table table-sm table-bordered">
                <tr><th style="width: 150px;">Harga Modal</th><td>@rupiah($product->purchase_price)</td></tr>
                <tr><th>Stok Tersedia</th><td>{{ $product->stock }} {{ $product->unit }}</td></tr>
                <tr><th>Batas Minimal</th><td>{{ $product->min_stock }} {{ $product->unit }}</td></tr>
                <tr><th>Status</th><td>{!! $product->is_active ? '<span class="badge badge-soft-success">Aktif</span>' : '<span class="badge badge-soft-secondary">Nonaktif</span>' !!}</td></tr>
            </table>
        </div>
    </div>
@endsection