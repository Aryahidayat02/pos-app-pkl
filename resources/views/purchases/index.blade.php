@extends('layouts.app')
@section('title', 'Stok Masuk')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0">Riwayat Stok Masuk</h5>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Catat Stok Masuk</a>
    </div>
    <div class="card"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>No. Dokumen</th><th>Tanggal</th><th>Supplier</th><th class="amount">Total Belanja</th><th>Petugas</th><th style="width: 80px;" class="text-center">Aksi</th></tr></thead>
            <tbody>
                @forelse($purchases as $p)
                    <tr>
                        <td class="fw-medium">{{ $p->purchase_number }}</td>
                        <td>{{ $p->purchase_date->format('d/m/Y') }}</td>
                        <td>{{ $p->supplier->name }}</td>
                        <td class="amount">@rupiah($p->total_amount)</td>
                        <td>{{ $p->user->name }}</td>
                        <td class="text-center"><a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat stok masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $purchases->links() }}</div>
@endsection