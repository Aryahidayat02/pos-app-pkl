<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
        }
        .receipt-container {
            background: #fff;
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .receipt-header h4 {
            margin: 0;
            font-weight: 800;
            font-size: 1.2rem;
        }
        .receipt-header p {
            margin: 2px 0;
            font-size: 0.8rem;
            color: #444;
        }
        .receipt-info {
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        .receipt-table {
            width: 100%;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        .receipt-table th {
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            text-align: left;
        }
        .receipt-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .receipt-footer {
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 0.8rem;
        }
        .receipt-footer .row-data {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .receipt-footer .total-row {
            font-weight: 800;
            font-size: 1rem;
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 8px 0;
        }
        .thank-you {
            text-align: center;
            font-size: 0.75rem;
            margin-top: 20px;
            color: #555;
        }
        @media print {
            body { background: none; padding: 0; margin: 0; }
            .receipt-container { box-shadow: none; max-width: 100%; padding: 0; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        
        <div class="d-flex justify-content-between mb-3 no-print">
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                <i class="bi bi-printer"></i> Cetak Struk
            </button>
        </div>

        <!-- HEADER STRUK DINAMIS DARI DATABASE -->
        <div class="receipt-header">
            <h4>{{ \App\Models\Setting::get('store_name', 'POS APP') }}</h4>
            <p>{{ \App\Models\Setting::get('store_address', 'Jl. Contoh Alamat No. 123') }}</p>
            <p>Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}</p>
        </div>

        <div class="receipt-info">
            <div>No: {{ $sale->invoice_number }}</div>
            <div>Tgl: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</div>
            <div>Kasir: {{ $sale->user->name ?? '-' }}</div>
            <div>Plgn: {{ $sale->customer->name ?? 'Umum' }}</div>
        </div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}<br>
                            <small style="color: #666;">@ Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-footer">
            <div class="row-data">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="row-data">
                <span>Diskon:</span>
                <span>Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
            </div>
            
            <div class="row-data total-row">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>

            <div class="row-data">
                <span>Bayar ({{ strtoupper($sale->payment_method) }}):</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="row-data">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="thank-you">
            <p>{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih telah berbelanja!') }}</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar</p>
        </div>

    </div>

</body>
</html>