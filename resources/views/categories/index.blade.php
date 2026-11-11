@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form class="d-flex gap-2" method="GET">
            {{-- DIGANTI DARI $search JADI request('search') --}}
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari kategori..." style="width: 220px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-light">Reset</a>
            @endif
        </form>
        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Kategori</a>
    </div>
    <div class="card"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Nama</th><th>Deskripsi</th><th class="amount">Jumlah Produk</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                @forelse($categories as $i => $category)
                    <tr>
                        <td>{{ $categories->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $category->name }}</td>
                        <td class="text-muted">{{ $category->description ?? '—' }}</td>
                        <td class="amount">{{ $category->products_count }}</td>
                        <td class="text-center">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $categories->links() }}</div>
@endsection