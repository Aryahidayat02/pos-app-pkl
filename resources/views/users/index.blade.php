@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Daftar Pengguna</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="bi bi-plus-lg"></i> Tambah User
        </button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="users-table" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahUserTitle">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="8" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 300,
            ajax: @json(route('users.index')),
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', name: 'users.id', orderable: false, searchable: false },
                { data: 'name', name: 'users.name' },
                { data: 'email', name: 'users.email' },
                { data: 'role', name: 'users.role' },
                { data: 'status_display', name: 'users.is_active', searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ pengguna',
                infoEmpty: 'Tidak ada pengguna',
                infoFiltered: '(disaring dari _MAX_ pengguna)',
                zeroRecords: 'Pengguna tidak ditemukan',
                emptyTable: 'Belum ada data pengguna',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });
    });
</script>
@endpush
