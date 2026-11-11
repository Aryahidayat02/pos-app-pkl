@extends('layouts.app')
@section('title', 'Detail Transaksi - ' . $sale->invoice_number)
@section('content')

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1">{{ $sale->invoice_number }}</h5>
                <p class="text-muted mb-0">{{ $sale->sale_date->format('d F Y H:i') }}</p>
            </div>
            <div class="text-end">
                @if($sale->status === 'paid')
                    <span class="badge badge-ok">Paid</span>
                @else
                    <span class="badge badge-void">Void</span>
                @endif
                <div class="small text-muted mt-2">Kasir: {{ $sale->user->name }}</div>
                <div class="small text-muted">Pelanggan: {{ $sale->customer->name ?? 'Umum' }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="amount">Harga</th>
                        <th class="text-center">Qty</th>
                        <th class="amount">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($sale->items as $it)
                    <tr>
                        <td class="fw-semibold">{{ $it->product_name }}</td>
                        <td class="amount">{{ rupiah($it->price) }}</td>
                        <td class="text-center">{{ $it->quantity }}</td>
                        <td class="amount">{{ rupiah($it->subtotal) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-6">
                <table class="table table-sm">
                    <tr>
                        <td>Subtotal</td>
                        <td class="amount">{{ rupiah($sale->subtotal) }}</td>
                    </tr>
                    <tr>
                        <td>Diskon</td>
                        <td class="amount">{{ rupiah($sale->discount) }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Total</td>
                        <td class="amount">{{ rupiah($sale->total) }}</td>
                    </tr>
                    <tr>
                        <td>Bayar ({{ strtoupper($sale->payment_method) }})</td>
                        <td class="amount">{{ rupiah($sale->paid_amount) }}</td>
                    </tr>
                    <tr>
                        <td>Kembalian</td>
                        <td class="amount">{{ rupiah($sale->change_amount) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($sale->note)
            <div class="alert alert-light border mt-3 mb-3">
                <strong>Catatan:</strong> {{ $sale->note }}
            </div>
        @endif

        <div class="mt-3 d-flex gap-2 flex-wrap">
            <a href="{{ route('sales.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i> Cetak Struk
            </a>
            
            {{-- TOMBOL VOID KHUSUS ADMIN --}}
            @if(auth()->user()->role === 'admin' && $sale->status === 'paid')
                <button type="button" class="btn btn-outline-danger"
                        data-bs-toggle="modal" data-bs-target="#voidModal">
                    <i class="bi bi-x-circle me-1"></i> Void Transaksi
                </button>
            @endif
        </div>
    </div>
</div>

{{-- MODAL VOID --}}
@if(auth()->user()->role === 'admin' && $sale->status === 'paid')
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('sales.void', $sale) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Void Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">
                        Stok produk akan dikembalikan. Aksi ini tidak bisa dibatalkan.
                    </p>
                    <label class="form-label fw-semibold">Alasan Void <span class="text-danger">*</span></label>
                    <textarea name="note" class="form-control" rows="3" placeholder="misal: salah input produk" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger">Void Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection