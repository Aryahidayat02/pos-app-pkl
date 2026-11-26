@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold text-dark">Laporan Penjualan</h5>
            <a href="{{ route('reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total Omzet</h6>
                <h4 class="fw-bold text-primary">Rp {{ number_format($omzet, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body">
                <h6 class="text-muted mb-1">Laba Kotor</h6>
                <h4 class="fw-bold text-success">Rp {{ number_format($labaKotor, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body">
                <h6 class="text-muted mb-1">Jumlah Transaksi</h6>
                <h4 class="fw-bold">{{ $transaksi }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total Diskon</h6>
                <h4 class="fw-bold text-danger">Rp {{ number_format($diskon, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Grafik Omzet Harian</div>
            <div class="card-body">
                <div id="sales-line-chart" aria-live="polite"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Candlestick Transaksi Harian</div>
            <div class="card-body">
                <div id="sales-candlestick-chart" aria-live="polite"></div>
                <p class="form-text mb-0">Open/High/Low/Close memakai nilai total tiap transaksi pada hari tersebut.</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Penjualan Per Hari -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Penjualan Per Hari</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-center">Transaksi</th>
                            <th class="text-end">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualanHarian as $harian)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($harian->tgl)->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $harian->trx }}</td>
                                <td class="text-end">Rp {{ number_format($harian->omzet, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">10 Produk Terlaris</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Terjual</th>
                            <th class="text-end">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produkTerlaris as $produk)
                            <tr>
                                <td>{{ $produk->product_name }}</td>
                                <td class="text-center">{{ $produk->qty }}</td>
                                <td class="text-end">Rp {{ number_format($produk->omzet, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada produk terjual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Stok Menipis -->
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white fw-bold">Produk Stok Menipis</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Min Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokMenipis as $produk)
                    <tr>
                        <td>{{ $produk->sku }}</td>
                        <td class="fw-bold">{{ $produk->name }}</td>
                        <td class="text-center text-danger fw-bold">{{ $produk->stock }}</td>
                        <td class="text-center">{{ $produk->min_stock }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">Semua stok aman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.reportChartData = {{ \Illuminate\Support\Js::from($chartData) }};
    </script>
    <script src="{{ asset('js/report-charts.js') }}"></script>
@endpush