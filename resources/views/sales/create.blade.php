@extends('layouts.app')
@section('title', 'Kasir')
@section('content')
    <div class="kasir-grid">
        <!-- KIRI: Daftar Produk -->
        <div class="kasir-products">
            <input type="text" id="searchProduct" class="form-control mb-3" placeholder="Cari nama produk atau scan barcode (SKU)..." onkeyup="filterProducts()">
            
            <div class="row g-3" id="productList">
                @foreach($products as $product)
                    <div class="col-md-4 col-lg-3 product-item" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}">
                        <div class="product-tile h-100 p-2" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->stock }})">
                            
                            <!-- FOTO PRODUK GEDE -->
                            <div class="mb-2 text-center" style="height: 100px; overflow: hidden; border-radius: 8px; background: #f1f5f9;">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $product->name }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="p-name text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
                            <div class="p-price">@rupiah($product->selling_price)</div>
                            <div class="p-stock mt-1">Stok: {{ $product->stock }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KANAN: Keranjang -->
        <div class="kasir-cart">
            <form method="POST" action="{{ route('sales.store') }}" id="saleForm" class="d-flex flex-column h-100">
                @csrf
                <div class="p-3 bg-white border-bottom">
                    <label class="form-label mb-1">Pelanggan</label>
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">Pelanggan Umum</option>
                        @foreach($customers as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>

                <div class="cart-items" id="cartContainer">
                    <div class="text-center text-muted py-5" id="cartEmpty">
                        <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Keranjang kosong</p>
                    </div>
                </div>

                <div class="cart-totals">
                    <div class="cart-total-row">
                        <span>Subtotal</span>
                        <span id="subtotalText">Rp 0</span>
                    </div>
                    <div class="cart-total-row align-items-center">
                        <span>Diskon (Rp)</span>
                        <input type="number" name="discount" id="discountInput" class="form-control form-control-sm text-end" style="width: 120px;" value="0" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="cart-total-row grand">
                        <span>Total</span>
                        <span id="totalText">Rp 0</span>
                    </div>

                    <hr class="my-2">
                    <div class="mb-2">
                        <select name="payment_method" id="paymentMethod" class="form-select form-select-sm" onchange="togglePayment()">
                            <option value="tunai">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="cart-total-row">
                        <span>Uang Diterima</span>
                        <input type="number" name="paid_amount" id="paidInput" class="form-control form-control-sm text-end" style="width: 120px;" value="0" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="cart-total-row">
                        <span>Kembalian</span>
                        <span id="changeText" class="fw-bold">Rp 0</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3 py-2" id="submitBtn" disabled>
                        <i class="bi bi-check-circle me-1"></i>Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let cart = [];

    // Cari Produk
    function filterProducts() {
        const query = document.getElementById('searchProduct').value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(el => {
            const name = el.dataset.name;
            const sku = el.dataset.sku;
            el.style.display = (name.includes(query) || sku.includes(query)) ? 'block' : 'none';
        });
    }

    // Tambah ke Keranjang
    function addToCart(id, name, price, stock) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty < stock) existing.qty++;
            else alert('Stok tidak mencukupi!');
        } else {
            cart.push({ id, name, price, stock, qty: 1 });
        }
        renderCart();
    }

    // Render Tampilan Keranjang
    function renderCart() {
        const container = document.getElementById('cartContainer');
        const emptyState = document.getElementById('cartEmpty');
        
        if (cart.length === 0) {
            container.innerHTML = '';
            container.appendChild(emptyState);
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
            let html = '';
            cart.forEach((item, index) => {
                html += `
                    <div class="cart-item">
                        <div style="flex: 1;">
                            <div class="fw-medium text-truncate" style="max-width: 150px;">${item.name}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Rp ${item.price.toLocaleString('id-ID')}</div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                            <input type="number" name="items[${index}][quantity]" value="${item.qty}" min="1" max="${item.stock}" class="form-control form-control-sm text-center" style="width: 60px;" onchange="updateQty(${index}, this.value)">
                        </div>
                        <div class="text-end fw-medium" style="width: 80px;">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</div>
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-1" onclick="removeFromCart(${index})"><i class="bi bi-x"></i></button>
                    </div>
                `;
            });
            container.innerHTML = `<div id="cartEmpty" style="display:none;"></div>` + html;
        }
        calculateTotal();
    }

    function updateQty(index, val) {
        let qty = parseInt(val);
        if (isNaN(qty) || qty < 1) qty = 1;
        if (qty > cart[index].stock) {
            alert('Melebihi stok!');
            qty = cart[index].stock;
        }
        cart[index].qty = qty;
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function togglePayment() {
        const method = document.getElementById('paymentMethod').value;
        const paidInput = document.getElementById('paidInput');
        if (method !== 'tunai') {
            paidInput.value = getTotal();
            paidInput.readOnly = true;
        } else {
            paidInput.readOnly = false;
        }
        calculateTotal();
    }

    function getSubtotal() { return cart.reduce((sum, item) => sum + (item.price * item.qty), 0); }
    function getTotal() { 
        let discount = parseFloat(document.getElementById('discountInput').value) || 0;
        let subtotal = getSubtotal();
        return Math.max(0, subtotal - discount);
    }

    function calculateTotal() {
        const subtotal = getSubtotal();
        const total = getTotal();
        const paid = parseFloat(document.getElementById('paidInput').value) || 0;
        const change = paid - total;

        document.getElementById('subtotalText').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('totalText').innerText = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('changeText').innerText = 'Rp ' + (change > 0 ? change : 0).toLocaleString('id-ID');

        const btn = document.getElementById('submitBtn');
        const method = document.getElementById('paymentMethod').value;
        
        if (cart.length === 0) {
            btn.disabled = true;
        } else if (method === 'tunai' && paid < total) {
            btn.disabled = true;
        } else {
            btn.disabled = false;
        }
    }
</script>
@endpush