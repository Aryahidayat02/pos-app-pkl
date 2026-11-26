@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Daftar Produk</h5>
        @if(auth()->user()->role === 'admin')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </button>
        @endif
    </div>

    <div class="card-body">
        <div class="d-flex justify-content-end mb-3">
            <div class="input-group input-group-sm" style="max-width: 320px;">
                <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                <input type="search" id="products-search" class="form-control" placeholder="Cari produk..." aria-label="Cari produk">
            </div>
        </div>
        <div class="table-responsive">
            <table id="products-table" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Gambar</th>
                        <th>SKU</th>
                        <th>Barcode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" data-minimum-loading-ms="500">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahProdukTitle">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2" data-category-search>
                        <label for="new-product-category-search" class="form-label">Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                            <input type="search" id="new-product-category-search" class="form-control" list="new-product-categories" data-category-search-input placeholder="Cari kategori..." autocomplete="off" value="{{ $categories->firstWhere('id', old('category_id'))?->name }}" required>
                        </div>
                        <datalist id="new-product-categories">
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}" data-category-id="{{ $category->id }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="category_id" data-category-search-value value="{{ old('category_id') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Barcode (Opsional)</label>
                        <input type="text" name="barcode" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label for="new-product-unit" class="form-label">Satuan</label>
                        <input type="search" id="new-product-unit" name="unit" list="product-unit-options" class="form-control" placeholder="Ketik atau cari satuan..." autocomplete="off" value="{{ old('unit', 'pcs') }}" required>
                        <datalist id="product-unit-options">
                            @foreach($units as $unit)
                                <option value="{{ $unit }}"></option>
                            @endforeach
                            @foreach(['pcs', 'kg', 'gram', 'liter', 'ml', 'box', 'pak', 'bungkus', 'botol', 'sak', 'lusin'] as $unit)
                                <option value="{{ $unit }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Harga Modal (Rp)</label>
                            <input type="text" name="purchase_price" inputmode="decimal" data-number-format="id" data-number-decimals="2" data-number-min="0" class="form-control price-input product-purchase-price text-end" placeholder="Contoh: 1.090.000" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input type="text" name="selling_price" inputmode="decimal" data-number-format="id" data-number-decimals="2" data-number-min="0" class="form-control price-input product-selling-price text-end" placeholder="Contoh: 1.090.000" required>
                        </div>
                    </div>
                    <div class="product-price-feedback form-text" aria-live="polite"></div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Stok Awal</label>
                            <input type="text" name="stock" inputmode="numeric" data-number-format="id" data-number-min="0" class="form-control text-end" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Min Stok</label>
                            <input type="text" name="min_stock" inputmode="numeric" data-number-format="id" data-number-min="0" class="form-control text-end" value="5" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="new-product-image" class="form-label">Gambar Produk (JPG/PNG)</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="border rounded-3 bg-body-tertiary d-flex align-items-center justify-content-center overflow-hidden flex-shrink-0" style="width: 88px; height: 88px;" data-image-preview>
                                <img src="" alt="Preview gambar produk" class="w-100 h-100 object-fit-contain p-1" data-image-preview-image hidden>
                                <span class="text-muted" data-image-preview-placeholder><i class="bi bi-image fs-3" aria-hidden="true"></i></span>
                            </div>
                            <input type="file" id="new-product-image" name="image" class="form-control" accept="image/jpeg,image/png" data-image-preview-input>
                        </div>
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
        const productsTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            orderMulti: false,
            lengthChange: false,
            ajax: @json(route('products.index')),
            pageLength: 10,
            order: [],
            dom: 'rtip',
            columns: [
                { data: 'DT_RowIndex', name: 'products.id', orderable: false, searchable: false },
                { data: 'image_display', name: 'image', orderable: false, searchable: false },
                { data: 'sku', name: 'products.sku', orderable: true },
                { data: 'barcode', name: 'products.barcode', orderable: true },
                { data: 'name', name: 'products.name', orderable: true },
                { data: 'category_name', name: 'category_name', orderable: true },
                { data: 'selling_price', name: 'products.selling_price', orderable: true, searchable: false },
                { data: 'stock_display', name: 'products.stock', orderable: true, searchable: false },
                { data: 'status_display', name: 'products.is_active', orderable: true, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ produk',
                infoEmpty: 'Tidak ada produk',
                infoFiltered: '(disaring dari _MAX_ produk)',
                zeroRecords: 'Produk tidak ditemukan',
                emptyTable: 'Belum ada data produk',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });

        let searchTimeout;
        document.getElementById('products-search').addEventListener('input', function() {
            window.clearTimeout(searchTimeout);
            const searchTerm = this.value;
            searchTimeout = window.setTimeout(function() {
                productsTable.search(searchTerm).draw();
            }, 150);
        });
    });
</script>
@endpush
