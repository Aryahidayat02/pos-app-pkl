<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $query = Sale::where('status', 'paid');
        if ($user->role === 'kasir') {
            $query->where('user_id', $user->id);
        }

        $todaySalesTotal = (clone $query)
            ->whereRaw('DATE(sale_date) = ?', [$today])
            ->sum('total');

        $todaySalesCount = (clone $query)
            ->whereRaw('DATE(sale_date) = ?', [$today])
            ->count();

        $activeProductsCount = Product::where('is_active', 1)->count();
        $lowStockCount = Product::whereRaw('stock <= min_stock')->count();

        // REVISI: Ambil daftar produk stok menipis buat notifikasi
        $lowStockProducts = Product::whereRaw('stock <= min_stock')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        $recentSales = (clone $query)->latest('sale_date')->take(10)->get();

        return view('dashboard', compact(
            'todaySalesTotal',
            'todaySalesCount',
            'lowStockCount',
            'activeProductsCount',
            'recentSales',
            'lowStockProducts' // <--- Kirim ke view
        ));
    }
}
