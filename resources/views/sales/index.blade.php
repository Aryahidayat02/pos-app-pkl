@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari Invoice</label>
                    <input type="text" name="invoice" value="{{ request('invoice') }}" class="form-control form-control-sm" placeholder="INV-...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Invoice</th><th>Tanggal</th><th>Kasir</th><th>Pelanggan</th><th class="amount">Total</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td class="fw-medium">{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                        <td class="amount">@rupiah($sale->total)</td>
                        <td>{!! $sale->status === 'paid' ? '<span class="badge badge-soft-success">Paid</span>' : '<span class="badge badge-soft-danger">Void</span>' !!}</td>
                        <td class="text-center">
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Lihat Struk"><i class="bi bi-receipt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $sales->links() }}</div>
@endsection