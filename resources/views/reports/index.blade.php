@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')

<form class="row g-2 mb-3 align-items-end" method="GET">
    <div class="col-md-3">
        <label class="small text-muted mb-1">Dari Tanggal</label>
        <input type="date" name="start_date" value="{{ $start }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="small text-muted mb-1">Sampai Tanggal</label>
        <input type="date" name="end_date" value="{{ $end }}" class="form-control">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Tampilkan Laporan
        </button>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.export', ['start_date' => $start, 'end_date' => $end]) }}" class="btn btn-success w-100">
            <i class="bi bi-file-earmark-excel"></i> Export ke Excel
        </a>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Omzet</div>
                <div class="stat-value">{{ rupiah($summary['omzet']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Laba Kotor</div>
                <div class="stat-value text-success">{{ rupiah($profit) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Jumlah Transaksi</div>
                <div class="stat-value">{{ $summary['count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Diskon</div>
                <div class="stat-value">{{ rupiah($summary['discount']) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Penjualan Per Hari</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-center">Transaksi</th>
                            <th class="amount">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perDay as $d)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($d->tgl)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $d->trx }}</td>
                            <td class="amount">{{ rupiah($d->omzet) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">10 Produk Terlaris</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Terjual</th>
                            <th class="amount">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $t)
                        <tr>
                            <td>{{ $t->product_name }}</td>
                            <td class="text-center">{{ $t->qty }}</td>
                            <td class="amount">{{ rupiah($t->omzet) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header text-danger">
        <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Menipis
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th class="text-center">Sisa Stok</th>
                    <th class="text-center">Batas Minimal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStock as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td><code>{{ $p->sku }}</code></td>
                    <td class="text-center text-danger fw-bold">{{ $p->stock }}</td>
                    <td class="text-center">{{ $p->min_stock }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Semua stok aman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection