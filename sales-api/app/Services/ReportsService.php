<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\PointsOfSale;

class ReportsService
{
    public function salesMonthly()
	{
	    $user = auth()->user();
	    $pointOfSale = $user->pointsOfSale()->first();

	    if (!$pointOfSale) {
	        return response()->json([
	            'error' => 'El usuario no tiene puntos de venta asignados'
	        ], 403);
	    }

	    $id_branch = $pointOfSale->id_branch;

	    if (!$id_branch) {
	        return response()->json([
	            'error' => 'No se pudo determinar la sucursal del usuario'
	        ], 403);
	    }

	    // Filtro general para todas las consultas
	    $query = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	        $q->where('id_branch', $id_branch);
	    });

	    // ➤ Ventas mensuales del año actual
	    $monthly = $query
	        ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
	        ->whereYear('created_at', date('Y'))
	        ->groupBy('month')
	        ->orderBy('month')
	        ->pluck('total', 'month');

	    $result = [];
	    $month_current = date('n');

	    for ($month = 1; $month <= $month_current; $month++) {
	        $result[] = isset($monthly[$month]) ? (float) $monthly[$month] : 0.0;
	    }

	    // ➤ Total año actual (filtrado por branch)
	    $totalCurrentYear = (float) Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	            $q->where('id_branch', $id_branch);
	        })
	        ->whereYear('created_at', date('Y'))
	        ->sum('total');

	    // ➤ Total año anterior (filtrado por branch)
	    $totalLastYear = (float) Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	            $q->where('id_branch', $id_branch);
	        })
	        ->whereYear('created_at', date('Y') - 1)
	        ->sum('total');

	    // ➤ Porcentaje de diferencia
	    if ($totalLastYear > 0) {
	        $percentDifference = (($totalCurrentYear - $totalLastYear) / $totalLastYear) * 100;
	    } else {
	        $percentDifference = 100;
	    }

	    return [
	        'month_current'       => $month_current,
	        'monthly'             => $result,
	        'total_current_year'  => $totalCurrentYear,
	        'total_last_year'     => $totalLastYear,
	        'percent_difference'  => round($percentDifference, 2)
	    ];
	}



    public function salesDaily()
    {   //ventas de los ultimos 10 dias
        return DB::table('sales')
            ->select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(10))
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
