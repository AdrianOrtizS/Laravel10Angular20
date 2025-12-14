<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\PointsOfSale;
use App\Models\Product;

class ReportsService
{
 //    public function salesMonthly()
	// {
	//     $user = auth()->user();
	//     $pointOfSale = $user->pointsOfSale()->first();

	//     if (!$pointOfSale) {
	//         return response()->json([
	//             'error' => 'El usuario no tiene puntos de venta asignados'
	//         ], 403);
	//     }

	//     $id_branch = $pointOfSale->id_branch;

	//     if (!$id_branch) {
	//         return response()->json([
	//             'error' => 'No se pudo determinar la sucursal del usuario'
	//         ], 403);
	//     }

	//     // Filtro general para todas las consultas
 //                    // belongsTo
	//     $query = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	//         $q->where('id_branch', $id_branch);
	//     });

	//     // Ventas mensuales del año actual
	    
	// 	// Año actual
	// 	$monthly = $query
	// 			    ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
	// 			    ->whereYear('created_at', date('Y'))
	// 			    ->groupBy('month')
	// 			    ->orderBy('month')
	// 			    ->pluck('total', 'month');

	// 	// Año anterior
	// 	$monthly_last = $query
	// 			    ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
	// 			    ->whereYear('created_at', date('Y') - 1)
	// 			    ->groupBy('month')
	// 			    ->orderBy('month')
	// 			    ->pluck('total', 'month');



	//     $result = [];
	//     $result_last = [];

	//     $month_current = date('n');

	//     for ($month = 1; $month <= $month_current; $month++) {
	//         $result[] 	= 		isset($monthly[$month]) 	? 	(float) $monthly[$month] : 0.0;
	//         $result_last[] = 	isset($monthly_last[$month]) ? (float) $monthly_last[$month] : 0.0;
	//     }

	//     // ➤ Total año actual (filtrado por branch)
	//     $totalCurrentYear = (float) Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	//             $q->where('id_branch', $id_branch);
	//         })
	//         ->whereYear('created_at', date('Y'))
	//         ->sum('total');

	//     // ➤ Total año anterior (filtrado por branch)
	//     $totalLastYear = (float) Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	//             $q->where('id_branch', $id_branch);
	//         })
	//         ->whereYear('created_at', date('Y') - 1)
	//         ->sum('total');

	//     // ➤ Porcentaje de diferencia
	//     if ($totalLastYear > 0) {
	//         $percentDifference = (($totalCurrentYear - $totalLastYear) / $totalLastYear) * 100;
	//     } else {
	//         $percentDifference = 100;
	//     }

	//     return [
	//         'month_current'       => $month_current,
	//         'monthly'             => $result,
	//         'monthly_last'        => $result_last,
	//         'total_current_year'  => round($totalCurrentYear, 2),
	//         'total_last_year'     => round($totalLastYear, 2),
	//         'percent_difference'  => round($percentDifference, 2)
	//     ];
	// }

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

	    // Filtro general
	    $baseQuery = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	        $q->where('id_branch', $id_branch);
	    });

	    // ░░░ Año actual
	    $monthly = (clone $baseQuery)
	        ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
	        ->whereYear('created_at', date('Y'))
	        ->groupBy('month')
	        ->orderBy('month')
	        ->pluck('total', 'month');

	    // ░░░ Año anterior
	    $monthly_last = (clone $baseQuery)
	        ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
	        ->whereYear('created_at', date('Y') - 1)
	        ->groupBy('month')
	        ->orderBy('month')
	        ->pluck('total', 'month');

	    // Armar arrays del 1 al mes actual
	    $result = [];
	    $result_last = [];
        $current_year = date('Y');
	    $month_current = date('n');

	    for ($month = 1; $month <= $month_current; $month++) {
	        $result[] = isset($monthly[$month]) ? (float) $monthly[$month] : 0.0;
	        $result_last[] = isset($monthly_last[$month]) ? (float) $monthly_last[$month] : 0.0;
	    }

	    // ░░░ Totales anuales
	    $totalCurrentYear = (float) (clone $baseQuery)
	        ->whereYear('created_at', date('Y'))
	        ->sum('total');

	    $totalLastYear = (float) (clone $baseQuery)
	        ->whereYear('created_at', date('Y') - 1)
	        ->sum('total');

	    // ░░░ Porcentaje
	    $percentDifference = $totalLastYear > 0
	        ? (($totalCurrentYear - $totalLastYear) / $totalLastYear) * 100
	        : 100;

	    return [
	        'month_current'       => $month_current,
	        'monthly'             => $result,
	        'monthly_last'        => $result_last,
	        'total_current_year'  => round($totalCurrentYear, 2),
	        'total_last_year'     => round($totalLastYear, 2),
	        'percent_difference'  => round($percentDifference, 2),
            'current_year'        => $current_year  
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



	public function top10Products()
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
        $oneYearOld = now()->subYear();
	    
        // Ventas filtradas por sucursal con detalles y productos
	    $sales = Sale::whereHas('point_of_sale', function($q) use ($id_branch) {
	            $q->where('id_branch', $id_branch);
	        })
	        ->with(['details.product'])
	        ->where('created_at', '>=', $oneYearOld)
            ->get();

	    // Agrupar productos y sumar cantidades
	    $products = [];
        $total_products = 0;
       
	    foreach ($sales as $sale) {
	        foreach ($sale->details as $detail) {

                //id del producto
	            $id = $detail->product->id;

                    //isset -> true (si es declarada y no es null)
                    //si el producto aun no esta agregado
                    //agrega id, nombre, cantidad 0
                if (!isset($products[$id])) {
	                $products[$id] = [
	                    'product_id'   => $id,
	                    'product_name' => $detail->product->name,
	                    'quantity'     => 0
	                ];
	            }
                // print_r($products);

	            $products[$id]['quantity'] += $detail->quantity;
                $total_products = $total_products + $detail->quantity;
	        }
	    }

        // error_log($products);

	    // Ordenar de mayor a menor cantidad
	    usort($products, function($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });

	    // Obtener solo los primeros 5
	    $top5 = array_slice($products, 0, 5);

	    return [
	        'top_10_products' => $top5,
            'total_products' => $total_products
	    ];
	}

    public function lowStock()
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

		$query = Product::select('products.name',
								//COALESCE(si es null, devuelve 0)
					        \DB::raw('cat.name as categorie'),
					        \DB::raw('COALESCE(inv.stock, 0) as stock'),
					        \DB::raw('COALESCE(inv.stock_min, 0) as stock_min')
		    )
		    ->leftJoin('inventories as inv', function ($join) use ($id_branch) {
		        $join->on('products.id', '=', 'inv.id_product')
		             ->where('inv.id_branch', $id_branch);
		    })
		    ->join('categories as cat', 'products.id_categorie', '=', 'cat.id')
		    	      // COALESCE(si es null, devuelve 0)
		    ->whereRaw('COALESCE(inv.stock, 0) <= COALESCE(inv.stock_min, 0)')
		    // ->orderBy('inv.stock', 'desc')
		    ->take(20)
		    ->get();

		// $low5 = array_slice($query, 0, 5);

        return [
            'low_stock_20' => $query
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

}