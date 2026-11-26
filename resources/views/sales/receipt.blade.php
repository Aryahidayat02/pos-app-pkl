<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $sale->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #edf0f4;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
        }

        .receipt-toolbar {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 18px 12px 0;
        }

        .receipt-paper {
            width: 80mm;
            max-width: calc(100vw - 24px);
            margin: 18px auto 36px;
            padding: 7mm 5mm 8mm;
            background: #fff;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .12);
            color: #111;
            font-family: "Courier New", Courier, monospace;
            font-size: 11px;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .receipt-store {
            margin-bottom: 12px;
            text-align: center;
        }

        .receipt-logo {
            display: block;
            width: 42px;
            height: 42px;
            margin: 0 auto 7px;
            object-fit: contain;
        }

        .receipt-store-name {
            margin: 0 0 4px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .receipt-store-detail {
            color: #333;
            font-size: 10px;
        }

        .receipt-divider {
            margin: 9px 0;
            border: 0;
            border-top: 1px dashed #555;
            opacity: 1;
        }

        .receipt-meta-row,
        .receipt-total-row,
        .receipt-item-pricing {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .receipt-meta-row span:first-child,
        .receipt-total-row span:first-child {
            flex: 0 0 auto;
        }

        .receipt-meta-row span:last-child,
        .receipt-total-row span:last-child {
            text-align: right;
        }

        .receipt-meta-row + .receipt-meta-row,
        .receipt-total-row + .receipt-total-row {
            margin-top: 2px;
        }

        .receipt-items-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .receipt-item {
            padding: 5px 0;
        }

        .receipt-item-name {
            margin-bottom: 2px;
            font-weight: 700;
        }

        .receipt-item-pricing {
            color: #333;
            font-size: 10px;
        }

        .receipt-total {
            margin-top: 6px;
            padding: 6px 0;
            border-top: 1px solid #111;
            border-bottom: 3px double #111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            font-weight: 800;
        }

        .receipt-footer {
            margin-top: 12px;
            text-align: center;
        }

        .receipt-thanks {
            margin-bottom: 4px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .receipt-void {
            margin: 10px 0;
            padding: 5px;
            border: 2px solid #111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .12em;
            text-align: center;
        }

        @media (max-width: 380px) {
            .receipt-paper {
                padding: 6mm 4mm;
            }
        }

        @media print {
            html,
            body {
                width: 80mm;
                margin: 0;
                background: #fff;
            }

            .receipt-toolbar {
                display: none !important;
            }

            .receipt-paper {
                width: 80mm;
                max-width: none;
                margin: 0;
                padding: 5mm 4mm;
                box-shadow: none;
                font-size: 10px;
            }

            .receipt-divider {
                break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-toolbar no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> Cetak Struk
        </button>
        <a href="{{ route('sales.index') }}" class="btn btn-light">Kembali ke Riwayat</a>
    </div>

    <main class="receipt-paper">
        <header class="receipt-store">
            @if(\App\Models\Setting::get('show_logo_on_receipt', '1') === '1' && $logo = \App\Models\Setting::get('store_logo'))
                <img class="receipt-logo" src="{{ asset('storage/'.$logo) }}" alt="Logo toko">
            @endif
            <h1 class="receipt-store-name">{{ \App\Models\Setting::get('store_name', 'Toko') }}</h1>
            @if($address = \App\Models\Setting::get('store_address'))
                <div class="receipt-store-detail">{{ $address }}</div>
            @endif
            @if($phone = \App\Models\Setting::get('store_phone'))
                <div class="receipt-store-detail">Telp. {{ $phone }}</div>
            @endif
        </header>

        <hr class="receipt-divider">

        <section aria-label="Informasi transaksi">
            <div class="receipt-meta-row">
                <span>No. Struk</span>
                <span>{{ $sale->invoice_number }}</span>
            </div>
            <div class="receipt-meta-row">
                <span>Tanggal</span>
                <span>{{ $sale->sale_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="receipt-meta-row">
                <span>Kasir</span>
                <span>{{ $sale->user->name }}</span>
            </div>
            <div class="receipt-meta-row">
                <span>Pelanggan</span>
                <span>{{ $sale->customer->name ?? 'Umum' }}</span>
            </div>
        </section>

        <hr class="receipt-divider">

        <section aria-label="Rincian barang">
            <div class="receipt-items-label">
                <span>Barang</span>
                <span>Jumlah</span>
            </div>
            @foreach($sale->items as $item)
                <div class="receipt-item">
                    <div class="receipt-item-name">{{ $item->product_name }}</div>
                    <div class="receipt-item-pricing">
                        <span>{{ $item->quantity }} x {{ rupiah($item->price) }}</span>
                        <span>{{ rupiah($item->subtotal - $item->discount) }}</span>
                    </div>
                    @if($item->discount > 0)
                        <div class="receipt-item-pricing text-danger">
                            <span>Diskon {{ $item->discount_type === 'percent' ? $item->discount_value.'%' : 'nominal' }}</span>
                            <span>-{{ rupiah($item->discount) }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </section>

        <hr class="receipt-divider">

        <section aria-label="Ringkasan pembayaran">
            <div class="receipt-total-row">
                <span>Subtotal</span>
                <span>{{ rupiah($sale->subtotal) }}</span>
            </div>
            <div class="receipt-total-row">
                <span>Diskon</span>
                <span>{{ rupiah($sale->discount) }}</span>
            </div>
            <div class="receipt-total-row receipt-total">
                <span>TOTAL</span>
                <span>{{ rupiah($sale->total) }}</span>
            </div>
            <div class="receipt-total-row">
                <span>Metode bayar</span>
                <span>{{ strtoupper($sale->payment_method) }}</span>
            </div>
            <div class="receipt-total-row">
                <span>Dibayar</span>
                <span>{{ rupiah($sale->paid_amount) }}</span>
            </div>
            @if($sale->change_amount > 0)
                <div class="receipt-total-row">
                    <span>Kembalian</span>
                    <span>{{ rupiah($sale->change_amount) }}</span>
                </div>
            @endif
        </section>

        @if($sale->note)
            <div class="receipt-meta-row mt-2">
                <span>Catatan</span>
                <span>{{ $sale->note }}</span>
            </div>
        @endif

        @if($sale->status === 'void')
            <div class="receipt-void">TRANSAKSI VOID</div>
            @if($sale->void_reason)
                <div>Alasan: {{ $sale->void_reason }}</div>
            @endif
        @endif

        <footer class="receipt-footer">
            <div class="receipt-thanks">{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih') }}</div>
            <div>Simpan struk ini sebagai bukti transaksi.</div>
        </footer>
    </main>
</body>
</html>
