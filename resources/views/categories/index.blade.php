@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Daftar Kategori</h5>
        @if(auth()->user()->role === 'admin')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </button>
        @endif
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="categories-table" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Produk</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editCategoryForm" action="{{ route('categories.index') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalTitle">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Nama Kategori</label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" required>
                    </div>
                    <div>
                        <label for="editCategoryDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" id="editCategoryDescription" class="form-control" rows="2"></textarea>
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

<!-- MODAL TAMBAH KATEGORI -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
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
        $('#categories-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: @json(route('categories.index')),
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description', searchable: false },
                { data: 'products_count', name: 'products_count', searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ kategori',
                infoEmpty: 'Tidak ada kategori',
                infoFiltered: '(disaring dari _MAX_ kategori)',
                zeroRecords: 'Kategori tidak ditemukan',
                emptyTable: 'Belum ada kategori',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });

        const editCategoryModal = document.getElementById('editCategoryModal');
        editCategoryModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!(button instanceof HTMLElement)) {
                return;
            }

            document.getElementById('editCategoryForm').action = button.dataset.updateUrl;
            document.getElementById('editCategoryName').value = button.dataset.name ?? '';
            document.getElementById('editCategoryDescription').value = button.dataset.description ?? '';
        });
    });
</script>
@endpush