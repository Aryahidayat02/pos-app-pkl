@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-label">Penjualan Hari Ini</div>
                <div class="stat-value">@rupiah($todaySales ?? 0)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-label">Transaksi Hari Ini</div>
                <div class="stat-value">{{ $todayCount ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-label">Stok Menipis</div>
                <div class="stat-value">{{ $lowStock ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e0e7ff; color:#4338ca;"><i class="bi bi-box-seam"></i></div>
                <div class="stat-label">Produk Aktif</div>
                <div class="stat-value">{{ $activeProducts ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">5 Transaksi Terakhir</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th class="amount">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales ?? [] as $sale)
                        <tr>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                            <td class="amount">@rupiah($sale->total)</td>
                            <td>
                                @if($sale->status === 'paid')
                                    <span class="badge badge-soft-success">Paid</span>
                                @else
                                    <span class="badge badge-soft-danger">Void</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection