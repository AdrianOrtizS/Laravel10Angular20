<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Categorie;
use App\Models\Branch;
use App\Models\Tarifa_iva;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Log;

class ProductController extends Controller
{
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

        $search     = $request->search;
        $id_categorie  = $request->id_categorie;
        $pageSize      = $request->pageSize ?? 15;

        $query = Product::select('products.*',
                        //COALESCE  ->  si es null return 0  
                \DB::raw('COALESCE(inventories.stock, 0) as stock'),
                \DB::raw('COALESCE(inventories.stock_min, 0) as stock_min')
            )
            ->leftJoin('inventories', function($join) use ($id_branch) {
                $join->on('products.id', '=', 'inventories.id_product')
                     ->where('inventories.id_branch', $id_branch);
            })
            ->with(['categorie'
                // ,'tarifa_iva'
            ]);
            // ->with(['categorie','tarifa_iva' => function($q){
            //     $q->withTrashed();
            // }]);

        // Filtros
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.cod_pro', 'LIKE', "%{$search}%");
            });
        }

        if ($id_categorie) {
            $query->where('products.id_categorie', $id_categorie);
        }

        $products = $query->orderBy('products.name')->paginate($pageSize);

        return response()->json([
            'total' => $products->total(),
            'Products' => ProductCollection::make($products)
        ]);

    }

    public function getCategories(Request $request)
    {
        $categories = Categorie::select( 'categories.id',
                                         'categories.name')
                                ->orderBy('id') 
                                ->get();
        
        return response()->json(
            [   
                'code'       => 200,
                'Categories' => ($categories)
            ]);
    }
    // public function getTarifasIva(Request $request)
    // {
    //     $tarifas_iva = Tarifa_iva::select( 'tarifa_ivas.id',
    //                                        'tarifa_ivas.codigo',
    //                                        'tarifa_ivas.porcentaje'
    //                                    )
    //                             // ->where('state',1)
    //                             ->orderBy('id') 
    //                             ->get();
        
    //     return response()->json(
    //         [   
    //             'code'       => 200,
    //             'Tarifas_iva' => ($tarifas_iva)
    //         ]);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                  'cod_pro'     => 'required|unique:products,cod_pro',
                  'name'        => 'required|unique:products,name',
                  'description' => 'required',
                  'price'       => 'required',
                  'id_categorie'=> 'required|exists:categories,id',
                  'stock'       => 'required',
                  'stock_min'   => 'required',
                  'tarifa_iva'  => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $user = auth()->user();

        $pointsOfSale = $user->pointsOfSale()->first();
        $id_branch = $pointsOfSale->id_branch;


        if (!$id_branch) {
            return response()->json([
                'error' => 'Usuario no tiene sucursal asignada'
            ], 403);
        }

        if($request->hasFile('producto')){
            $path = Storage::putFile('products', $request->file('producto'));
            $request->request->add(['imagen' => $path]);
        }

        try {
            DB::beginTransaction();

            $product = Product::create($request->only([
                'cod_pro', 'name', 'description', 'price', 
                'id_categorie', 'imagen', 'tarifa_iva' 
            ]));

            // Asignar stock a la sucursal del usuario
            $stock = $request->stock ?? 0;
            $stock_min = $request->stock_min ?? 0;

            $product->branches()->attach($id_branch, ['stock' => $stock,
                                                     'stock_min' => $stock_min]);
            
            $otherBranches = Branch::where('id', '!=', $id_branch)->get();

            foreach ($otherBranches as $branch) {
                $product->branches()->attach($branch->id, [
                    'stock' => 0,
                    'stock_min' => 0
                ]);
            }


            DB::commit();
            return response()->json(['code'     => 200, 
                                     'message'  => 'Product created']);

        }catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating product: ' . $e->getMessage());
            
            return response()->json([
                'code' => 500,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         try {
            // Obtener sucursal del usuario autenticado
            $user = auth()->user();
            // $branchId = $user->branch_id ?? $user->branches()->first()->id ?? null;

            $pointsOfSale = $user->pointsOfSale()->first();
            $id_branch = $pointsOfSale->id_branch;

            $product = Product::select(
                'products.*', 
                \DB::raw('COALESCE(inventories.stock, 0) as stock'),
                \DB::raw('COALESCE(inventories.stock_min, 0) as stock_min'),
            )
            ->leftJoin('inventories', function($join) use ($id_branch) {
                $join->on('products.id', '=', 'inventories.id_product')
                    ->where('inventories.id_branch', $id_branch);
            })
            ->with(['categorie'])
            ->where('products.id', $id)
            ->first();

            if (!$product) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            return response()->json([
                'code' => 200,
                'Product' => new ProductResource($product),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error al obtener el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */              
    public function update(Request $request, string $id)
    {            

         $validator = Validator::make($request->all(), [
            'cod_pro'       => ['required', Rule::unique('products', 'cod_pro')->ignore($id)],
            'name'          => ['required', Rule::unique('products', 'name')->ignore($id)],
            'description'   => 'required',
            'price'         => 'required|numeric|min:0',
            'id_categorie'  => 'required|exists:categories,id',
            'stock'         => 'required|integer',
            'stock_min'     => 'required|integer',
            'producto'      => 'sometimes|image|max:2048',
            'tarifa_iva'    => 'required'
        ]);
        

        if ($validator->fails()) {
            return response()->json([
                'code' => 403, 
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 403);
        }
        
        $user = auth()->user();

        $pointsOfSale = $user->pointsOfSale()->first();
        $id_branch = $pointsOfSale->id_branch;

        if (!$id_branch) {
            return response()->json([
                'code' => 403,
                'message' => 'Usuario no tiene sucursal asignada'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $product = Product::find($id);
            if (!$product) {
                DB::rollBack();
                return response()->json([
                    'code' => 404,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Manejar carga de imagen
            if ($request->hasFile('producto')) {
                if ($product->imagen && Storage::exists($product->imagen)) {
                    Storage::delete($product->imagen);
                }
                $path = Storage::putFile('products', $request->file('producto'));
                $request->merge(['imagen' => $path]);
            }

            // Actualizar campos del producto
            $updateData = $request->only([
                'cod_pro', 'name', 'description', 'price', 'id_categorie', 'imagen', 'tarifa_iva'
            ]);
            
            // Agregar stock y stock_min al update del producto
            if ($request->has('stock')) {
                $updateData['stock'] = $request->stock;
            }
            
            if ($request->has('stock_min')) {
                $updateData['stock_min'] = $request->stock_min;
            }

            $product->update($updateData);

            // Actualizar información de stock en sucursal
            $branchUpdates = [];
            
            if ($request->has('stock')) {
                $branchUpdates['stock'] = $request->stock;
            }
            
            if ($request->has('stock_min')) {
                $branchUpdates['stock_min'] = $request->stock_min;
            }

            if (!empty($branchUpdates)) {
                $product->branches()->syncWithoutDetaching([
                    $id_branch => $branchUpdates
                ]);
            }

            // Recargar relaciones
            $product->load(['categorie', 'branches']);

            DB::commit();

            return response()->json([
                'code' => 200, 
                'message' => 'Producto actualizado exitosamente',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'code' => 500,
                'message' => 'Error al actualizar el producto',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::where('id', '=', $id)->first();
                
        if(!$product){
            $data = ['code'=> 404,
                     'message' => 'Product not found'];
        }else{
            if($product->state == true){
                $product->state = false;
                $message = 'Product deactivate';
            }else{
                $product->state = true;
                $message = 'Product activate';
            }
            $product->update();
            $data = ['code'    => 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

}
