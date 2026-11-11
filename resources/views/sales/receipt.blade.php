<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $sale->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body style="background:#f1f5f9">

<div class="no-print text-center my-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer"></i> Cetak Struk
    </button>
    <a href="{{ route('sales.index') }}" class="btn btn-light">Kembali ke Riwayat</a>
</div>

<div class="receipt-page">
    <div class="r-center fw-bold" style="font-size:14px">
        {{ \App\Models\Setting::get('store_name', 'Toko') }}
    </div>
    <div class="r-center">{{ \App\Models\Setting::get('store_address', '') }}</div>
    <div class="r-center">{{ \App\Models\Setting::get('store_phone', '') }}</div>

    <hr>

    <div>No: {{ $sale->invoice_number }}</div>
    <div>{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
    <div>Kasir: {{ $sale->user->name }}</div>
    <div>Pelanggan: {{ $sale->customer->name ?? 'Umum' }}</div>

    <hr>

    @foreach($sale->items as $it)
        <div>{{ $it->product_name }}</div>
        <div style="display:flex;justify-content:space-between">
            <span>{{ $it->quantity }} x {{ number_format($it->price, 0, ',', '.') }}</span>
            <span>{{ number_format($it->subtotal, 0, ',', '.') }}</span>
        </div>
    @endforeach

    <hr>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-end">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="text-end">{{ number_format($sale->discount, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight:bold">
            <td>Total</td>
            <td class="text-end">{{ number_format($sale->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ strtoupper($sale->payment_method) }})</td>
            <td class="text-end">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-end">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr>

    <div class="r-center">{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih') }}</div>

    @if($sale->status === 'void')
        <hr>
        <div class="r-center fw-bold" style="color:#dc2626">*** TRANSAKSI VOID ***</div>
        <div class="r-center small">{{ $sale->note }}</div>
    @endif
</div>

</body>
</html>