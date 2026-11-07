@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- CARD STATISTIK -->
    <div class="row g-3 mb-4">
        <!-- Penjualan Hari Ini -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <h6 class="text-muted mb-0 fw-semibold">Penjualan Hari Ini</h6>
                    </div>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Transaksi Hari Ini -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded p-2 me-3">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                        <h6 class="text-muted mb-0 fw-semibold">Transaksi Hari Ini</h6>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $todaySalesCount }}</h3>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-2 me-3">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                        <h6 class="text-muted mb-0 fw-semibold">Stok Menipis</h6>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $lowStockCount }}</h3>
                </div>
            </div>
        </div>

        <!-- Produk Aktif -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded p-2 me-3">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <h6 class="text-muted mb-0 fw-semibold">Produk Aktif</h6>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $activeProductsCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL TRANSAKSI TERAKHIR -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark">5 Transaksi Terakhir</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th class="text-end pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $sale->invoice_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="text-end pe-4">
                                    @if($sale->status === 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">Paid</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">Void</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection