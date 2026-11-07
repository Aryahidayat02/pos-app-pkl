@extends('layouts.app')
@section('title', 'Catat Stok Masuk')

@section('content')
<form action="{{ route('purchases.store') }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Form Stok Masuk</h5>
        </div>
        <div class="card-body">
            <!-- Info Header -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Catatan (Opsional)</label>
                    <input type="text" name="note" class="form-control" value="{{ old('note') }}" placeholder="Misal: Restok bulanan">
                </div>
            </div>

            <!-- Tabel Item Dinamis -->
            <table class="table table-bordered" id="itemTable">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th width="150">Jumlah (Qty)</th>
                        <th width="200">Harga Beli Satuan (Rp)</th>
                        <th>Subtotal</th>
                        <th width="50">Aksi</th>
                    </tr>
                </thead>
                <tbody id="itemBody">
                    <!-- Baris Pertama -->
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" class="form-select product-select" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                        {{ $product->name }} (Stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" class="form-control qty-input" value="1" min="1" required></td>
                        <td><input type="number" name="items[0][purchase_price]" class="form-control price-input" value="0" min="0" required></td>
                        <td class="subtotal-text align-middle fw-bold">Rp 0</td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addRow">
                <i class="bi bi-plus-lg"></i> Tambah Baris
            </button>

            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                <h5 class="fw-bold mb-0">Total Belanja: <span id="grandTotal" class="text-primary">Rp 0</span></h5>
                <div>
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Stok Masuk</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let rowIndex = 1;

    // Tambah Baris Baru
    document.getElementById('addRow').addEventListener('click', function() {
        let newRow = `
            <tr class="item-row">
                <td>
                    <select name="items[${rowIndex}][product_id]" class="form-select product-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                {{ $product->name }} (Stok: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="1" min="1" required></td>
                <td><input type="number" name="items[${rowIndex}][purchase_price]" class="form-control price-input" value="0" min="0" required></td>
                <td class="subtotal-text align-middle fw-bold">Rp 0</td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
        document.getElementById('itemBody').insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
        updateRemoveButtons();
    });

    // Hapus Baris
    document.getElementById('itemBody').addEventListener('click', function(e) {
        if(e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            hitungGrandTotal();
            updateRemoveButtons();
        }
    });

    // Hanya izinkan hapus kalau baris > 1
    function updateRemoveButtons() {
        let rows = document.querySelectorAll('.item-row');
        let buttons = document.querySelectorAll('.remove-row');
        buttons.forEach(btn => {
            btn.disabled = rows.length === 1;
        });
    }

    // Auto-fill Harga saat pilih produk
    document.getElementById('itemBody').addEventListener('change', function(e) {
        if(e.target.classList.contains('product-select')) {
            let selectedOption = e.target.options[e.target.selectedIndex];
            let price = selectedOption.getAttribute('data-price') || 0;
            let row = e.target.closest('tr');
            row.querySelector('.price-input').value = price;
            hitungSubtotal(row);
        }
    });

    // Hitung Subtotal tiap baris
    document.getElementById('itemBody').addEventListener('input', function(e) {
        if(e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            hitungSubtotal(e.target.closest('tr'));
        }
    });

    function hitungSubtotal(row) {
        let qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        let price = parseFloat(row.querySelector('.price-input').value) || 0;
        let subtotal = qty * price;
        row.querySelector('.subtotal-text').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        hitungGrandTotal();
    }

    function hitungGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            let qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            let price = parseFloat(row.querySelector('.price-input').value) || 0;
            total += (qty * price);
        });
        document.getElementById('grandTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endpush