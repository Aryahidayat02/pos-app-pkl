@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
@if($errors->has('phone'))
    <div class="alert alert-danger">{{ $errors->first('phone') }}</div>
@endif
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Daftar Supplier</h5>
        @if(auth()->user()->role === 'admin')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
                <i class="bi bi-plus-lg"></i> Tambah Supplier
            </button>
        @endif
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('suppliers.index') }}" class="input-group mb-3" style="max-width: 360px;">
            <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama, telepon, atau alamat...">
            <button type="submit" class="btn btn-outline-secondary" aria-label="Cari supplier"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Supplier</th>
                        <th>Contact Person</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="fw-bold">{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ Str::limit($supplier->address, 30) ?? '-' }}</td>
                            <td class="text-end">
                                @if(auth()->user()->role === 'admin')
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $supplier->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" data-delete-name="{{ $supplier->name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        <!-- MODAL EDIT SUPPLIER -->
                        <div class="modal fade" id="modalEdit{{ $supplier->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Supplier: {{ $supplier->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Nama Supplier</label>
                                                <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Contact Person</label>
                                                <input type="text" name="contact_person" class="form-control" value="{{ $supplier->contact_person }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">No. HP <span class="text-danger">*</span></label>
                                                <input type="tel" name="phone" class="form-control" value="{{ $supplier->phone }}" placeholder="0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Alamat</label>
                                                <textarea name="address" class="form-control" rows="2">{{ $supplier->address }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data supplier.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
    <small class="text-muted">
        @if($suppliers->total() > 0)
            Menampilkan {{ $suppliers->firstItem() }} sampai {{ $suppliers->lastItem() }} dari {{ $suppliers->total() }} supplier
        @else
            Tidak ada supplier
        @endif
    </small>
    {{ $suppliers->links() }}
</div>

<!-- MODAL TAMBAH SUPPLIER -->
<div class="modal fade" id="modalTambahSupplier" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection