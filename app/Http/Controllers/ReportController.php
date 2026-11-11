<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->toDateString();

        $base = Sale::where('status', 'paid')->whereBetween(DB::raw('DATE(sale_date)'), [$start, $end]);

        $summary = [
            'omzet' => (clone $base)->sum('total'),
            'count' => (clone $base)->count(),
            'avg'   => (clone $base)->avg('total') ?? 0,
            'discount' => (clone $base)->sum('discount'),
        ];

        $perDay = Sale::where('status', 'paid')
            ->whereBetween(DB::raw('DATE(sale_date)'), [$start, $end])
            ->selectRaw('DATE(sale_date) as tgl, COUNT(*) as trx, SUM(total) as omzet')
            ->groupBy('tgl')->orderBy('tgl')->get();

        // PERBAIKAN: Pakai sale_items.subtotal biar gak ambigu
        $topProducts = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'paid')
            ->whereBetween(DB::raw('DATE(sales.sale_date)'), [$start, $end])
            ->selectRaw('sale_items.product_name, SUM(sale_items.quantity) as qty, SUM(sale_items.subtotal) as omzet')
            ->groupBy('sale_items.product_name')->orderByDesc('qty')->limit(10)->get();

        $profit = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'paid')
            ->whereBetween(DB::raw('DATE(sales.sale_date)'), [$start, $end])
            ->selectRaw('SUM((sale_items.price - sale_items.purchase_price) * sale_items.quantity) as laba')
            ->value('laba') ?? 0;

        $lowStock = Product::lowStock()->active()->orderBy('stock')->get();

        return view('reports.index', compact('start', 'end', 'summary', 'perDay', 'topProducts', 'profit', 'lowStock'));
    }

    public function export(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->toDateString();

        return Excel::download(new SalesExport($start, $end), 'laporan-penjualan-' . $start . '-sd-' . $end . '.xlsx');
    }
}