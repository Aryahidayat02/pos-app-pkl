<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request)
    {
        // Default filter: Awal bulan ini sampai hari ini
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Query dasar (hanya yang paid)
        $baseQuery = Sale::where('status', 'paid')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        // 1. Ringkasan Atas
        $summary = $baseQuery->clone()->select(
            DB::raw('COALESCE(SUM(total), 0) as total_omzet'),
            DB::raw('COUNT(*) as total_transaksi'),
            DB::raw('COALESCE(AVG(total), 0) as rata_rata'),
            DB::raw('COALESCE(SUM(discount), 0) as total_diskon')
        )->first();

        // 2. Penjualan Per Hari
        $dailySales = $baseQuery->clone()
            ->select(
                DB::raw('DATE(sale_date) as tanggal'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total) as omzet')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Produk Terlaris (Top 10)
        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_omzet')
            )
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')
                  ->whereDate('sale_date', '>=', $startDate)
                  ->whereDate('sale_date', '<=', $endDate);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // 4. Laba Kotor (Omzet - Modal)
        $profit = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')
                  ->whereDate('sale_date', '>=', $startDate)
                  ->whereDate('sale_date', '<=', $endDate);
            })
            ->select(DB::raw('SUM((price - purchase_price) * quantity) as laba_kotor'))
            ->first();

        // 5. Stok Menipis
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock', 'asc')
            ->get();

        return view('reports.index', compact(
            'startDate', 'endDate', 'summary', 'dailySales', 
            'topProducts', 'profit', 'lowStockProducts'
        ));
    }
}