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
        <div class="table-responsive">
            <table id="suppliers-table" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Supplier</th>
                        <th>Contact Person</th>
                        <th>Qty Masuk</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editSupplierForm" action="{{ route('suppliers.index') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalTitle">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editSupplierName" class="form-label">Nama Supplier</label>
                        <input type="text" name="name" id="editSupplierName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editSupplierContactPerson" class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" id="editSupplierContactPerson" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="editSupplierPhone" class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" id="editSupplierPhone" class="form-control" placeholder="0812345678901" inputmode="numeric" pattern="[0-9]{1,13}" maxlength="13" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                    </div>
                    <div>
                        <label for="editSupplierAddress" class="form-label">Alamat</label>
                        <textarea name="address" id="editSupplierAddress" class="form-control" rows="2"></textarea>
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
        $('#suppliers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: @json(route('suppliers.index')),
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'contact_person', name: 'contact_person' },
                { data: 'supplied_quantity', name: 'supplied_quantity', searchable: false },
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ supplier',
                infoEmpty: 'Tidak ada supplier',
                infoFiltered: '(disaring dari _MAX_ supplier)',
                zeroRecords: 'Supplier tidak ditemukan',
                emptyTable: 'Belum ada data supplier',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });

        const editSupplierModal = document.getElementById('editSupplierModal');
        editSupplierModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!(button instanceof HTMLElement)) {
                return;
            }

            document.getElementById('editSupplierForm').action = button.dataset.updateUrl;
            document.getElementById('editSupplierName').value = button.dataset.name ?? '';
            document.getElementById('editSupplierContactPerson').value = button.dataset.contactPerson ?? '';
            document.getElementById('editSupplierPhone').value = button.dataset.phone ?? '';
            document.getElementById('editSupplierAddress').value = button.dataset.address ?? '';
        });
    });
</script>
@endpush