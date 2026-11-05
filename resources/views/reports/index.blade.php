@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')
    
    <!-- Filter Tanggal -->
    <div class="card mb-4 no-print">
        <div class="card-body">
            <form class="row g-2 align-items-end" method="GET">
                <div class="col-md-4">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Tampilkan Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ringkasan (4 Kartu) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label">Total Omzet</div>
                <div class="stat-value">@rupiah($summary->total_omzet ?? 0)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-label">Laba Kotor</div>
                <div class="stat-value">@rupiah($profit->laba_kotor ?? 0)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-label">Jumlah Transaksi</div>
                <div class="stat-value">{{ number_format($summary->total_transaksi ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e0e7ff; color:#4338ca;"><i class="bi bi-tag"></i></div>
                <div class="stat-label">Total Diskon</div>
                <div class="stat-value">@rupiah($summary->total_diskon ?? 0)</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kiri: Penjualan Per Hari -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Penjualan Per Hari</div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Tanggal</th><th class="text-center">Transaksi</th><th class="amount">Omzet</th></tr></thead>
                        <tbody>
                            @forelse($dailySales as $ds)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($ds->tanggal)->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $ds->jumlah_transaksi }}</td>
                                    <td class="amount">@rupiah($ds->omzet)</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kanan: Produk Terlaris -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">10 Produk Terlaris</div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Produk</th><th class="text-center">Terjual</th><th class="amount">Omzet</th></tr></thead>
                        <tbody>
                            @forelse($topProducts as $tp)
                                <tr>
                                    <td>{{ $tp->product->name ?? 'Produk Dihapus' }}</td>
                                    <td class="text-center">{{ $tp->total_qty }}</td>
                                    <td class="amount">@rupiah($tp->total_omzet)</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada penjualan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bawah: Stok Menipis -->
    <div class="card mt-4">
        <div class="card-header text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Peringatan Stok Menipis</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Produk</th><th>SKU</th><th class="text-center">Sisa Stok</th><th class="text-center">Batas Minimal</th></tr></thead>
                <tbody>
                    @forelse($lowStockProducts as $lsp)
                        <tr>
                            <td class="fw-medium">{{ $lsp->name }}</td>
                            <td class="text-muted">{{ $lsp->sku }}</td>
                            <td class="text-center text-danger fw-bold">{{ $lsp->stock }}</td>
                            <td class="text-center">{{ $lsp->min_stock }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Semua stok aman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection