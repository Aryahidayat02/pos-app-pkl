@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<form action="{{ route('sales.store') }}" method="POST" id="saleForm">
    @csrf

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
                                <th width="80">Qty</th>
                                <th>Subtotal</th>
                                <th></th>
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
                            <option value="tunai">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Uang Diterima (Rp)</label>
                        <input type="number" name="paid_amount" id="paidAmount" class="form-control form-control-sm" required placeholder="0">
                        
                        <div class="d-flex gap-1 mt-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setUang(50000)">50rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setUang(100000)">100rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setUang(200000)">200rb</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setUangPas()">Uang Pas</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold" id="btnSubmit" disabled>
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
            let currentQty = parseInt(qtyInput.value);
            
            if (currentQty >= maxStock) {
                alert('Stok ' + name + ' tidak cukup!');
                return;
            }
            qtyInput.value = currentQty + 1;
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
                    <input type="number" name="items[${itemCounter}][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" max="${maxStock}" onchange="updateSubtotal(${id}, ${price}, this.value)">
                </td>
                <td class="subtotal-text">Rp ${price.toLocaleString('id-ID')}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(${id}, ${price})">x</button>
                </td>
            `;
            
            tbody.appendChild(tr);
            itemCounter++;
            updateSubtotal(id, price);
        }
        
        document.getElementById('btnSubmit').disabled = false;
    }

    function updateSubtotal(id, price, newQty = null) {
        let row = document.getElementById('row-' + id);
        let qtyInput = row.querySelector('.qty-input');
        
        if (newQty) {
            qtyInput.value = newQty;
        }
        
        let qty = parseInt(qtyInput.value);
        let subtotal = price * qty;
        
        row.querySelector('.subtotal-text').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        hitungTotal();
    }

    function hapusItem(id, price) {
        let row = document.getElementById('row-' + id);
        row.remove();
        
        if (document.getElementById('cartTableBody').children.length === 0) {
            document.getElementById('emptyText').style.display = 'block';
            document.getElementById('cartTable').style.display = 'none';
            document.getElementById('btnSubmit').disabled = true;
        }
        hitungTotal();
    }

    function hitungTotal() {
        totalAmount = 0;
        let subtotals = document.querySelectorAll('.subtotal-text');
        
        subtotals.forEach(el => {
            let text = el.innerText.replace('Rp ', '').replace(/\./g, '');
            totalAmount += parseInt(text);
        });
        
        document.getElementById('totalText').innerText = 'Rp ' + totalAmount.toLocaleString('id-ID');
    }

    function setUang(nominal) {
        document.getElementById('paidAmount').value = nominal;
    }

    function setUangPas() {
        document.getElementById('paidAmount').value = totalAmount;
    }

    document.getElementById('searchInput').addEventListener('input', function(e) {
        let term = e.target.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(el => {
            let name = el.getAttribute('data-name');
            el.style.display = name.includes(term) ? 'block' : 'none';
        });
    });
</script>
@endpush