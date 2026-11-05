<?php

namespace App\Support;

use App\Models\Purchase;
use App\Models\Sale;

class DocumentNumber
{
    public static function purchase(): string
    {
        // Format: PB-YYYYMMDD-XXXX
        $date = now()->format('Ymd');
        $count = Purchase::whereDate('created_at', today())->count() + 1;
        
        return sprintf('PB-%s-%04d', $date, $count);
    }

    public static function sale(): string
    {
        // Format: INV-YYYYMMDD-XXXX
        $date = now()->format('Ymd');
        $count = Sale::whereDate('created_at', today())->count() + 1;
        
        return sprintf('INV-%s-%04d', $date, $count);
    }
}