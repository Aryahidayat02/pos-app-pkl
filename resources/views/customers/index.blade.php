@extends('layouts.app')
@section('title', 'Pelanggan')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama / no HP..." style="width: 250px;">
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        @if(request('search'))
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-light">Reset</a>
        @endif
    </form>
    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Pelanggan</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No. HP</th>
                    <th>Email</th>
                    <th class="text-center">Total Transaksi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td class="fw-medium">{{ $customer->name }}</td>
                        <td>{{ $customer->phone ?: '—' }}</td>
                        <td>{{ $customer->email ?: '—' }}</td>
                        <td class="text-center"><span class="badge badge-soft-primary">{{ $customer->sales_count }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary" title="Detail"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pelanggan?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data pelanggan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>

@endsection