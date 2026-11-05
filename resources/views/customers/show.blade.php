@extends('layouts.app')
@section('title', 'Detail Pelanggan')
@section('content')
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Profil Pelanggan</div>
                <div class="card-body">
                    <h5>{{ $customer->name }}</h5>
                    <p class="text-muted mb-1"><i class="bi bi-telephone me-1"></i> {{ $customer->phone ?: '—' }}</p>
                    <p class="text-muted mb-1"><i class="bi bi-envelope me-1"></i> {{ $customer->email ?: '—' }}</p>
                    <p class="text-muted mb-3"><i class="bi bi-geo-alt me-1"></i> {{ $customer->address ?: '—' }}</p>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary w-100">Edit Profil</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Riwayat Belanja (10 Terakhir)</div>
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Invoice</th><th>Tanggal</th><th class="amount">Total</th></tr></thead>
                    <tbody>
                        @forelse($customer->sales as $sale)
                            <tr><td>{{ $sale->invoice_number }}</td><td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td><td class="amount">@rupiah($sale->total)</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada riwayat belanja.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
@endsection