<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection; // <-- Tambahan tipe data

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    // Wajib pakai return type : Collection
    public function collection(): Collection
    {
        return Sale::with('user')
            ->where('status', 'paid')
            ->whereBetween(\DB::raw('DATE(sale_date)'), [$this->start, $this->end])
            ->orderBy('sale_date', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'Tanggal',
            'Kasir',
            'Metode Bayar',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Total (Rp)',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_number,
            $sale->sale_date->format('d/m/Y H:i'),
            $sale->user->name,
            strtoupper($sale->payment_method),
            $sale->subtotal,
            $sale->discount,
            $sale->total,
        ];
    }
}