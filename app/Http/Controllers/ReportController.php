<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $query = Sale::where('status', 'paid')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        $omzet = $query->sum('total');
        $transaksi = $query->count();
        $rataRata = $query->avg('total');
        $diskon = $query->sum('discount');

        $penjualanHarian = Sale::where('status', 'paid')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->select(DB::raw('DATE(sale_date) as tgl'), DB::raw('COUNT(*) as trx'), DB::raw('SUM(total) as omzet'))
            ->groupBy('tgl')
            ->orderBy('tgl', 'asc')
            ->get();

        $produkTerlaris = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->where('status', 'paid')
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })
            ->select('product_name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(subtotal) as omzet'))
            ->groupBy('product_name')
            ->orderBy('qty', 'desc')
            ->limit(10)
            ->get();

        $labaKotor = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->where('status', 'paid')
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })
            ->select(DB::raw('SUM((price - purchase_price) * quantity) as laba'))
            ->value('laba') ?? 0;

        $stokMenipis = Product::lowStock()->get();

        return view('reports.index', compact(
            'omzet', 'transaksi', 'rataRata', 'diskon',
            'penjualanHarian', 'produkTerlaris', 'labaKotor', 'stokMenipis',
            'startDate', 'endDate'
        ));
    }

    public function export(Request $request)
    {
        return Excel::download(new SalesExport($request->start_date, $request->end_date), 'laporan_penjualan.xlsx');
    }
}
