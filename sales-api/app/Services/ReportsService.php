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
                    // belongsTo
	    $query = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	        $q->where('id_branch', $id_branch);
	    });

	    // Ventas mensuales del año actual
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
	        'total_current_year'  => round($totalCurrentYear, 2),
	        'total_last_year'     => round($totalLastYear, 2),
	        'percent_difference'  => round($percentDifference, 2)
	    ];
	}


	public function sales10Daily()
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

	    // Filtrar por branch (sucursal)
	    $query = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	        $q->where('id_branch', $id_branch);
	    });

	    // Obtener ventas de los últimos 10 días
	    $last_10days = $query
	        ->selectRaw('DATE(created_at) as date, SUM(total) as total')
	        ->where('created_at', '>=', Carbon::now()->subDays(10))
	        ->groupBy('date')
	        ->orderBy('date')
	        ->pluck('total', 'date');

	    $total_last_10days = (float) Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	            $q->where('id_branch', $id_branch);
	        })
	        ->where('created_at', '>=', Carbon::now()->subDays(10))
	        ->sum('total');

	    // Convertir objeto => array de { date, value }
	    $formatted = [];

	    foreach ($last_10days as $date => $value) {
	        $formatted[] = [
	            'date'  => $date,
	            'total' => (float) $value,
	        ];
	    }

	    return [
	        'last_10days' => $formatted,
	        'total_last_10days' => round($total_last_10days,2)
	    ];
	}


  //   public function topProducts()
  //   {
	 //    $user = auth()->user();
	 //    $pointOfSale = $user->pointsOfSale()->first();

	 //    if (!$pointOfSale) {
	 //        return response()->json([
	 //            'error' => 'El usuario no tiene puntos de venta asignados'
	 //        ], 403);
	 //    }

	 //    $id_branch = $pointOfSale->id_branch;

	 //    if (!$id_branch) {
	 //        return response()->json([
	 //            'error' => 'No se pudo determinar la sucursal del usuario'
	 //        ], 403);
	 //    }

	 //    // Filtrar por branch (sucursal)
	 //    $query = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	 //        $q->where('id_branch', $id_branch);
	 //    })->with(['details.product'])->get();


		// $products = [];

		// foreach ($query as $sale) {

		//     foreach ($sale->details as $detail) {

		//         $products[] = [
		//             'product_id'   => $detail->product->id ?? null,
		//             'product_name' => $detail->product->name ?? null,
		//             'quantity'     => $detail->quantity
		//         ];
		//     }
		// }		


	 //    return [
	 //        'products' => $products
	 //    ];
  //   }
	public function topProducts()
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

	    // Ventas filtradas por sucursal con detalles y productos
	    $sales = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	            $q->where('id_branch', $id_branch);
	        })
	        ->with(['details.product'])
	        ->get();

	    // Agrupar productos y sumar cantidades
	    $products = [];

	    foreach ($sales as $sale) {
	        foreach ($sale->details as $detail) {

	            $id = $detail->product->id;

	            if (!isset($products[$id])) {
	                $products[$id] = [
	                    'product_id'   => $id,
	                    'product_name' => $detail->product->name,
	                    'quantity'     => 0
	                ];
	            }

	            $products[$id]['quantity'] += $detail->quantity;
	        }
	    }

	    // Ordenar de mayor a menor cantidad
	    usort($products, function($a, $b){
	        return $b['quantity'] <=> $a['quantity'];
	    });

	    // Obtener solo los primeros 5
	    $top5 = array_slice($products, 0, 5);

	    return [
	        'top_5_products' => $top5
	    ];
	}


    public function purchaseslast_10days()
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
