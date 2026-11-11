@extends('layouts.app')
@section('title', 'Supplier')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari supplier..." style="width: 250px;">
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        @if(request('search'))
            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-light">Reset</a>
        @endif
    </form>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Supplier</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kontak</th>
                    <th>No. HP</th>
                    <th class="text-center">Stok Masuk</th>
                    <th style="width: 120px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                    <tr>
                        <td class="fw-medium">{{ $s->name }}</td>
                        <td>{{ $s->contact_person ?? '-' }}</td>
                        <td>{{ $s->phone }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $s->purchases_count }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('suppliers.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus supplier?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $suppliers->links() }}</div>

@endsection