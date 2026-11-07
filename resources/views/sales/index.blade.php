@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">Riwayat Transaksi</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                            <td class="fw-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td>
                                @if($sale->status === 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">Paid</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Void</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection