@extends('layouts.app')
@section('title', 'Catat Stok Masuk')
@section('content')
    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf
        <div class="card mb-3">
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="note" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                Item Barang
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()"><i class="bi bi-plus-lg"></i> Tambah Baris</button>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" id="itemsTable">
                    <thead><tr><th>Produk</th><th style="width: 100px;">Qty</th><th style="width: 150px;">Harga Beli</th><th class="amount" style="width: 150px;">Subtotal</th><th style="width: 50px;"></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-primary w-100 py-2"><i class="bi bi-save me-1"></i>Simpan & Tambah Stok</button>
    </form>

    <template id="rowTemplate">
        <tr>
            <td>
                <select name="items[__INDEX__][product_id]" class="form-select form-select-sm" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p) <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}">{{ $p->name }} ({{ $p->sku }})</option> @endforeach
                </select>
            </td>
            <td><input type="number" name="items[__INDEX__][quantity]" class="form-control form-control-sm qty" min="1" value="1" oninput="calcRow(this)" required></td>
            <td><input type="number" name="items[__INDEX__][purchase_price]" class="form-control form-control-sm price" min="0" value="0" oninput="calcRow(this)" required></td>
            <td class="amount subtotal fw-medium">Rp 0</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x"></i></button></td>
        </tr>
    </template>

    @push('scripts')
    <script>
        let idx = 0;
        function addRow() {
            const html = document.getElementById('rowTemplate').innerHTML.replace(/__INDEX__/g, idx++);
            document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', html);
        }
        function calcRow(el) {
            const tr = el.closest('tr');
            const qty = parseFloat(tr.querySelector('.qty').value) || 0;
            const price = parseFloat(tr.querySelector('.price').value) || 0;
            tr.querySelector('.subtotal').innerText = 'Rp ' + (qty * price).toLocaleString('id-ID');
        }
        document.addEventListener('change', function(e) {
            if (e.target.tagName === 'SELECT' && e.target.name.includes('[product_id]')) {
                const price = e.target.selectedOptions[0].dataset.price || 0;
                const tr = e.target.closest('tr');
                tr.querySelector('.price').value = price;
                calcRow(tr.querySelector('.price'));
            }
        });
        addRow();
    </script>
    @endpush
@endsection