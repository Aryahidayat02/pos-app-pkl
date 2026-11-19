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
        <div class="table-responsive">
            <table class="table table-hover table-datatable align-middle mb-0">
                <thead class="table-light">
                    <tr>
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
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->barcode ?? '-' }}</td>
                            <td class="fw-bold">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>Rp {{ number_format($product->selling_price, 2, ',', '.') }}</td>
                            <td>
                                @if($product->stock <= $product->min_stock)
                                    <span class="text-danger fw-bold">{{ $product->stock }} {{ $product->unit }}</span>
                                @else
                                    {{ $product->stock }} {{ $product->unit }}
                                @endif
                            </td>
                            <td>
                                @if($product->is_active == 1)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(auth()->user()->role === 'admin')
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $product->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($product->saleItems()->count() > 0 || $product->purchaseItems()->count() > 0)
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak bisa dihapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" data-delete-name="{{ $product->name }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>

                        <!-- MODAL EDIT PRODUK -->
                        <div class="modal fade" id="modalEdit{{ $product->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Produk: {{ $product->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Kategori</label>
                                                {{-- CLASS SELECT2 --}}
                                                <select name="category_id" class="form-select select2" required>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">SKU</label>
                                                <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Barcode (Opsional)</label>
                                                <input type="text" name="barcode" class="form-control" value="{{ $product->barcode }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Nama Produk</label>
                                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Satuan</label>
                                                <select name="unit" class="form-select" required>
                                                    @foreach(['pcs', 'kg', 'box', 'bungkus', 'botol', 'sak'] as $unit)
                                                        <option value="{{ $unit }}" {{ $product->unit == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Harga Modal</label>
                                                    <input type="number" name="purchase_price" step="0.01" class="form-control price-input" value="{{ number_format($product->purchase_price, 2, '.', '') }}" required>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Harga Jual</label>
                                                    <input type="number" name="selling_price" step="0.01" class="form-control price-input" value="{{ number_format($product->selling_price, 2, '.', '') }}" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Min Stok</label>
                                                    <input type="number" name="min_stock" class="form-control" value="{{ $product->min_stock }}" required>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Gambar</label>
                                                    <input type="file" name="image" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label">Produk Aktif</label>
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
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PRODUK -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="category-search" class="form-label">Cari Kategori</label>
                        <div class="position-relative">
                            <input type="hidden" name="category_id" id="category-id">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="search" id="category-search" class="form-control" placeholder="Ketik nama kategori..." autocomplete="off" required aria-controls="category-search-results" aria-expanded="false">
                            </div>
                            <div id="category-search-results" class="list-group position-absolute w-100 shadow" style="z-index: 1060;" hidden>
                                @foreach($categories as $cat)
                                    <button type="button" class="list-group-item list-group-item-action text-start" data-category-id="{{ $cat->id }}" data-category-name="{{ $cat->name }}">{{ $cat->name }}</button>
                                @endforeach
                                <div class="list-group-item text-muted" data-no-category-results hidden>Kategori tidak ditemukan.</div>
                            </div>
                        </div>
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
                        <label class="form-label">Satuan</label>
                        <select name="unit" class="form-select" required>
                            <option value="pcs">pcs</option>
                            <option value="kg">kg</option>
                            <option value="box">box</option>
                            <option value="bungkus">bungkus</option>
                            <option value="botol">botol</option>
                            <option value="sak">sak</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Harga Modal</label>
                            <input type="number" name="purchase_price" step="0.01" class="form-control price-input" placeholder="0.00" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Harga Jual</label>
                            <input type="number" name="selling_price" step="0.01" class="form-control price-input" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Min Stok</label>
                            <input type="number" name="min_stock" class="form-control" value="5" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Gambar</label>
                        <input type="file" name="image" class="form-control">
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

@push('scripts')
<script>
    // Format input harga otomatis jadi 2 desimal (pas blur)
    document.addEventListener('DOMContentLoaded', function() {
        const priceInputs = document.querySelectorAll('.price-input');
        priceInputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseFloat(this.value).toFixed(2);
                }
            });
        });

        const categorySearch = document.getElementById('category-search');
        const categoryId = document.getElementById('category-id');
        const categoryResults = document.getElementById('category-search-results');

        if (categorySearch && categoryId && categoryResults) {
            const categoryOptions = Array.from(categoryResults.querySelectorAll('[data-category-id]'));
            const noCategoryResults = categoryResults.querySelector('[data-no-category-results]');
            const categoryNotSelectedMessage = 'Pilih kategori dari hasil pencarian.';

            function filterCategories() {
                const searchTerm = categorySearch.value.trim().toLocaleLowerCase('id');
                let visibleCategories = 0;

                categoryOptions.forEach(option => {
                    const categoryName = option.dataset.categoryName.toLocaleLowerCase('id');
                    option.hidden = !categoryName.includes(searchTerm);
                    if (!option.hidden) {
                        visibleCategories++;
                    }
                });

                noCategoryResults.hidden = visibleCategories > 0;
                categoryResults.hidden = false;
                categorySearch.setAttribute('aria-expanded', 'true');
            }

            categorySearch.addEventListener('focus', filterCategories);
            categorySearch.addEventListener('input', function() {
                categoryId.value = '';
                categorySearch.setCustomValidity(categoryNotSelectedMessage);
                filterCategories();
            });

            categoryOptions.forEach(option => {
                option.addEventListener('click', function() {
                    categoryId.value = option.dataset.categoryId;
                    categorySearch.value = option.dataset.categoryName;
                    categorySearch.setCustomValidity('');
                    categoryResults.hidden = true;
                    categorySearch.setAttribute('aria-expanded', 'false');
                });
            });

            document.addEventListener('click', function(event) {
                if (!categoryResults.contains(event.target) && !categorySearch.contains(event.target)) {
                    categoryResults.hidden = true;
                    categorySearch.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>
@endpush