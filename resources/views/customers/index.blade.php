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
        <div class="table-responsive">
            <table id="customers-table" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th>Total Transaksi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editCustomerForm" action="{{ route('customers.index') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalTitle">Edit Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCustomerName" class="form-label">Nama</label>
                        <input type="text" name="name" id="editCustomerName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCustomerPhone" class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" id="editCustomerPhone" class="form-control" placeholder="0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                    </div>
                    <div class="mb-3">
                        <label for="editCustomerEmail" class="form-label">Email</label>
                        <input type="email" name="email" id="editCustomerEmail" class="form-control">
                    </div>
                    <div>
                        <label for="editCustomerAddress" class="form-label">Alamat</label>
                        <textarea name="address" id="editCustomerAddress" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-arrow-repeat me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
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
        $('#customers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: @json(route('customers.index')),
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                { data: 'email', name: 'email', searchable: false },
                { data: 'sales_count', name: 'sales_count', searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ pelanggan',
                infoEmpty: 'Tidak ada pelanggan',
                infoFiltered: '(disaring dari _MAX_ pelanggan)',
                zeroRecords: 'Pelanggan tidak ditemukan',
                emptyTable: 'Belum ada data pelanggan',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });

        const editCustomerModal = document.getElementById('editCustomerModal');
        editCustomerModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!(button instanceof HTMLElement)) {
                return;
            }

            document.getElementById('editCustomerForm').action = button.dataset.updateUrl;
            document.getElementById('editCustomerName').value = button.dataset.name ?? '';
            document.getElementById('editCustomerPhone').value = button.dataset.phone ?? '';
            document.getElementById('editCustomerEmail').value = button.dataset.email ?? '';
            document.getElementById('editCustomerAddress').value = button.dataset.address ?? '';
        });
    });
</script>
@endpush