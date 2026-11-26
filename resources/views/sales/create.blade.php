@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('sales.store') }}" method="POST" id="saleForm">
    @csrf
    <input type="hidden" name="cart_data" id="cartData" value="">

    <div class="row">
        <!-- KIRI: PRODUK -->
        <div class="col-md-7">
            <div class="input-group mb-3">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari produk...">
            </div>
            
            <div class="row g-2" id="productList">
                @foreach($products as $product)
                    <div class="col-6 col-md-4 product-item" data-name="{{ strtolower($product->name) }}">
                        <div class="card h-100 border shadow-sm" style="cursor: pointer;" onclick="tambahItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->stock }})">
                            <div class="card-body p-2 text-center d-flex flex-column justify-content-between">
                                
                                {{-- KOTAK GAMBAR BARU (LEBIH GEDE & RAPI) --}}
                                <div class="mb-2 d-flex align-items-center justify-content-center bg-light rounded" style="height: 90px; padding: 10px; width: 100%;">
                                    @if($product->image)
                                        @if(filter_var($product->image, FILTER_VALIDATE_URL))
                                            <img src="{{ $product->image }}" class="img-fluid" style="max-height: 100%; object-fit: contain;" alt="{{ $product->name }}">
                                        @else
                                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" style="max-height: 100%; object-fit: contain;" alt="{{ $product->name }}">
                                        @endif
                                    @else
                                        <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                    @endif
                                </div>
                                
                                {{-- INFORMASI PRODUK --}}
                                <div>
                                    <div class="fw-bold small text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
                                    <div class="text-primary fw-bold small">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Stok: {{ $product->stock }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KANAN: KERANJANG -->
        <div class="col-md-5">
            <div class="card border shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-cart"></i> Keranjang Belanja
                </div>
                <div class="card-body" id="cartBody" style="max-height: 350px; overflow-y: auto;">
                    <p class="text-muted text-center my-4" id="emptyText">Belum ada barang</p>
                    <table class="table table-sm" id="cartTable" style="display: none;">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th style="width: 76px;">Qty</th>
                                <th style="width: 170px;">Diskon</th>
                                <th>Subtotal</th>
                                <th style="width: 42px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total:</span>
                        <span id="totalText">Rp 0</span>
                    </div>

                    <div class="mb-2">
                        <label class="small fw-bold">Pelanggan</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">Pelanggan Umum</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="small fw-bold">Metode Bayar</label>
                        <select name="payment_method" id="paymentMethod" class="form-select form-select-sm">
                            <option value="tunai" @selected(\App\Models\Setting::get('default_payment_method', 'tunai') === 'tunai')>Tunai</option>
                            <option value="qris" @selected(\App\Models\Setting::get('default_payment_method', 'tunai') === 'qris')>QRIS</option>
                            <option value="transfer" @selected(\App\Models\Setting::get('default_payment_method', 'tunai') === 'transfer')>Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="saleNote" class="small fw-bold">Catatan</label>
                        <textarea name="note" id="saleNote" class="form-control form-control-sm" rows="2" maxlength="255" placeholder="Catatan transaksi (opsional)">{{ old('note') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Uang Diterima (Rp)</label>
                        <input type="text" name="paid_amount" id="paidAmount" inputmode="numeric" data-number-format="id" data-number-min="0" class="form-control form-control-sm text-end fw-semibold" required placeholder="Contoh: 1.090.000">
                        
                        <div class="d-flex gap-1 mt-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setUang(50000)">50rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setUang(100000)">100rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setUang(200000)">200rb</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setUangPas()">Uang Pas</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold" id="btnSubmit" data-requires-cart-items="true" disabled>
                        <i class="bi bi-save"></i> Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let totalAmount = 0;
    let itemCounter = 0;

    function tambahItem(id, name, price, maxStock) {
        let existingRow = document.getElementById('row-' + id);
        let qtyInput;
        
        if (existingRow) {
            qtyInput = existingRow.querySelector('.qty-input');
            let currentQty = window.PosNumberFormat.parseLocalizedValue(qtyInput.value);
            
            if (currentQty >= maxStock) {
                alert('Stok ' + name + ' tidak cukup!');
                return;
            }
            window.PosNumberFormat.setValue(qtyInput, currentQty + 1);
            updateSubtotal(id, price);
        } else {
            document.getElementById('emptyText').style.display = 'none';
            document.getElementById('cartTable').style.display = 'table';
            
            let tbody = document.getElementById('cartTableBody');
            let tr = document.createElement('tr');
            tr.id = 'row-' + id;
            
            tr.innerHTML = `
                <td>
                    ${name}
                    <input type="hidden" name="items[${itemCounter}][product_id]" value="${id}">
                </td>
                <td>
                    <input type="text" name="items[${itemCounter}][quantity]" inputmode="numeric" data-number-format="id" data-number-min="1" data-number-max="${maxStock}" class="form-control form-control-sm qty-input text-end" value="1" oninput="updateSubtotal(${id}, ${price})">
                </td>
                <td>
                    <select name="items[${itemCounter}][discount_type]" class="form-select form-select-sm discount-type" onchange="updateSubtotal(${id}, ${price})">
                        <option value="amount">Nominal (Rp)</option>
                        <option value="percent">Persentase (%)</option>
                    </select>
                    <input type="text" name="items[${itemCounter}][discount_value]" inputmode="decimal" data-number-format="id" data-number-decimals="2" data-number-min="0" class="form-control form-control-sm discount-value text-end mt-1" value="0" aria-label="Nilai diskon ${name}" oninput="updateSubtotal(${id}, ${price})">
                    <small class="discount-preview text-danger"></small>
                </td>
                <td class="subtotal-text">Rp ${price.toLocaleString('id-ID')}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" aria-label="Hapus ${name}" onclick="hapusItem(${id})"><i class="bi bi-x-lg"></i></button>
                </td>
            `;
            
            tbody.appendChild(tr);
            itemCounter++;
            updateSubtotal(id, price);
        }
        
        updateSubmitButton();
    }

    function updateSubtotal(id, price) {
        let row = document.getElementById('row-' + id);
        let qtyInput = row.querySelector('.qty-input');
        let discountType = row.querySelector('.discount-type').value;
        let discountInput = row.querySelector('.discount-value');

        const qty = window.PosNumberFormat.parseLocalizedValue(qtyInput.value) ?? 0;
        const discountValue = window.PosNumberFormat.parseLocalizedValue(discountInput.value) ?? 0;
        const grossSubtotal = price * qty;
        const discountAmount = discountType === 'percent'
            ? grossSubtotal * discountValue / 100
            : discountValue;
        const subtotal = Math.max(0, grossSubtotal - discountAmount);
        const isInvalidDiscount = discountType === 'percent'
            ? discountValue > 100
            : discountValue > grossSubtotal;

        discountInput.setCustomValidity(isInvalidDiscount
            ? discountType === 'percent'
                ? 'Diskon persentase maksimal 100%.'
                : 'Diskon nominal tidak boleh melebihi subtotal produk.'
            : '');
        row.querySelector('.discount-preview').innerText = discountAmount > 0
            ? '-Rp ' + discountAmount.toLocaleString('id-ID')
            : '';
        row.querySelector('.subtotal-text').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        row.querySelector('.subtotal-text').dataset.amount = String(subtotal);
        hitungTotal();
    }

    function hapusItem(id) {
        let row = document.getElementById('row-' + id);
        row.remove();
        
        if (document.getElementById('cartTableBody').children.length === 0) {
            document.getElementById('emptyText').style.display = 'block';
            document.getElementById('cartTable').style.display = 'none';
        }
        hitungTotal();
    }

    function hitungTotal() {
        totalAmount = 0;
        let subtotals = document.querySelectorAll('.subtotal-text');

        subtotals.forEach(el => {
            totalAmount += Number(el.dataset.amount) || 0;
        });
        
        document.getElementById('totalText').innerText = 'Rp ' + totalAmount.toLocaleString('id-ID');
        updateSubmitButton();
    }

    function updateSubmitButton() {
        const saleForm = document.getElementById('saleForm');
        const hasCartItems = document.querySelector('#cartTableBody tr') !== null;
        document.getElementById('btnSubmit').disabled = !hasCartItems || !saleForm.checkValidity();
    }

    function setUang(nominal) {
        window.PosNumberFormat.setValue(document.getElementById('paidAmount'), nominal);
        updateSubmitButton();
    }

    function setUangPas() {
        window.PosNumberFormat.setValue(document.getElementById('paidAmount'), totalAmount);
        updateSubmitButton();
    }

    document.getElementById('searchInput').addEventListener('input', function(e) {
        let term = e.target.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(el => {
            let name = el.getAttribute('data-name');
            el.style.display = name.includes(term) ? 'block' : 'none';
        });

    });

    document.getElementById('paidAmount').addEventListener('input', updateSubmitButton);

    document.getElementById('saleForm').addEventListener('submit', function(event) {
        let cart = [];
        document.querySelectorAll('#cartTableBody tr').forEach(function(row) {
            let productId = parseInt(row.id.replace('row-', ''), 10);
            let quantity = window.PosNumberFormat.parseLocalizedValue(row.querySelector('.qty-input').value);
            if (productId && quantity > 0) {
                cart.push({ id: productId, qty: quantity });
            }
        });

        if (cart.length === 0) {
            event.preventDefault();
            alert('Keranjang belanja masih kosong.');
            return;
        }

        document.getElementById('cartData').value = JSON.stringify(cart);
    });
</script>
@endpush