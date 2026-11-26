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
        $dates = validator([
            'start_date' => $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d')),
            'end_date' => $request->input('end_date', Carbon::now()->format('Y-m-d')),
        ], [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ])->validate();

        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

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
            ->select('product_name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(subtotal - discount) as omzet'))
            ->groupBy('product_name')
            ->orderBy('qty', 'desc')
            ->limit(10)
            ->get();

        $labaKotor = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->where('status', 'paid')
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })
            ->select(DB::raw('SUM((price - purchase_price) * quantity - discount) as laba'))
            ->value('laba') ?? 0;

        $stokMenipis = Product::lowStock()->get();

        $dailyCandleBounds = Sale::query()
            ->where('status', 'paid')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->selectRaw('DATE(sale_date) as sale_day')
            ->selectRaw('MIN(id) as first_sale_id, MAX(id) as last_sale_id, MIN(total) as low, MAX(total) as high')
            ->groupByRaw('DATE(sale_date)')
            ->orderBy('sale_day')
            ->get();
        $endpointSaleIds = $dailyCandleBounds
            ->flatMap(fn ($day): array => [$day->first_sale_id, $day->last_sale_id])
            ->unique()
            ->values();
        $endpointSaleTotals = Sale::query()->whereKey($endpointSaleIds)->pluck('total', 'id');
        $candleChart = $dailyCandleBounds->map(fn ($day): array => [
            'date' => $day->sale_day,
            'open' => (float) $endpointSaleTotals[$day->first_sale_id],
            'high' => (float) $day->high,
            'low' => (float) $day->low,
            'close' => (float) $endpointSaleTotals[$day->last_sale_id],
        ])->all();

        $chartData = [
            'line' => $penjualanHarian->map(fn ($day): array => [
                'date' => $day->tgl,
                'value' => (float) $day->omzet,
            ])->all(),
            'candles' => $candleChart,
        ];

        return view('reports.index', compact(
            'omzet', 'transaksi', 'rataRata', 'diskon',
            'penjualanHarian', 'produkTerlaris', 'labaKotor', 'stokMenipis',
            'startDate', 'endDate', 'chartData'
        ));
    }

    public function export(Request $request)
    {
        return Excel::download(new SalesExport($request->start_date, $request->end_date), 'laporan_penjualan.xlsx');
    }
}
