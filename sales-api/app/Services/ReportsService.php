<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsService
{
    public function salesMonthly()
    {
        return DB::table('sales')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function salesDaily()
    {
        return DB::table('sales')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function topProducts()
    {
        return DB::table('sale_items')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as qty'))
            ->groupBy('products.name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();
    }

    public function purchasesMonthly()
    {
        return DB::table('purchases')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function lowStock()
    {
        return DB::table('products')
            ->where('stock', '<=', 5)
            ->select('name', 'stock')
            ->orderBy('stock')
            ->get();
    }

}
