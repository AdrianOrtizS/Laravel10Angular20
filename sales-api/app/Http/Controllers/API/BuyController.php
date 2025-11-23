<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Buy;
use App\Models\BuyDetail;
use App\Models\Configuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Http\Resources\BuyResource;
use App\Http\Resources\BuyCollection;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Inventory;
use DOMDocument;
use Illuminate\Support\Facades\DB;
use Log;


class BuyController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $user = auth()->user();
        
        // Obtener primer punto de venta del usuario
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

        $search    = $request->search;
        $pageSize  = $request->pageSize ?? 10;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $type_pay   = $request->type_pay;

        // Consulta base
        $query = Buy::where('id_point_of_sale', $pointOfSale->id);

        if ($search) {
            $query->FilterBuy($search);
        }

        if ($fecha_ini && $fecha_fin) {
            try {
                $inicio = \Carbon\Carbon::parse($fecha_ini)->startOfDay();
                $fin    = \Carbon\Carbon::parse($fecha_fin)->endOfDay();

                $query->whereBetween('created_at', [$inicio, $fin]);
            } catch (\Exception $e) {
                \Log::error('Error al parsear fechas: ' . $e->getMessage());
            }
        }

        if($type_pay)
        {
            $query->where('type_pay', (string)$type_pay);
        }   

        // Orden y paginación
        $buys = $query->orderBy('created_at', 'desc')
                      ->paginate($pageSize);

        return response()->json([
            'code'  => 200,
            'total' => $buys->total(),
            'Buys'  => BuyCollection::make($buys),
        ]);
    }

    public function getSuppliers(Request $request)
    {
        $searchSupplier   = $request->searchSupplier;
        if($searchSupplier){
            $suppliers = Supplier::where('name', 'like', '%'.$searchSupplier.'%')
                            ->orWhere('num_identificador', 'like', '%'.$searchSupplier.'%')
                            ->orderBy('name', 'asc')
                            ->take(10)
                            ->get();            
        }else{
            $suppliers = Supplier::orderBy('name', 'asc')
                                ->take(10)
                                ->get();
        }

        return response()->json(
            [   'code'  => 200,
                'suppliers' => SupplierCollection::make($suppliers)
            ]);
    }

    public function getProducts(Request $request)
    {
        $user = auth()->user();
        
        // Obtener primer punto de venta del usuario
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


        $query = Product::select('products.*',
                            //COALESCE  ->  si es null return 0  
                    \DB::raw('COALESCE(inventories.stock, 0) as stock'),
                    \DB::raw('COALESCE(inventories.stock_min, 0) as stock_min'))
                ->leftJoin('inventories', function($join) use ($id_branch) {
                    $join->on('products.id', '=', 'inventories.id_product')
                        ->where('inventories.id_branch', $id_branch);
                });

        // Filtros
        $search = $request->searchProduct;
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.cod_pro', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('products.name')
                            ->take(10)
                            ->get();

        return response()->json([
            'code' => 200,
            'Products' => ProductCollection::make($products)
        ]);

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Obtener primer punto de venta del usuario
        $pointOfSale = $user->pointsOfSale()->first();
        
        if(!$pointOfSale) {
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

        $validator = Validator::make($request->all(), [
            'id_supplier'   => 'required|exists:suppliers,id',
            'fecha_ingreso' => 'required',
            'items'         => 'required|array|min:1',
            'items.*.id_product' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        try{
            DB::beginTransaction();

            // Crear la compra
            $buy = Buy::create([
                'id_supplier'       => $request->id_supplier,
                'id_point_of_sale'  => $pointOfSale->id,
                'fecha_ingreso'     => $request->fecha_ingreso,
                'numero_factura'    => $request->numero_factura,
                'type_pay'      => $request->type_pay,
                'type_doc'      => $request->type_doc,
                'iva'           => $request->iva,
                'subtotal'      => $request->subtotal,
                'total'         => $request->total,
            ]);

            // Agregar detalles del pedido
            foreach ($request->items as $item) {
                $product = Product::find($item['id_product']);

                BuyDetail::create([
                    'id_buy'   => $buy->id,
                    'id_product'=> $item['id_product'],
                    'cod_pro'   => $item['cod_pro'],
                    'quantity'  => $item['quantity'],
                    'price'     => $item['price'],
                    'subtotal'  => $item['subtotal'],
                ]);

                $this->updateStockBuy( $item['id_product'], 
                                        $id_branch, 
                                        $item['quantity']);
            }

            DB::commit();
            
            return response()->json([ 'code' => 200,
                                      'buy' => $buy ]);

        }catch (\Exception $e) {
            DB::rollBack();
            return [
                'code'  => 405,
                'status' => 'error',
                'message' => 'Error en el proceso',
                'errors' => $e->getMessage()
            ];
        }
    }

    
    public function updateStockBuy($id_product, $id_branch, $quantity)
    {
        $inventory = Inventory::where('id_product', $id_product)
                                ->where('id_branch', $id_branch)
                                ->first();
                
        $inventory->update(['stock' => $inventory->stock + $quantity]);
        return $inventory->fresh();
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $buy = Buy::with(['supplier', 'details.product'])->find($id);

        if (!$buy) {
            return response()->json(['message' => 'No existe la venta solicitada.'], 404);
        }

        return response()->json(BuyResource::make($buy));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $buy = Buy::with('details.product')->find($id);

        if ($buy->pays && $buy->pays->count() > 0) {
            return response()->json([
                'code' => 400,
                'message' => 'No se puede eliminar, tiene pagos asociados'
            ], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($buy->details as $item) {
                // Buscar el registro de inventario del producto
                $inventory = Inventory::where('id_product', $item->product->id)
                                        ->first();

                if ($inventory) {
                    // Devolver el stock eliminado por esta compra
                    $inventory->stock -= $item->quantity;
                    $inventory->save();
                }
            }

            // Eliminar detalles e historial de compra
            $buy->details()->delete();

            // Eliminar la compra principal
            $buy->delete();
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Buy deleted and stock updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => 'Error deleting buy: ' . $e->getMessage()
            ], 500);
        }
    }

}
