@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" placeholder="Dari">
    </div>
    <div class="col-md-3">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" placeholder="Sampai">
    </div>
    <div class="col-md-3">
        <input name="invoice" value="{{ request('invoice') }}" class="form-control" placeholder="Cari nomor invoice...">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100"><i class="bi bi-funnel"></i> Filter</button>
    </div>
    @if(request()->hasAny(['start_date', 'end_date', 'invoice']))
        <div class="col-md-1">
            <a href="{{ route('sales.index') }}" class="btn btn-light w-100">Reset</a>
        </div>
    @endif
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th class="amount">Total</th>
                    <th>Status</th>
                    <th class="text-end" style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sales as $s)
                <tr>
                    <td class="fw-semibold">
                        <a href="{{ route('sales.show', $s) }}" class="text-decoration-none">
                            {{ $s->invoice_number }}
                        </a>
                    </td>
                    <td>{{ $s->sale_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>{{ $s->customer->name ?? 'Umum' }}</td>
                    <td class="amount">{{ rupiah($s->total) }}</td>
                    <td>
                        {{-- BAGIAN STATUS PAID / VOID --}}
                        @if($s->status === 'paid')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">Paid</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded">Void</span>
                        @endif
                    </td>
                    <td class="text-end">
                        {{-- TOMBOL DETAIL: Ngarah ke halaman rincian (show) --}}
                        <a href="{{ route('sales.show', $s) }}" class="btn btn-sm btn-outline-secondary" title="Detail Transaksi">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        
                        {{-- TOMBOL CETAK: Ngarah ke halaman struk (receipt) --}}
                        <a href="{{ route('sales.receipt', $s) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak Struk">
                            <i class="bi bi-printer"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-receipt"></i>
                        @if(request()->hasAny(['start_date', 'end_date', 'invoice']))
                            Tidak ada transaksi dengan filter ini.
                        @else
                            Belum ada transaksi.
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center">
    <small class="text-muted">
        Menampilkan {{ $sales->firstItem() ?? 0 }}–{{ $sales->lastItem() ?? 0 }}
        dari {{ $sales->total() }} transaksi
    </small>
    <div>
        {{ $sales->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection