@extends('layouts.app')
@section('title', 'Detail Stok Masuk')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Detail Stok Masuk</span>
            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="150">No. Dokumen</th><td>{{ $purchase->purchase_number }}</td></tr>
                        <tr><th>Tanggal</th><td>{{ $purchase->purchase_date->format('d/m/Y') }}</td></tr>
                        <tr><th>Supplier</th><td>{{ $purchase->supplier->name }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr><th width="150">Petugas</th><td>{{ $purchase->user->name }}</td></tr>
                        <tr><th>Catatan</th><td>{{ $purchase->note ?: '—' }}</td></tr>
                        <tr><th>Total Belanja</th><td class="fw-bold text-primary">@rupiah($purchase->total_amount)</td></tr>
                    </table>
                </div>
            </div>
            <table class="table table-bordered">
                <thead><tr><th>Produk</th><th class="text-center">Qty</th><th class="amount">Harga Beli</th><th class="amount">Subtotal</th></tr></thead>
                <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="amount">@rupiah($item->purchase_price)</td>
                            <td class="amount">@rupiah($item->subtotal)</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection