@extends('layouts.app')
@section('title', 'Struk Transaksi')

@push('styles')
<style>
    .struk-container {
        max-width: 350px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: 'Courier New', Courier, monospace;
        color: #000;
    }
    .struk-header { text-align: center; margin-bottom: 10px; }
    .struk-header h4 { margin: 0; font-weight: bold; font-size: 1.1rem; }
    .struk-header p { margin: 0; font-size: 0.8rem; }
    .struk-divider { border-top: 1px dashed #000; margin: 10px 0; }
    .struk-item { display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 4px; }
    .struk-item-name { font-weight: bold; margin-bottom: 2px; }
    .struk-total { font-size: 1rem; font-weight: bold; }
    
    @media print {
        body * { visibility: hidden; }
        .struk-container, .struk-container * { visibility: visible; }
        .struk-container { position: absolute; left: 0; top: 0; border: none; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Riwayat
        </a>
        
        <div class="d-flex gap-2">
            @if(auth()->user()->role === 'admin' && $sale->status === 'paid')
            <!-- Tombol Void (Pembatalan) -->
            <form action="{{ route('sales.void', $sale) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan transaksi ini? Stok akan dikembalikan.')">
                @csrf
                <input type="hidden" name="void_reason" value="Dibatalkan oleh admin">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i>Batalkan (Void)
                </button>
            </form>
            @endif

            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i>Cetak Struk
            </button>
        </div>
    </div>

    <div class="struk-container shadow-sm">
        @if($sale->status === 'void')
            <div class="alert alert-danger text-center fw-bold mb-3" style="font-size: 1rem;">
                TRANSAKSI DIBATALKAN (VOID)
            </div>
        @endif

        <div class="struk-header">
            <h4>{{ \App\Models\Setting::get('store_name', 'POS APP') }}</h4>
            <p>{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</p>
            <p>Telp: {{ \App\Models\Setting::get('store_phone', '-') }}</p>
        </div>

        <div class="struk-divider"></div>

        <div style="font-size: 0.8rem; margin-bottom: 8px;">
            <div>No. Invoice : {{ $sale->invoice_number }}</div>
            <div>Tanggal&nbsp;&nbsp;&nbsp;&nbsp; : {{ $sale->sale_date->format('d/m/Y H:i') }}</div>
            <div>Kasir&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $sale->user->name }}</div>
            <div>Pelanggan : {{ $sale->customer->name ?? 'Pelanggan Umum' }}</div>
        </div>

        <div class="struk-divider"></div>

        @foreach($sale->items as $item)
            <div class="struk-item-name">{{ $item->product_name }}</div>
            <div class="struk-item">
                <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="struk-divider"></div>

        <div class="struk-item">
            <span>Subtotal</span>
            <span>{{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="struk-item">
            <span>Diskon</span>
            <span>-{{ number_format($sale->discount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="struk-item struk-total mt-1">
            <span>TOTAL</span>
            <span>{{ number_format($sale->total, 0, ',', '.') }}</span>
        </div>

        <div class="struk-divider"></div>

        <div class="struk-item">
            <span>Bayar ({{ strtoupper($sale->payment_method) }})</span>
            <span>{{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="struk-item">
            <span>Kembalian</span>
            <span>{{ number_format($sale->change_amount, 0, ',', '.') }}</span>
        </div>

        <div class="struk-divider"></div>

        <div class="struk-header mt-3">
            <p style="font-size: 0.75rem;">{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih atas kunjungan Anda') }}</p>
        </div>
    </div>
@endsection