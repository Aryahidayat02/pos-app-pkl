<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        $salesQuery = Sale::where('status', 'paid');
        if ($user->role === 'kasir') {
            $salesQuery->where('user_id', $user->id);
        }

        $todaySales = (clone $salesQuery)->whereDate('sale_date', today())->sum('total');
        $todayCount = (clone $salesQuery)->whereDate('sale_date', today())->count();
        $lowStock = Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->count();
        $activeProducts = Product::where('is_active', true)->count();
        $recentSales = (clone $salesQuery)->with('items')->latest()->limit(5)->get();

        // Ini akan memanggil file resources/views/dashboard.blade.php
        return view('dashboard', compact(
            'todaySales', 'todayCount', 'lowStock', 'activeProducts', 'recentSales'
        ));
    }
}