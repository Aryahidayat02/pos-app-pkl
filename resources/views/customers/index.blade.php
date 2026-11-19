@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')
@if($errors->has('phone'))
    <div class="alert alert-danger">{{ $errors->first('phone') }}</div>
@endif
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Daftar Pelanggan</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggan">
            <i class="bi bi-plus-lg"></i> Tambah Pelanggan
        </button>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('customers.index') }}" class="input-group mb-3" style="max-width: 360px;">
            <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama atau telepon...">
            <button type="submit" class="btn btn-outline-secondary" aria-label="Cari pelanggan"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th>Total Transaksi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="fw-bold">{{ $customer->name }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>{{ $customer->email ?? '-' }}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $customer->sales_count }}x</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $customer->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" data-delete-name="{{ $customer->name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        <!-- MODAL EDIT PELANGGAN -->
                        <div class="modal fade" id="modalEdit{{ $customer->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Pelanggan: {{ $customer->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">No. HP <span class="text-danger">*</span></label>
                                                <input type="tel" name="phone" class="form-control" value="{{ $customer->phone }}" placeholder="0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Alamat</label>
                                                <textarea name="address" class="form-control" rows="2">{{ $customer->address }}</textarea>
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
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data pelanggan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
    <small class="text-muted">
        @if($customers->total() > 0)
            Menampilkan {{ $customers->firstItem() }} sampai {{ $customers->lastItem() }} dari {{ $customers->total() }} pelanggan
        @else
            Tidak ada pelanggan
        @endif
    </small>
    {{ $customers->links() }}
</div>

<!-- MODAL TAMBAH PELANGGAN -->
<div class="modal fade" id="modalTambahPelanggan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelanggan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
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