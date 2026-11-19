@extends('layouts.app')
@section('title', 'Stok Masuk')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0">Riwayat Stok Masuk</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#stockInModal">
            <i class="bi bi-plus-lg me-1"></i>Catat Stok Masuk
        </button>
    </div>
    <div class="card"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>No. Dokumen</th><th>Tanggal</th><th>Supplier</th><th class="amount">Total Belanja</th><th>Petugas</th><th style="width: 80px;" class="text-center">Aksi</th></tr></thead>
            <tbody>
                @forelse($purchases as $p)
                    <tr>
                        <td class="fw-medium">{{ $p->purchase_number }}</td>
                        <td>{{ $p->purchase_date->format('d/m/Y') }}</td>
                        <td>{{ $p->supplier->name }}</td>
                        <td class="amount">@rupiah($p->total_amount)</td>
                        <td>{{ $p->user->name }}</td>
                        <td class="text-center"><a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat stok masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $purchases->links() }}</div>

    @php($purchaseItems = old('items', [['product_id' => '', 'quantity' => 1, 'purchase_price' => 0]]))
    <div class="modal fade" id="stockInModal" tabindex="-1" aria-labelledby="stockInModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('purchases.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="stockInModalTitle">Catat Stok Masuk</h5>
                            <p class="text-secondary small mb-0">Pilih supplier dan produk yang baru diterima.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <div class="fw-semibold mb-1">Periksa kembali data stok masuk:</div>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="stockInSupplier" class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="stockInSupplier" class="form-select" required>
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="stockInDate" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" id="stockInDate" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="stockInNote" class="form-label fw-semibold">Catatan</label>
                                <input type="text" name="note" id="stockInNote" class="form-control" value="{{ old('note') }}" placeholder="Misal: Restok bulanan">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Daftar Produk</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addStockInRow">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Produk
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th style="width: 130px;">Jumlah</th>
                                        <th style="width: 190px;">Harga Beli Satuan</th>
                                        <th style="width: 170px;">Subtotal</th>
                                        <th style="width: 60px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="stockInItems" data-next-index="{{ ($purchaseItems ? max(array_keys($purchaseItems)) : -1) + 1 }}">
                                    @foreach($purchaseItems as $index => $item)
                                        <tr class="stock-item-row">
                                            <td>
                                                <select name="items[{{ $index }}][product_id]" class="form-select stock-product-select" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}" {{ old('items.'.$index.'.product_id', $item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }} ({{ $product->sku }}) — Stok: {{ $product->stock }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control stock-quantity" value="{{ old('items.'.$index.'.quantity', $item['quantity'] ?? 1) }}" min="1" required></td>
                                            <td><input type="number" name="items[{{ $index }}][purchase_price]" class="form-control stock-price" value="{{ old('items.'.$index.'.purchase_price', $item['purchase_price'] ?? 0) }}" min="0" step="0.01" required></td>
                                            <td class="stock-subtotal fw-semibold">Rp 0</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-stock-row" aria-label="Hapus produk">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <template id="stockInRowTemplate">
                            <tr class="stock-item-row">
                                <td>
                                    <select name="items[__INDEX__][product_id]" class="form-select stock-product-select" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                                {{ $product->name }} ({{ $product->sku }}) — Stok: {{ $product->stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="items[__INDEX__][quantity]" class="form-control stock-quantity" value="1" min="1" required></td>
                                <td><input type="number" name="items[__INDEX__][purchase_price]" class="form-control stock-price" value="0" min="0" step="0.01" required></td>
                                <td class="stock-subtotal fw-semibold">Rp 0</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-stock-row" aria-label="Hapus produk">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <h5 class="fw-bold mb-0">Total Belanja</h5>
                            <span id="stockInGrandTotal" class="fs-5 fw-bold text-primary">Rp 0</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bi bi-save me-1"></i>Simpan Stok Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const stockInModal = document.getElementById('stockInModal');
    const stockInItems = document.getElementById('stockInItems');
    const stockInRowTemplate = document.getElementById('stockInRowTemplate');
    let stockInRowIndex = Number(stockInItems.dataset.nextIndex);

    function initializeStockProductSelect(select) {
        $(select).select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(stockInModal)
        });
    }

    function initializeStockProductSelects() {
        stockInItems.querySelectorAll('.stock-product-select').forEach(select => {
            if (!$(select).hasClass('select2-hidden-accessible')) {
                initializeStockProductSelect(select);
            }
        });
    }

    function updateStockInRow(row) {
        const quantity = parseFloat(row.querySelector('.stock-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.stock-price').value) || 0;
        row.querySelector('.stock-subtotal').textContent = 'Rp ' + (quantity * price).toLocaleString('id-ID');
        updateStockInTotal();
    }

    function updateStockInTotal() {
        let total = 0;
        stockInItems.querySelectorAll('.stock-item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.stock-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.stock-price').value) || 0;
            total += quantity * price;
        });
        document.getElementById('stockInGrandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function updateStockInRemoveButtons() {
        const rows = stockInItems.querySelectorAll('.stock-item-row');
        stockInItems.querySelectorAll('.remove-stock-row').forEach(button => {
            button.disabled = rows.length === 1;
        });
    }

    stockInModal.addEventListener('shown.bs.modal', initializeStockProductSelects);

    document.getElementById('addStockInRow').addEventListener('click', function() {
        const rowHtml = stockInRowTemplate.innerHTML.replace(/__INDEX__/g, stockInRowIndex++);
        stockInItems.insertAdjacentHTML('beforeend', rowHtml);
        initializeStockProductSelects();
        updateStockInRemoveButtons();
        updateStockInTotal();
    });

    stockInItems.addEventListener('click', function(event) {
        const removeButton = event.target.closest('.remove-stock-row');
        if (removeButton && stockInItems.querySelectorAll('.stock-item-row').length > 1) {
            $(removeButton.closest('tr').querySelector('.stock-product-select')).select2('destroy');
            removeButton.closest('tr').remove();
            updateStockInRemoveButtons();
            updateStockInTotal();
        }
    });

    stockInItems.addEventListener('change', function(event) {
        if (event.target.matches('.stock-product-select')) {
            const selectedOption = event.target.selectedOptions[0];
            const row = event.target.closest('.stock-item-row');
            row.querySelector('.stock-price').value = selectedOption.dataset.price || 0;
            updateStockInRow(row);
        }
    });

    stockInItems.addEventListener('input', function(event) {
        if (event.target.matches('.stock-quantity, .stock-price')) {
            updateStockInRow(event.target.closest('.stock-item-row'));
        }
    });

    stockInItems.querySelectorAll('.stock-item-row').forEach(updateStockInRow);
    updateStockInRemoveButtons();

    @if($errors->any())
        bootstrap.Modal.getOrCreateInstance(stockInModal).show();
    @endif
</script>
@endpush