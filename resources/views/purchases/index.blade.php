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
        <table id="purchases-table" class="table table-hover mb-0">
            <thead><tr><th>#</th><th>No. Dokumen</th><th>Tanggal</th><th>Supplier</th><th class="amount">Total Belanja</th><th>Petugas</th><th class="text-center">Aksi</th></tr></thead>
            <tbody></tbody>
        </table>
    </div></div>

    @php($purchaseItems = old('items', [['product_id' => '', 'quantity' => 1, 'purchase_price' => 0]]))
    @php($selectedSupplier = $suppliers->firstWhere('id', old('supplier_id')))
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
                                <label for="stockInSupplierSearch" class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                                <div class="position-relative" data-stock-search-wrapper>
                                    <input type="hidden" name="supplier_id" value="{{ $selectedSupplier?->id }}">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="search" id="stockInSupplierSearch" class="form-control stock-search-input" value="{{ $selectedSupplier?->name }}" placeholder="Ketik nama supplier..." autocomplete="off" required role="combobox" aria-autocomplete="list" aria-controls="stockInSearchResults" aria-expanded="false" data-search-kind="supplier">
                                    </div>
                                </div>
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
                                        <th style="width: 190px;">Harga Beli Satuan (Rp)</th>
                                        <th style="width: 170px;">Subtotal</th>
                                        <th style="width: 60px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="stockInItems" data-next-index="{{ ($purchaseItems ? max(array_keys($purchaseItems)) : -1) + 1 }}">
                                    @foreach($purchaseItems as $index => $item)
                                        @php($selectedProduct = $products->firstWhere('id', old('items.'.$index.'.product_id', $item['product_id'] ?? '')))
                                        <tr class="stock-item-row">
                                            <td>
                                                <div class="position-relative" data-stock-search-wrapper>
                                                    <input type="hidden" class="stock-product-id" name="items[{{ $index }}][product_id]" value="{{ $selectedProduct?->id }}">
                                                    <input type="search" class="form-control stock-search-input" value="{{ $selectedProduct ? $selectedProduct->name.' ('.$selectedProduct->sku.') — Stok: '.$selectedProduct->stock : '' }}" placeholder="Ketik nama atau SKU produk..." autocomplete="off" required role="combobox" aria-autocomplete="list" aria-controls="stockInSearchResults" aria-expanded="false" data-search-kind="product" aria-label="Cari produk untuk stok masuk">
                                                </div>
                                            </td>
                                            <td><input type="text" name="items[{{ $index }}][quantity]" inputmode="numeric" data-number-format="id" data-number-min="1" class="form-control stock-quantity text-end" value="{{ old('items.'.$index.'.quantity', $item['quantity'] ?? 1) }}" required></td>
                                            <td><input type="text" name="items[{{ $index }}][purchase_price]" inputmode="decimal" data-number-format="id" data-number-decimals="2" data-number-min="0" class="form-control stock-price text-end" value="{{ number_format((float) old('items.'.$index.'.purchase_price', $item['purchase_price'] ?? 0), 2, ',', '.') }}" required></td>
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
                                    <div class="position-relative" data-stock-search-wrapper>
                                        <input type="hidden" class="stock-product-id" name="items[__INDEX__][product_id]">
                                        <input type="search" class="form-control stock-search-input" placeholder="Ketik nama atau SKU produk..." autocomplete="off" required role="combobox" aria-autocomplete="list" aria-controls="stockInSearchResults" aria-expanded="false" data-search-kind="product" aria-label="Cari produk untuk stok masuk">
                                    </div>
                                </td>
                                <td><input type="text" name="items[__INDEX__][quantity]" inputmode="numeric" data-number-format="id" data-number-min="1" class="form-control stock-quantity text-end" value="1" required></td>
                                <td><input type="text" name="items[__INDEX__][purchase_price]" inputmode="decimal" data-number-format="id" data-number-decimals="2" data-number-min="0" class="form-control stock-price text-end" value="0" required></td>
                                <td class="stock-subtotal fw-semibold">Rp 0</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-stock-row" aria-label="Hapus produk">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <div id="stockInSearchCatalog" hidden>
                            @foreach($suppliers as $supplier)
                                <div data-stock-search-option data-search-kind="supplier" data-option-id="{{ $supplier->id }}" data-label="{{ $supplier->name }}" data-search-text="{{ $supplier->name }}"></div>
                            @endforeach
                            @foreach($products as $product)
                                <div data-stock-search-option data-search-kind="product" data-option-id="{{ $product->id }}" data-label="{{ $product->name }} ({{ $product->sku }}) — Stok: {{ $product->stock }}" data-search-text="{{ $product->name }} {{ $product->sku }} {{ $product->stock }}" data-price="{{ $product->purchase_price }}"></div>
                            @endforeach
                        </div>
                        <div id="stockInSearchResults" class="list-group shadow" role="listbox" hidden style="position: fixed; z-index: 1080; max-height: 240px; overflow-y: auto;"></div>
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
    $(function() {
        $('#purchases-table').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 300,
            ajax: @json(route('purchases.index')),
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[2, 'desc']],
            columns: [
                { data: 'DT_RowIndex', name: 'purchases.id', orderable: false, searchable: false },
                { data: 'purchase_number', name: 'purchases.purchase_number' },
                { data: 'purchase_date', name: 'purchases.purchase_date' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'total_amount', name: 'purchases.total_amount', searchable: false },
                { data: 'staff_name', name: 'staff_name' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ stok masuk',
                infoEmpty: 'Tidak ada stok masuk',
                infoFiltered: '(disaring dari _MAX_ stok masuk)',
                zeroRecords: 'Stok masuk tidak ditemukan',
                emptyTable: 'Belum ada riwayat stok masuk',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });
    });

    const stockInModal = document.getElementById('stockInModal');
    const stockInItems = document.getElementById('stockInItems');
    const stockInRowTemplate = document.getElementById('stockInRowTemplate');
    const stockInSearchCatalog = Array.from(document.querySelectorAll('#stockInSearchCatalog [data-stock-search-option]'));
    const stockInSearchResults = document.getElementById('stockInSearchResults');
    let stockInRowIndex = Number(stockInItems.dataset.nextIndex);
    let activeStockSearchInput = null;

    document.body.append(stockInSearchResults);

    function closeStockSearch() {
        if (activeStockSearchInput) {
            activeStockSearchInput.setAttribute('aria-expanded', 'false');
        }
        activeStockSearchInput = null;
        stockInSearchResults.hidden = true;
        stockInSearchResults.style.display = 'none';
    }

    function positionStockSearchResults() {
        if (!activeStockSearchInput || stockInSearchResults.hidden) {
            return;
        }

        const inputBounds = activeStockSearchInput.getBoundingClientRect();
        const spaceBelow = window.innerHeight - inputBounds.bottom - 8;
        const spaceAbove = inputBounds.top - 8;
        const openAbove = spaceBelow < 160 && spaceAbove > spaceBelow;
        const maxHeight = Math.max(0, Math.min(240, openAbove ? spaceAbove : spaceBelow));

        stockInSearchResults.style.left = `${Math.max(8, Math.min(inputBounds.left, window.innerWidth - inputBounds.width - 8))}px`;
        stockInSearchResults.style.width = `${inputBounds.width}px`;
        stockInSearchResults.style.maxHeight = `${maxHeight}px`;
        stockInSearchResults.style.top = openAbove
            ? `${Math.max(8, inputBounds.top - maxHeight - 4)}px`
            : `${inputBounds.bottom + 4}px`;
    }

    function showStockSearchResults(input) {
        if (activeStockSearchInput && activeStockSearchInput !== input) {
            activeStockSearchInput.setAttribute('aria-expanded', 'false');
        }
        activeStockSearchInput = input;
        input.setAttribute('aria-expanded', 'true');
        stockInSearchResults.replaceChildren();

        const searchTerm = input.value.trim().toLocaleLowerCase('id');
        const matchingOptions = stockInSearchCatalog.filter(option => {
            return option.dataset.searchKind === input.dataset.searchKind
                && option.dataset.searchText.toLocaleLowerCase('id').includes(searchTerm);
        });

        if (matchingOptions.length === 0) {
            const emptyMessage = document.createElement('div');
            emptyMessage.className = 'list-group-item text-muted';
            emptyMessage.textContent = input.dataset.searchKind === 'supplier'
                ? 'Supplier tidak ditemukan.'
                : 'Produk tidak ditemukan.';
            stockInSearchResults.append(emptyMessage);
        } else {
            matchingOptions.forEach(option => {
                const result = document.createElement('button');
                result.type = 'button';
                result.className = 'list-group-item list-group-item-action text-start';
                result.setAttribute('role', 'option');
                result.setAttribute('aria-selected', 'false');
                result.dataset.optionId = option.dataset.optionId;
                result.dataset.label = option.dataset.label;
                result.dataset.price = option.dataset.price || '';
                result.textContent = option.dataset.label;
                stockInSearchResults.append(result);
            });
        }

        stockInSearchResults.hidden = false;
        stockInSearchResults.style.display = 'block';
        positionStockSearchResults();
    }

    function selectStockSearchResult(result) {
        if (!activeStockSearchInput) {
            return;
        }

        const wrapper = activeStockSearchInput.closest('[data-stock-search-wrapper]');
        wrapper.querySelector('input[type="hidden"]').value = result.dataset.optionId;
        activeStockSearchInput.value = result.dataset.label;
        activeStockSearchInput.setCustomValidity('');
        activeStockSearchInput.dispatchEvent(new Event('change', { bubbles: true }));

        if (activeStockSearchInput.dataset.searchKind === 'product') {
            const row = activeStockSearchInput.closest('.stock-item-row');
            window.PosNumberFormat.setValue(row.querySelector('.stock-price'), result.dataset.price || 0);
            updateStockInRow(row);
        }

        closeStockSearch();
    }

    function updateStockInRow(row) {
        const quantity = window.PosNumberFormat.parseLocalizedValue(row.querySelector('.stock-quantity').value) || 0;
        const price = window.PosNumberFormat.parseLocalizedValue(row.querySelector('.stock-price').value) || 0;
        row.querySelector('.stock-subtotal').textContent = 'Rp ' + (quantity * price).toLocaleString('id-ID');
        updateStockInTotal();
    }

    function updateStockInTotal() {
        let total = 0;
        stockInItems.querySelectorAll('.stock-item-row').forEach(row => {
            const quantity = window.PosNumberFormat.parseLocalizedValue(row.querySelector('.stock-quantity').value) || 0;
            const price = window.PosNumberFormat.parseLocalizedValue(row.querySelector('.stock-price').value) || 0;
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

    stockInModal.addEventListener('focusin', function(event) {
        if (event.target.matches('.stock-search-input')) {
            showStockSearchResults(event.target);
        }
    });

    stockInModal.addEventListener('click', function(event) {
        if (event.target.matches('.stock-search-input')) {
            showStockSearchResults(event.target);
        }
    });

    stockInModal.addEventListener('input', function(event) {
        if (!event.target.matches('.stock-search-input')) {
            return;
        }

        const input = event.target;
        const wrapper = input.closest('[data-stock-search-wrapper]');
        wrapper.querySelector('input[type="hidden"]').value = '';
        input.setCustomValidity(input.value.trim()
            ? `Pilih ${input.dataset.searchKind === 'supplier' ? 'supplier' : 'produk'} dari hasil pencarian.`
            : '');

        if (input.dataset.searchKind === 'product') {
            const row = input.closest('.stock-item-row');
            row.querySelector('.stock-price').value = 0;
            updateStockInRow(row);
        }

        showStockSearchResults(input);
    });

    stockInSearchResults.addEventListener('mousedown', function(event) {
        event.preventDefault();
    });

    stockInSearchResults.addEventListener('click', function(event) {
        const result = event.target.closest('button[data-option-id]');
        if (result) {
            selectStockSearchResult(result);
        }
    });

    stockInModal.addEventListener('keydown', function(event) {
        if (!event.target.matches('.stock-search-input')) {
            return;
        }

        if (event.key === 'Escape') {
            closeStockSearch();
        } else if (event.key === 'ArrowDown' && !stockInSearchResults.hidden) {
            const firstResult = stockInSearchResults.querySelector('button[data-option-id]');
            if (firstResult) {
                event.preventDefault();
                firstResult.focus();
            }
        }
    });

    stockInSearchResults.addEventListener('keydown', function(event) {
        const results = Array.from(stockInSearchResults.querySelectorAll('button[data-option-id]'));
        const currentIndex = results.indexOf(event.target);

        if (event.key === 'Escape') {
            const searchInput = activeStockSearchInput;
            closeStockSearch();
            searchInput?.focus();
            closeStockSearch();
        } else if (event.key === 'ArrowDown' && currentIndex < results.length - 1) {
            event.preventDefault();
            results[currentIndex + 1].focus();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (currentIndex > 0) {
                results[currentIndex - 1].focus();
            } else {
                activeStockSearchInput?.focus();
            }
        }
    });

    document.addEventListener('focusin', function(event) {
        if (activeStockSearchInput
            && event.target !== activeStockSearchInput
            && !stockInSearchResults.contains(event.target)) {
            closeStockSearch();
        }
    });

    document.addEventListener('click', function(event) {
        if (activeStockSearchInput
            && !activeStockSearchInput.contains(event.target)
            && !stockInSearchResults.contains(event.target)) {
            closeStockSearch();
        }
    });

    window.addEventListener('resize', positionStockSearchResults);
    stockInModal.querySelector('.modal-body').addEventListener('scroll', positionStockSearchResults);
    stockInModal.addEventListener('hidden.bs.modal', closeStockSearch);

    document.getElementById('addStockInRow').addEventListener('click', function() {
        const rowHtml = stockInRowTemplate.innerHTML.replace(/__INDEX__/g, stockInRowIndex++);
        stockInItems.insertAdjacentHTML('beforeend', rowHtml);
        updateStockInRemoveButtons();
        updateStockInTotal();
    });

    stockInItems.addEventListener('click', function(event) {
        const removeButton = event.target.closest('.remove-stock-row');
        if (removeButton && stockInItems.querySelectorAll('.stock-item-row').length > 1) {
            if (activeStockSearchInput && removeButton.closest('tr').contains(activeStockSearchInput)) {
                closeStockSearch();
            }
            removeButton.closest('tr').remove();
            updateStockInRemoveButtons();
            updateStockInTotal();
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